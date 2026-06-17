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
                SubmissionStatus::WAITING_SIGNATURE
            ])->count(), 'color' => 'violet'],
            ['label' => 'Perlu Revisi', 'value' => $submissions->where('status', SubmissionStatus::RESUBMISSION)->count(), 'color' => 'amber'],
            ['label' => 'Selesai', 'value' => $submissions->whereIn('status', [
                SubmissionStatus::APPROVED,
                SubmissionStatus::APPROVED_WITH_REVISION,
                SubmissionStatus::REJECTED,
                SubmissionStatus::DONE
            ])->count(), 'color' => 'emerald'],
        ];
        return view('dashboard.student', compact('submissions', 'metrics'));
    }

    private function reviewer($user)
    {
        $assignments = Assignment::where('reviewer_id', $user->id)->with('submission.student')->latest()->get();
        $reviews = Review::where('reviewer_id', $user->id)->get();
        $metrics = [
            ['label' => 'Ditugaskan', 'value' => $assignments->count(), 'color' => 'blue'],
            ['label' => 'Belum Direview', 'value' => $assignments->count() - $reviews->whereNotNull('submitted_at')->count(), 'color' => 'amber'],
            ['label' => 'Selesai', 'value' => $reviews->whereNotNull('submitted_at')->count(), 'color' => 'emerald'],
        ];
        return view('dashboard.reviewer', compact('assignments', 'metrics'));
    }

    private function ketua()
    {
        $needAssign = Submission::where('status', SubmissionStatus::PROCESS)
            ->whereDoesntHave('assignments')->with('student')->latest()->get();
        $assigned = Submission::where('status', SubmissionStatus::ON_REVIEW)
            ->with('student', 'assignments.reviewer')->latest()->get();
        $metrics = [
            ['label' => 'Perlu Assign Reviewer', 'value' => $needAssign->count(), 'color' => 'amber'],
            ['label' => 'Sudah Di-assign', 'value' => $assigned->count(), 'color' => 'blue'],
            ['label' => 'Total Pengajuan Aktif', 'value' => Submission::whereNotIn('status', [SubmissionStatus::REJECTED, SubmissionStatus::DONE])->count(), 'color' => 'violet'],
        ];
        return view('dashboard.ketua', compact('needAssign', 'assigned', 'metrics'));
    }

    private function sekretariat()
    {
        $submitted = Submission::whereIn('status', [
            SubmissionStatus::NEW_PROPOSAL,
            SubmissionStatus::REVISED
        ])->with('student')->latest()->get();
        $pendingDecision = Submission::where('status', SubmissionStatus::ON_REVIEW)
            ->with('student', 'reviews.reviewer', 'assignments.reviewer')->latest()->get();
        $metrics = [
            ['label' => 'Perlu Cek Dokumen', 'value' => $submitted->count(), 'color' => 'amber'],
            ['label' => 'Menunggu Keputusan', 'value' => $pendingDecision->count(), 'color' => 'violet'],
            ['label' => 'Total Disetujui', 'value' => Submission::where('status', SubmissionStatus::APPROVED)->count(), 'color' => 'emerald'],
            ['label' => 'Total Ditolak', 'value' => Submission::where('status', SubmissionStatus::REJECTED)->count(), 'color' => 'red'],
        ];
        return view('dashboard.sekretariat', compact('submitted', 'pendingDecision', 'metrics'));
    }

    private function admin()
    {
        return redirect()->route('admin.dashboard');
    }
}
