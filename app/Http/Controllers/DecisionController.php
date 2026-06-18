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
        if (! $request->user()->hasRole('sekretariat')) {
            abort(403, 'Hanya Sekretariat yang dapat mengelola keputusan.');
        }

        $submissions = Submission::where('status', SubmissionStatus::ON_REVIEW)
            ->with('student', 'reviews.reviewer', 'assignments.reviewer')
            ->latest()
            ->get();

        return view('decisions.index', compact('submissions'));
    }

    public function show(Request $request, Submission $submission)
    {
        if (! $request->user()->hasRole('sekretariat')) {
            abort(403, 'Hanya Sekretariat yang dapat mengelola keputusan.');
        }

        $submission->load(['student', 'documents', 'reviews.reviewer', 'assignments.reviewer', 'statusHistories.changer', 'decisions.decider']);
        return view('decisions.show', compact('submission'));
    }

    public function store(Request $request, Submission $submission)
    {
        if (! $request->user()->hasRole('sekretariat')) {
            abort(403, 'Hanya Sekretariat yang dapat mengelola keputusan.');
        }

        $user = $request->user();

        $data = $request->validate([
            'decision' => 'required|in:APPROVED,APPROVED_WITH_REVISION,REJECTED',
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
            'APPROVED_WITH_REVISION' => SubmissionStatus::APPROVED_WITH_REVISION,
            'REJECTED' => SubmissionStatus::REJECTED,
        };

        $this->workflow->transition($submission, $newStatus, $user, $data['notes']);
        $submission->update(['decided_at' => now()]);

        return redirect()->route('decisions.index')
            ->with('success', 'Keputusan berhasil disimpan.');
    }
}
