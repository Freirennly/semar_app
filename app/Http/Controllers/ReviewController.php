<?php

namespace App\Http\Controllers;

use App\Enums\Recommendation;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\Review;
use App\Models\Submission;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $assignments = Assignment::where('reviewer_id', $user->id)
            ->with('submission.student')
            ->latest()
            ->get();

        return view('reviews.index', compact('assignments'));
    }

    public function show(Request $request, Submission $submission)
    {
        $user = $request->user();
        $assignment = $submission->assignments()->where('reviewer_id', $user->id)->firstOrFail();
        $review = Review::where('submission_id', $submission->id)->where('reviewer_id', $user->id)->first();

        $submission->load(['documents', 'student']);

        return view('reviews.show', compact('submission', 'assignment', 'review'));
    }

    public function store(Request $request, Submission $submission)
    {
        $user = $request->user();
        /** @var \App\Models\Assignment $assignment */
        $assignment = $submission->assignments()->where('reviewer_id', $user->id)->firstOrFail();

        $data = $request->validate([
            'recommendation' => 'required|in:APPROVE,REVISION,REJECT',
            'notes' => 'required|string|max:10000',
        ]);

        $review = Review::updateOrCreate(
            ['submission_id' => $submission->id, 'reviewer_id' => $user->id],
            [
                'recommendation' => $data['recommendation'],
                'notes' => $data['notes'],
                'submitted_at' => now(),
            ]
        );

        $assignment->update(['status' => 'COMPLETED']);


        // Check if all reviews complete -> notify admins
        $totalAssignments = $submission->assignments()->count();
        $completedReviews = $submission->reviews()->whereNotNull('submitted_at')->count();

        if ($completedReviews >= $totalAssignments && $submission->status === SubmissionStatus::ON_REVIEW) {
            $admins = \App\Models\User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\SubmissionWorkflowNotification(
                    'Hasil Review Masuk',
                    "Hasil review untuk proposal \"{$submission->title}\" telah diunggah lengkap. Menunggu keputusan.",
                    $submission->id,
                    route('decisions.show', $submission)
                ));
            }
        }

        return redirect()->route('reviews.index')
            ->with('success', 'Review berhasil disubmit.');
    }
}
