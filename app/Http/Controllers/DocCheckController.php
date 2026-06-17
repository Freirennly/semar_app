<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class DocCheckController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    public function index()
    {
        $submissions = Submission::whereIn('status', [
            SubmissionStatus::NEW_PROPOSAL,
            SubmissionStatus::REVISED
        ])->with('student', 'documents')->latest()->get();
        return view('doccheck.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        $submission->load(['student', 'documents', 'statusHistories.changer']);
        return view('doccheck.show', compact('submission'));
    }

    public function approve(Request $request, Submission $submission)
    {
        if ($submission->status !== SubmissionStatus::NEW_PROPOSAL && $submission->status !== SubmissionStatus::REVISED) {
            return back()->with('error', 'Status tidak tepat.');
        }
        $this->workflow->transition($submission, SubmissionStatus::PROCESS, $request->user(), 'Dokumen dinyatakan lengkap dan masuk tahap proses');
        return redirect()->route('doccheck.index')->with('success', 'Dokumen diterima.');
    }

    public function returnToDraft(Request $request, Submission $submission)
    {
        $request->validate(['note' => 'required|string|max:2000']);
        if ($submission->status !== SubmissionStatus::NEW_PROPOSAL && $submission->status !== SubmissionStatus::REVISED) {
            return back()->with('error', 'Status tidak tepat.');
        }
        $this->workflow->transition($submission, SubmissionStatus::REJECTED, $request->user(), $request->note);
        return redirect()->route('doccheck.index')->with('success', 'Proposal ditolak.');
    }
}
