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

    public function index()
    {
        $submissions = Submission::where('status', SubmissionStatus::PENDING_DECISION)
            ->with('student', 'reviews.reviewer', 'assignments.reviewer')
            ->latest()
            ->get();

        return view('decisions.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        $submission->load(['student', 'documents', 'reviews.reviewer', 'assignments.reviewer', 'statusHistories.changer', 'decisions.decider']);
        return view('decisions.show', compact('submission'));
    }

    public function store(Request $request, Submission $submission)
    {
        $user = $request->user();

        $data = $request->validate([
            'decision' => 'required|in:APPROVED,RESUBMISSION,DISAPPROVED',
            'notes' => 'nullable|string|max:5000',
        ]);

        Decision::create([
            'submission_id' => $submission->id,
            'decided_by' => $user->id,
            'decision' => $data['decision'],
            'notes' => $data['notes'],
            'decided_at' => now(),
        ]);

        $newStatus = match ($data['decision']) {
            'APPROVED' => SubmissionStatus::APPROVED,
            'RESUBMISSION' => SubmissionStatus::RESUBMISSION,
            'DISAPPROVED' => SubmissionStatus::DISAPPROVED,
        };

        $this->workflow->transition($submission, $newStatus, $user, $data['notes']);
        $submission->update(['decided_at' => now()]);

        return redirect()->route('decisions.index')
            ->with('success', 'Keputusan berhasil disimpan.');
    }
}
