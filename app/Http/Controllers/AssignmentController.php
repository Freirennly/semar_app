<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    public function index()
    {
        $submissions = Submission::whereIn('status', [
            SubmissionStatus::DOC_CHECK,
            SubmissionStatus::SUBMITTED,
            SubmissionStatus::ASSIGNED,
        ])->with('student', 'assignments.reviewer')->latest()->get();

        $reviewers = User::role('reviewer')->get();

        return view('assignments.index', compact('submissions', 'reviewers'));
    }

    public function store(Request $request, Submission $submission)
    {
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

        // Transition to ASSIGNED if not already
        if ($submission->status === SubmissionStatus::DOC_CHECK || $submission->status === SubmissionStatus::SUBMITTED) {
            $this->workflow->transition($submission, SubmissionStatus::ASSIGNED, $request->user(), "Reviewer ditugaskan: {$reviewer->name}");
        }

        return back()->with('success', "Reviewer {$reviewer->name} berhasil ditugaskan.");
    }

    public function destroy(Request $request, Assignment $assignment)
    {
        $name = $assignment->reviewer->name;
        $assignment->delete();
        return back()->with('success', "Penugasan {$name} berhasil dihapus.");
    }
}
