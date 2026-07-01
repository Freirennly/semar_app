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
        $this->authorize('viewAny', Assignment::class);

        $submissions = Submission::where('secretary_id', auth()->id())->whereIn('status', [
            SubmissionStatus::PROCESS,
            SubmissionStatus::ON_REVIEW,
        ])->with('student', 'assignments.reviewer')->latest()->get();

        $reviewers = User::role('reviewer')->get();

        return view('assignments.index', compact('submissions', 'reviewers'));
    }

    /**
     * RESTful store: POST /assignments
     * Payload: submission_id, reviewer_id, due_at
     */
    public function store(Request $request)
    {
        $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'reviewer_id' => 'required|exists:users,id',
            'due_at' => 'nullable|date|after_or_equal:today',
        ]);

        $submission = Submission::findOrFail($request->submission_id);
        $this->authorize('create', [Assignment::class, $submission]);
        $reviewer = User::findOrFail($request->reviewer_id);

        if (! $reviewer->hasRole('reviewer')) {
            return back()->with('error', 'User yang dipilih bukan reviewer.');
        }

        if (!in_array($submission->status, [SubmissionStatus::PROCESS, SubmissionStatus::ON_REVIEW])) {
            return back()->with('error', 'Penugasan reviewer hanya diperbolehkan saat proposal berstatus Diproses atau Sedang Direview.');
        }

        if ($submission->assignments()->where('reviewer_id', $reviewer->id)->exists()) {
            return back()->with('error', 'Reviewer sudah ditugaskan ke pengajuan ini.');
        }

        if ($submission->assignments()->count() >= 2) {
            return back()->with('error', 'Satu pengajuan maksimal hanya boleh ditugaskan kepada 2 reviewer.');
        }

        $round = $submission->decisions()->where('decision', 'REVISION_REQUIRED')->count() + 1;
        $hasReviews = $submission->reviews()
            ->where('revision_round', $round)
            ->whereNotNull('submitted_at')
            ->exists();

        if ($hasReviews) {
            return back()->with('error', 'Penugasan reviewer dikunci karena sudah ada review masuk pada putaran revisi ini.');
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
        $this->authorize('delete', $assignment);

        $submission = $assignment->submission;
        $round = $submission->decisions()->where('decision', 'REVISION_REQUIRED')->count() + 1;
        $hasSubmittedReview = $submission->reviews()
            ->where('reviewer_id', $assignment->reviewer_id)
            ->where('revision_round', $round)
            ->whereNotNull('submitted_at')
            ->exists();

        if ($hasSubmittedReview) {
            return back()->with('error', 'Penugasan tidak dapat dihapus karena reviewer sudah mengirim review pada putaran revisi ini.');
        }

        // Snapshot variables before delete
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
