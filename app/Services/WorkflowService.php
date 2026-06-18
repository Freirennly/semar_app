<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\StatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    private const TRANSITIONS = [
        'NEW_PROPOSAL' => ['PROCESS', 'REJECTED', 'RESUBMISSION'],
        'PROCESS' => ['ON_REVIEW', 'REJECTED'],
        'ON_REVIEW' => ['APPROVED', 'APPROVED_WITH_REVISION', 'REJECTED', 'PROCESS'],
        'APPROVED' => ['WAITING_SIGNATURE'],
        'APPROVED_WITH_REVISION' => ['RESUBMISSION'],
        'RESUBMISSION' => ['REVISED'],
        'REVISED' => ['PROCESS', 'RESUBMISSION'],
        'WAITING_SIGNATURE' => ['DONE'],
        'REJECTED' => [],
        'DONE' => [],
    ];

    public function canTransition(Submission $submission, SubmissionStatus $newStatus): bool
    {
        $current = $submission->status->value;
        return in_array($newStatus->value, self::TRANSITIONS[$current] ?? []);
    }

    public function getAllowedTransitions(SubmissionStatus $status): array
    {
        return self::TRANSITIONS[$status->value] ?? [];
    }

    public function transition(Submission $submission, SubmissionStatus $newStatus, User $actor, ?string $note = null): void
    {
        if (! $this->canTransition($submission, $newStatus)) {
            abort(403, "Transisi dari {$submission->status->value} ke {$newStatus->value} tidak diizinkan.");
        }

        if ($newStatus === SubmissionStatus::DONE) {
            if (! $actor->hasRole('ketua')) {
                abort(403, "Hanya Ketua yang dapat menyelesaikan pengajuan.");
            }

            if (empty($submission->verification_token)) {
                $submission->verification_token = (string) \Illuminate\Support\Str::uuid();
            }

            if (empty($submission->ec_number) ||
                empty($submission->signatory_id) ||
                empty($submission->confirmed_title) ||
                empty($submission->confirmed_researcher_name)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'ec_fields' => 'Semua kolom Ethical Clearance harus dilengkapi sebelum penandatanganan.'
                ]);
            }

            $submission->signed_at = now();
        }

        $oldStatus = $submission->status->value;

        if ($newStatus === SubmissionStatus::DONE) {
            DB::transaction(function () use ($submission, $newStatus, $oldStatus, $actor, $note) {
                $submission->status = $newStatus;
                $submission->save();

                $path = app(\App\Services\CertificateGenerator::class)->generate($submission);
                $submission->ec_certificate_path = $path;
                $submission->save();

                // DONE state invariant hardening
                if (
                    is_null($submission->status) ||
                    is_null($submission->ec_number) ||
                    is_null($submission->signatory_id) ||
                    is_null($submission->confirmed_title) ||
                    is_null($submission->confirmed_researcher_name) ||
                    is_null($submission->signed_at) ||
                    is_null($submission->ec_certificate_path) ||
                    is_null($submission->verification_token)
                ) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'invariants' => 'Semua atribut kelaikan etik (DONE) wajib terisi.'
                    ]);
                }

                // Log Status History (legacy model)
                StatusHistory::create([
                    'submission_id' => $submission->id,
                    'from_status' => $oldStatus,
                    'to_status' => $newStatus->value,
                    'changed_by' => $actor->id,
                    'note' => $note,
                    'created_at' => now(),
                ]);

                // Log Activity (new custom model)
                \App\Models\ActivityLog::create([
                    'user_id' => $actor->id,
                    'submission_id' => $submission->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus->value,
                    'description' => $note ?? "Status berubah dari {$oldStatus} menjadi {$newStatus->value}.",
                ]);
            });

            // Dispatch database notifications after commit
            $this->dispatchNotifications($submission, $newStatus);
        } else {
            $submission->status = $newStatus;
            $submission->save();

            // Log Status History (legacy model)
            StatusHistory::create([
                'submission_id' => $submission->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus->value,
                'changed_by' => $actor->id,
                'note' => $note,
                'created_at' => now(),
            ]);

            // Log Activity (new custom model)
            \App\Models\ActivityLog::create([
                'user_id' => $actor->id,
                'submission_id' => $submission->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus->value,
                'description' => $note ?? "Status berubah dari {$oldStatus} menjadi {$newStatus->value}.",
            ]);

            // Dispatch database notifications
            $this->dispatchNotifications($submission, $newStatus);
        }
    }

    private function dispatchNotifications(Submission $submission, SubmissionStatus $newStatus): void
    {
        // Safe dispatch of new production-grade queued notifications
        try {
            switch ($newStatus) {
                case SubmissionStatus::NEW_PROPOSAL:
                    $admins = User::role('admin')->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new \App\Notifications\NewProposalSubmitted($submission));
                    }
                    break;

                case SubmissionStatus::ON_REVIEW:
                    $submission->load('assignments.reviewer');
                    foreach ($submission->assignments as $assignment) {
                        $reviewer = $assignment->reviewer;
                        if ($reviewer) {
                            $reviewer->notify(new \App\Notifications\ReviewerAssigned($submission));
                        }
                    }
                    break;

                case SubmissionStatus::APPROVED:
                    if ($submission->student) {
                        $submission->student->notify(new \App\Notifications\ProposalApproved($submission));
                    }
                    break;

                case SubmissionStatus::APPROVED_WITH_REVISION:
                    if ($submission->student) {
                        $submission->student->notify(new \App\Notifications\ProposalRevisionRequested($submission));
                    }
                    break;

                case SubmissionStatus::WAITING_SIGNATURE:
                    if ($submission->signatory) {
                        $submission->signatory->notify(new \App\Notifications\EcWaitingSignature($submission));
                    }
                    break;

                case SubmissionStatus::DONE:
                    if ($submission->student) {
                        $submission->student->notify(new \App\Notifications\EcCertificateIssued($submission));
                    }
                    break;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to dispatch production-grade notifications: " . $e->getMessage());
        }

        // Keep legacy database notifications to prevent dashboard breaking
        switch ($newStatus) {
            case SubmissionStatus::NEW_PROPOSAL:
                $admins = User::role('admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\SubmissionWorkflowNotification(
                        'Proposal Baru Diajukan',
                        "Mahasiswa {$submission->student->name} telah mengajukan proposal baru: \"{$submission->title}\".",
                        $submission->id,
                        route('admin.proposals.show', $submission)
                    ));
                }
                break;

            case SubmissionStatus::PROCESS:
                $student = $submission->student;
                if ($student) {
                    $student->notify(new \App\Notifications\SubmissionWorkflowNotification(
                        'Proposal Diproses',
                        "Proposal Anda \"{$submission->title}\" sedang diproses oleh sekretariat.",
                        $submission->id,
                        route('submissions.show', $submission)
                    ));
                }
                break;

            case SubmissionStatus::ON_REVIEW:
                $submission->load('assignments.reviewer');
                foreach ($submission->assignments as $assignment) {
                    $reviewer = $assignment->reviewer;
                    if ($reviewer) {
                        $reviewer->notify(new \App\Notifications\SubmissionWorkflowNotification(
                            'Penugasan Reviewer Baru',
                            "Anda telah ditugaskan untuk meninjau proposal: \"{$submission->title}\".",
                            $submission->id,
                            route('reviews.show', $submission)
                        ));
                    }
                }
                break;

            case SubmissionStatus::APPROVED:
            case SubmissionStatus::APPROVED_WITH_REVISION:
            case SubmissionStatus::REJECTED:
                $student = $submission->student;
                if ($student) {
                    $statusLabel = $newStatus->label();
                    $student->notify(new \App\Notifications\SubmissionWorkflowNotification(
                        'Keputusan Proposal Etik',
                        "Status proposal Anda \"{$submission->title}\" telah diperbarui menjadi: {$statusLabel}.",
                        $submission->id,
                        route('submissions.show', $submission)
                    ));
                }
                break;

            case SubmissionStatus::RESUBMISSION:
                $student = $submission->student;
                if ($student) {
                    $student->notify(new \App\Notifications\SubmissionWorkflowNotification(
                        'Permintaan Revisi Proposal',
                        "Anda diminta untuk melakukan revisi pada proposal: \"{$submission->title}\".",
                        $submission->id,
                        route('submissions.show', $submission)
                    ));
                }
                break;

            case SubmissionStatus::REVISED:
                $adminsAndSecretaries = User::role(['admin', 'sekretariat'])->get();
                foreach ($adminsAndSecretaries as $user) {
                    $user->notify(new \App\Notifications\SubmissionWorkflowNotification(
                        'Revisi Proposal Dikirim',
                        "Mahasiswa {$submission->student->name} telah mengirimkan revisi untuk proposal: \"{$submission->title}\".",
                        $submission->id,
                        route('doccheck.show', $submission)
                    ));
                }
                break;

            case SubmissionStatus::WAITING_SIGNATURE:
                $chairmen = User::role('ketua')->get();
                foreach ($chairmen as $chairman) {
                    $chairman->notify(new \App\Notifications\SubmissionWorkflowNotification(
                        'Menunggu Tanda Tangan Sertifikat',
                        "Sertifikat untuk proposal \"{$submission->title}\" menunggu tanda tangan Anda.",
                        $submission->id,
                        route('submissions.show', $submission)
                    ));
                }
                break;

            case SubmissionStatus::DONE:
                $student = $submission->student;
                if ($student) {
                    $student->notify(new \App\Notifications\SubmissionWorkflowNotification(
                        'Sertifikat Laik Etik Terbit',
                        "Selamat! Sertifikat Laik Etik untuk proposal \"{$submission->title}\" telah selesai diterbitkan dan dapat diunduh.",
                        $submission->id,
                        route('submissions.show', $submission)
                    ));
                }
                break;
        }
    }
}
