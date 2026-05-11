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
        $submissions = Submission::where('status', SubmissionStatus::SUBMITTED)
            ->with('student', 'documents')->latest()->get();
        return view('doccheck.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        $submission->load(['student', 'documents', 'statusHistories.changer']);
        return view('doccheck.show', compact('submission'));
    }

    public function approve(Request $request, Submission $submission)
    {
        if ($submission->status !== SubmissionStatus::SUBMITTED) {
            return back()->with('error', 'Status tidak tepat.');
        }
        $this->workflow->transition($submission, SubmissionStatus::DOC_CHECK, $request->user(), 'Dokumen dinyatakan lengkap');
        return redirect()->route('doccheck.index')->with('success', 'Dokumen diterima.');
    }

    public function returnToDraft(Request $request, Submission $submission)
    {
        $request->validate(['note' => 'required|string|max:2000']);
        if ($submission->status !== SubmissionStatus::SUBMITTED) {
            return back()->with('error', 'Status tidak tepat.');
        }
        $this->workflow->transition($submission, SubmissionStatus::DRAFT, $request->user(), $request->note);
        return redirect()->route('doccheck.index')->with('success', 'Dikembalikan ke student.');
    }
}
