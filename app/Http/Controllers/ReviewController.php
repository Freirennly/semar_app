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
        $this->authorize('viewAny', Review::class);

        $user = $request->user();
        $assignments = Assignment::where('reviewer_id', $user->id)
            ->with('submission.student')
            ->latest()
            ->get();

        return view('reviews.index', compact('assignments'));
    }

    public function show(Request $request, Submission $submission)
    {
        $this->authorize('view', [Review::class, $submission]);

        $user = $request->user();
        $assignment = $submission->assignments()->where('reviewer_id', $user->id)->firstOrFail();
        
        $round = $submission->decisions()->where('decision', \App\Enums\DecisionType::REVISION_REQUIRED->value)->count() + 1;
        $review = Review::where('submission_id', $submission->id)
            ->where('reviewer_id', $user->id)
            ->where('revision_round', $round)
            ->first();

        $submission->load(['documents', 'student']);

        $currentRound = $round;
        $previousReviews = $submission->reviews->whereNotNull('submitted_at')
            ->where('revision_round', '<', $currentRound)
            ->where('reviewer_id', $user->id)
            ->groupBy('revision_round')
            ->sortKeys();
            
        $revisionHistories = $submission->statusHistories()
            ->where('to_status', \App\Enums\SubmissionStatus::REVISED)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('reviews.show', compact('submission', 'assignment', 'review', 'currentRound', 'previousReviews', 'revisionHistories'));
    }

    /**
     * Submit review — hanya boleh satu kali per reviewer per submission per round.
     * Menggunakan Review::create() bukan updateOrCreate().
     */
    public function store(Request $request, Submission $submission)
    {
        $this->authorize('create', [Review::class, $submission]);

        $user = $request->user();
        /** @var \App\Models\Assignment $assignment */
        $assignment = $submission->assignments()->where('reviewer_id', $user->id)->firstOrFail();

        $data = $request->validate([
            'recommendation' => 'required|in:APPROVE,REVISION,REJECT,RECOMMEND_FULLBOARD',
            'notes' => 'required|string|max:10000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:10240',
        ]);

        $round = $submission->decisions()->where('decision', \App\Enums\DecisionType::REVISION_REQUIRED->value)->count() + 1;

        // Reviewer hanya boleh submit review satu kali per round — tidak boleh overwrite
        $existingReview = Review::where('submission_id', $submission->id)
            ->where('reviewer_id', $user->id)
            ->where('revision_round', $round)
            ->first();

        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'Review sudah pernah dikirim untuk pengajuan ini pada putaran revisi ini. Anda tidak dapat mengubah review.');
        }

        Review::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $user->id,
            'revision_round' => $round,
            'recommendation' => $data['recommendation'],
            'notes' => $data['notes'],
            'submitted_at' => now(),
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('reviews/' . $submission->id, 'public');
            $submission->documents()->create([
                'document_template_id' => null,
                'doc_type' => 'REVIEW_ATTACHMENT_R' . $round . '_U' . $user->id,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);
        }

        $assignment->update(['status' => 'COMPLETED']);

        if ($submission->status === SubmissionStatus::PROCESS) {
            $this->workflow->transition($submission, SubmissionStatus::ON_REVIEW, $user, "Reviewer mengunggah review: {$user->name}");
        }

        // Check if all reviews complete for this round -> notify admins
        $totalAssignments = $submission->assignments()->count();
        $completedReviews = $submission->reviews()
            ->where('revision_round', $round)
            ->whereNotNull('submitted_at')
            ->count();

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
