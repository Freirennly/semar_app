<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Services\WorkflowService;
use App\Services\AssignmentService;
use App\Traits\SafeHookTrait;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    use SafeHookTrait;

    public function __construct(
        private WorkflowService $workflow,
        private AssignmentService $assignmentService
    ) {}

    public function index(Request $request)
    {
        if (! $request->user()->hasRole('sekretariat')) {
            abort(403, 'Hanya Sekretariat yang dapat mengelola penugasan.');
        }

        $submissions = Submission::whereIn('status', [
            SubmissionStatus::PROCESS,
            SubmissionStatus::ON_REVIEW,
        ])->with('student', 'assignments.reviewer')->latest()->get();

        $reviewers = User::role('reviewer')->get();

        return view('assignments.index', compact('submissions', 'reviewers'));
    }

    public function store(Request $request, Submission $submission)
    {
        if (! $request->user()->hasRole('sekretariat')) {
            abort(403, 'Hanya Sekretariat yang dapat mengelola penugasan.');
        }

        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'due_at' => 'nullable|date|after:today',
        ]);

        $reviewer = User::findOrFail($request->reviewer_id);
        if (! $reviewer->hasRole('reviewer')) {
            return back()->with('error', 'User yang dipilih bukan reviewer.');
        }

        if ($submission->assignments()->where('reviewer_id', $reviewer->id)->exists()) {
            return back()->with('error', 'Reviewer sudah ditugaskan ke pengajuan ini.');
        }

        Assignment::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $reviewer->id,
            'assigned_by' => $request->user()->id,
            'due_at' => $request->due_at,
        ]);

        // Transition to ON_REVIEW
        if ($submission->status === SubmissionStatus::PROCESS) {
            $this->workflow->transition($submission, SubmissionStatus::ON_REVIEW, $request->user(), "Reviewer ditugaskan: {$reviewer->name}");
        } else {
            try {
                $reviewer->notify(new \App\Notifications\ReviewerAssigned($submission));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to notify reviewer: " . $e->getMessage());
            }

            $reviewer->notify(new \App\Notifications\SubmissionWorkflowNotification(
                'Penugasan Reviewer Baru',
                "Anda telah ditugaskan untuk meninjau proposal: \"{$submission->title}\".",
                $submission->id,
                route('reviews.show', $submission)
            ));
        }

        $response = back()->with('success', "Reviewer {$reviewer->name} berhasil ditugaskan.");

        // Snapshot variables
        $submissionId = $submission->id;
        $reviewerId = $reviewer->id;
        $dueAt = $request->due_at;

        $this->safeHook(function () use ($submissionId, $reviewerId, $dueAt) {
            $this->assignmentService->assign($submissionId, $reviewerId, $dueAt, null);
        });

        return $response;
    }

    public function destroy(Request $request, Assignment $assignment)
    {
        if (! $request->user()->hasRole('sekretariat')) {
            abort(403, 'Hanya Sekretariat yang dapat mengelola penugasan.');
        }

        // Snapshot variables before delete
        $submission = $assignment->submission;
        $assignmentId = $assignment->id;
        $submissionId = $assignment->submission_id;
        $reviewerName = $assignment->reviewer->name;

        $assignment->delete();

        // Count remaining assignments
        $remainingAssignments = $submission->assignments()->count();

        // If status is ON_REVIEW and remaining count is 0, transition to PROCESS
        if (
            $submission->status === SubmissionStatus::ON_REVIEW
            && $remainingAssignments === 0
        ) {
            $this->workflow->transition(
                $submission,
                SubmissionStatus::PROCESS,
                $request->user(),
                'Seluruh reviewer telah dilepas'
            );
        }

        $response = back()->with('success', "Penugasan {$reviewerName} berhasil dihapus.");

        $this->safeHook(function () use ($assignmentId) {
            $this->assignmentService->unassign($assignmentId);
        });

        return $response;
    }
}

