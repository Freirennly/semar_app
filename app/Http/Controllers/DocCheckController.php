<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Services\WorkflowService;
use App\Services\DocCheckService;
use App\Traits\SafeHookTrait;
use Illuminate\Http\Request;

class DocCheckController extends Controller
{
    use SafeHookTrait;

    public function __construct(
        private WorkflowService $workflow,
        private DocCheckService $docCheckService
    ) {}

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
        
        $response = redirect()->route('doccheck.index')->with('success', 'Dokumen diterima.');

        $submissionId = $submission->id;
        $this->safeHook(function () use ($submissionId) {
            $this->docCheckService->approve($submissionId);
        });

        return $response;
    }

    public function returnToDraft(Request $request, Submission $submission)
    {
        $request->validate(['note' => 'required|string|max:2000']);
        if ($submission->status !== SubmissionStatus::NEW_PROPOSAL && $submission->status !== SubmissionStatus::REVISED) {
            return back()->with('error', 'Status tidak tepat.');
        }
        $this->workflow->transition($submission, SubmissionStatus::RESUBMISSION, $request->user(), $request->note);
        
        $response = redirect()->route('doccheck.index')->with('success', 'Proposal ditolak.');

        $submissionId = $submission->id;
        $note = $request->note;
        $this->safeHook(function () use ($submissionId, $note) {
            $this->docCheckService->returnToDraft($submissionId, $note);
        });

        return $response;
    }
}

