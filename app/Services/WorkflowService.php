<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\StatusHistory;
use App\Models\User;

class WorkflowService
{
    private const TRANSITIONS = [
        'NEW_PROPOSAL' => ['PROCESS', 'REJECTED'],
        'PROCESS' => ['ON_REVIEW', 'REJECTED'],
        'ON_REVIEW' => ['APPROVED', 'APPROVED_WITH_REVISION', 'REJECTED'],
        'APPROVED' => ['WAITING_SIGNATURE'],
        'APPROVED_WITH_REVISION' => ['RESUBMISSION'],
        'RESUBMISSION' => ['REVISED'],
        'REVISED' => ['PROCESS'],
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

        $oldStatus = $submission->status->value;
        $submission->update(['status' => $newStatus]);

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

    private function dispatchNotifications(Submission $submission, SubmissionStatus $newStatus): void
    {
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
