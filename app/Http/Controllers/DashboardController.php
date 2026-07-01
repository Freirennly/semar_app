<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\Assignment;
use App\Models\Review;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('student')) {
            return $this->student($user);
        } elseif ($user->hasRole('reviewer')) {
            return $this->reviewer($user);
        } elseif ($user->hasRole('ketua')) {
            return $this->ketua();
        } elseif ($user->hasRole('sekretariat')) {
            return $this->sekretariat();
        } elseif ($user->hasRole('admin')) {
            return $this->admin();
        }

        abort(403);
    }
    private function student($user)
    {
        $submissions = $user->submissions()->latest()->get();
        $metrics = [
            ['label' => 'Total Pengajuan', 'value' => $submissions->count(), 'color' => 'blue'],
            ['label' => 'Proposal Baru', 'value' => $submissions->where('status', SubmissionStatus::NEW_PROPOSAL)->count(), 'color' => 'slate'],
            ['label' => 'Sedang Diproses', 'value' => $submissions->whereIn('status', [
                SubmissionStatus::PROCESS,
                SubmissionStatus::ON_REVIEW,
                SubmissionStatus::REVISED,
                SubmissionStatus::WAITING_STUDENT_CONFIRMATION,
                SubmissionStatus::WAITING_SIGNATURE
            ])->count(), 'color' => 'violet'],
            ['label' => 'Perlu Revisi', 'value' => $submissions->where('status', SubmissionStatus::REVISION_REQUIRED)->count(), 'color' => 'amber'],
            ['label' => 'Selesai', 'value' => $submissions->whereIn('status', [
                SubmissionStatus::APPROVED,
                SubmissionStatus::REJECTED,
                SubmissionStatus::DONE
            ])->count(), 'color' => 'emerald'],
        ];

        $waitingEcConfirmation = $user->submissions()
            ->where('status', SubmissionStatus::WAITING_STUDENT_CONFIRMATION)
            ->whereNotNull('ec_number')
            ->latest()
            ->get();

        $ecReady = $user->submissions()
            ->where('status', SubmissionStatus::DONE)
            ->whereNotNull('ec_certificate_path')
            ->latest()
            ->get();

        $recentDownloads = \App\Models\ActivityLog::where('user_id', $user->id)
            ->where('description', 'like', 'Sertifikat diunduh%')
            ->with('submission')
            ->latest()
            ->limit(5)
            ->get();

        // Document completion metrics for latest active submission
        $docCompletionData = null;
        $latestActive = $submissions->whereNotIn('status', [
            SubmissionStatus::REJECTED,
            SubmissionStatus::DONE,
        ])->first();

        if ($latestActive) {
            $requiredTemplates = \App\Models\DocumentTemplate::visible()
                ->where('is_required', true)->get();
            $optionalTemplates = \App\Models\DocumentTemplate::visible()
                ->where('is_required', false)->get();
            $uploadedIds = $latestActive->documents->pluck('document_template_id')->toArray();

            $missingRequired = $requiredTemplates->filter(fn($t) => !in_array($t->id, $uploadedIds));
            $uploadedCount = count(array_intersect($requiredTemplates->pluck('id')->toArray(), $uploadedIds));
            $totalRequired = $requiredTemplates->count();
            $completionPct = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 100;

            $docCompletionData = [
                'submission' => $latestActive,
                'required' => $requiredTemplates,
                'optional' => $optionalTemplates,
                'uploaded_ids' => $uploadedIds,
                'missing' => $missingRequired,
                'uploaded_count' => $uploadedCount,
                'total_required' => $totalRequired,
                'completion_pct' => $completionPct,
            ];
        }

        return view('dashboard.student', compact('submissions', 'metrics', 'waitingEcConfirmation', 'ecReady', 'recentDownloads', 'docCompletionData'));
    }

    private function reviewer($user)
    {
        $assignments = Assignment::where('reviewer_id', $user->id)->with('submission.student')->latest()->get();
        $reviews = Review::where('reviewer_id', $user->id)->get();
        
        $upcomingFullboard = \App\Models\FullboardMeeting::with('submission')
            ->where('scheduled_at', '>=', now())
            ->whereHas('submission.reviews', function ($q) use ($user) {
                $q->where('reviewer_id', $user->id);
            })
            ->orderBy('scheduled_at', 'asc')
            ->limit(5)
            ->get();

        $metrics = [
            ['label' => 'Ditugaskan', 'value' => $assignments->count(), 'color' => 'blue'],
            ['label' => 'Belum Direview', 'value' => $assignments->count() - $reviews->whereNotNull('submitted_at')->count(), 'color' => 'amber'],
            ['label' => 'Selesai', 'value' => $reviews->whereNotNull('submitted_at')->count(), 'color' => 'emerald'],
        ];
        return view('dashboard.reviewer', compact('assignments', 'metrics', 'upcomingFullboard'));
    }

    private function ketua()
    {
        $waitingSignature = Submission::where('status', SubmissionStatus::WAITING_SIGNATURE)
            ->where('signatory_id', auth()->id())
            ->with('student')->latest()->get();

        $recentlySigned = Submission::where('signatory_id', auth()->id())
            ->whereNotNull('signed_at')
            ->with('student')->latest()->limit(10)->get();

        $verifiedLogs = \App\Models\ActivityLog::whereHas('submission', function ($query) {
                $query->where('signatory_id', auth()->id());
            })
            ->where('description', 'like', 'Verifikasi sertifikat diakses secara publik%')
            ->with('submission')
            ->latest()
            ->limit(10)
            ->get();

        $upcomingFullboard = \App\Models\FullboardMeeting::with('submission')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->limit(5)
            ->get();

        $metrics = [
            ['label' => 'Menunggu Tanda Tangan', 'value' => $waitingSignature->count(), 'color' => 'amber'],
            ['label' => 'Total Pengajuan Aktif', 'value' => Submission::whereNotIn('status', [SubmissionStatus::REJECTED, SubmissionStatus::DONE])->count(), 'color' => 'violet'],
        ];
        return view('dashboard.ketua', compact('waitingSignature', 'recentlySigned', 'metrics', 'verifiedLogs', 'upcomingFullboard'));
    }

    private function sekretariat()
    {
        $userId = auth()->id();

        $submitted = Submission::where('secretary_id', $userId)->whereIn('status', [
            SubmissionStatus::PROCESS,
            SubmissionStatus::REVISED
        ])->with('student')->latest()->get()->filter(function($sub) {
            if ($sub->status === SubmissionStatus::REVISED) return true;
            return !$sub->activityLogs()->where('description', 'like', '%Dokumen dinyatakan lengkap%')->exists();
        });

        $pendingDecision = Submission::where('secretary_id', $userId)->where('status', SubmissionStatus::ON_REVIEW)
            ->with('student', 'reviews.reviewer', 'assignments.reviewer')->latest()->get();

        $needAssign = Submission::where('secretary_id', $userId)->where('status', SubmissionStatus::PROCESS)
            ->whereDoesntHave('assignments')->with('student')->latest()->get()->filter(function($sub) {
                return $sub->activityLogs()->where('description', 'like', '%Dokumen dinyatakan lengkap%')->exists();
            });

        $assigned = Submission::where('secretary_id', $userId)->where('status', SubmissionStatus::ON_REVIEW)
            ->with('student', 'assignments.reviewer')->latest()->get();

        $metrics = [
            ['label' => 'Perlu Cek Dokumen', 'value' => $submitted->count(), 'color' => 'amber'],
            ['label' => 'Perlu Assign Reviewer', 'value' => $needAssign->count(), 'color' => 'blue'],
            ['label' => 'Menunggu Keputusan', 'value' => $pendingDecision->count(), 'color' => 'violet'],
        ];
        return view('dashboard.sekretariat', compact('submitted', 'pendingDecision', 'needAssign', 'assigned', 'metrics'));
    }

    private function admin()
    {
        return redirect()->route('admin.dashboard');
    }
}
