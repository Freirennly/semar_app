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
        $this->authorize('viewAnyDocCheck', Submission::class);

        $submissions = Submission::where('secretary_id', auth()->id())->whereIn('status', [
            SubmissionStatus::PROCESS,
            SubmissionStatus::REVISED
        ])->with('student', 'documents')->latest()->get()->filter(function($sub) {
            if ($sub->status === SubmissionStatus::REVISED) return true;
            return !$sub->activityLogs()->where('description', 'like', '%Dokumen dinyatakan lengkap%')->exists();
        });
        return view('doccheck.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        $this->authorize('viewDocCheck', $submission);

        $submission->load(['student', 'documents', 'statusHistories.changer']);
        return view('doccheck.show', compact('submission'));
    }

    public function approve(Request $request, Submission $submission)
    {
        $this->authorize('approveDocCheck', $submission);

        $isVerified = $submission->activityLogs()->where('description', 'like', '%Dokumen dinyatakan lengkap%')->exists();
        if ($submission->status === SubmissionStatus::PROCESS && $isVerified) {
            return redirect()->route('doccheck.index')->with('warning', 'Dokumen sudah diverifikasi sebelumnya.');
        }

        if ($submission->status !== SubmissionStatus::PROCESS) {
            $this->workflow->transition($submission, SubmissionStatus::PROCESS, $request->user(), 'Dokumen dinyatakan lengkap dan masuk tahap proses');
        } else {
            \App\Models\ActivityLog::create([
                'user_id' => $request->user()->id,
                'submission_id' => $submission->id,
                'old_status' => $submission->status->value,
                'new_status' => $submission->status->value,
                'description' => 'Dokumen dinyatakan lengkap dan diverifikasi.',
            ]);
        }
        
        $submission->assignments()->update(['status' => 'ASSIGNED']);

        $response = redirect()->route('doccheck.index')->with('success', 'Dokumen diterima.');

        $submissionId = $submission->id;
        $this->safeHook(function () use ($submissionId) {
            $this->docCheckService->approve($submissionId);
        });

        return $response;
    }

    public function returnToDraft(Request $request, Submission $submission)
    {
        $this->authorize('returnDocCheck', $submission);

        $request->validate(['note' => 'required|string|max:2000']);

        $this->workflow->transition($submission, SubmissionStatus::REVISION_REQUIRED, $request->user(), $request->note);
        
        $response = redirect()->route('doccheck.index')->with('success', 'Proposal ditolak.');

        $submissionId = $submission->id;
        $note = $request->note;
        $this->safeHook(function () use ($submissionId, $note) {
            $this->docCheckService->returnToDraft($submissionId, $note);
        });

        return $response;
    }
}
