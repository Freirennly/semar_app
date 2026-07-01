<?php

namespace App\Http\Controllers;

use App\Enums\DecisionType;
use App\Enums\SubmissionStatus;
use App\Models\Decision;
use App\Models\Submission;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class DecisionController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    public function index(Request $request)
    {
        $this->authorize('viewAnyDecision', Submission::class);

        $submissions = Submission::where('secretary_id', auth()->id())->whereIn('status', [
            SubmissionStatus::ON_REVIEW,
            SubmissionStatus::REVISED,
            SubmissionStatus::PROCESS
        ])
            ->with('student', 'reviews.reviewer', 'assignments.reviewer')
            ->latest()
            ->get();

        return view('decisions.index', compact('submissions'));
    }

    public function show(Request $request, Submission $submission)
    {
        $this->authorize('viewDecision', $submission);

        $submission->load(['student', 'documents', 'reviews.reviewer', 'assignments.reviewer', 'statusHistories.changer', 'decisions.decider']);
        return view('decisions.show', compact('submission'));
    }

    public function store(Request $request, Submission $submission)
    {
        $this->authorize('createDecision', $submission);

        $user = $request->user();

        $data = $request->validate([
            'decision' => 'required|in:APPROVED,REVISION_REQUIRED,REJECTED',
            'notes' => 'nullable|string|max:5000',
        ]);

        $round = $submission->decisions()->where('decision', 'REVISION_REQUIRED')->count() + 1;
        $assignedReviewerIds = $submission->assignments()->pluck('reviewer_id')->toArray();
        
        if (count($assignedReviewerIds) !== 2) {
            return back()->with('error', 'Keputusan tidak dapat disimpan karena pengajuan ini harus memiliki tepat 2 reviewer.');
        }

        $completedReviewerIds = $submission->reviews()
            ->where('revision_round', $round)
            ->whereNotNull('submitted_at')
            ->pluck('reviewer_id')
            ->toArray();

        foreach ($assignedReviewerIds as $reviewerId) {
            if (!in_array($reviewerId, $completedReviewerIds)) {
                return back()->with('error', 'Keputusan tidak dapat disimpan karena belum semua reviewer yang ditugaskan mengunggah review mereka pada putaran revisi ini.');
            }
        }

        Decision::create([
            'submission_id' => $submission->id,
            'decided_by' => $user->id,
            'decision' => $data['decision'],
            'notes' => $data['notes'],
            'decided_at' => now(),
        ]);

        $newStatus = match ($data['decision']) {
            'APPROVED' => SubmissionStatus::APPROVED,
            'REVISION_REQUIRED' => SubmissionStatus::REVISION_REQUIRED,
            'REJECTED' => SubmissionStatus::REJECTED,
        };

        $this->workflow->transition($submission, $newStatus, $user, $data['notes']);
        $submission->update(['decided_at' => now()]);

        return redirect()->route('decisions.index')
            ->with('success', 'Keputusan berhasil disimpan.');
    }
}
