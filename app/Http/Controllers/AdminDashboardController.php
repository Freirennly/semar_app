<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Submission;
use App\Models\StatusHistory;
use App\Enums\SubmissionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 5. Search Logic
        $search = $request->query('q');
        $submissionQuery = Submission::query();
        
        if ($search) {
            $submissionQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Dashboard Metrics
        $metrics = [
            'total_users' => User::count(),
            'total_submissions' => Submission::count(),
            'active_submissions' => Submission::whereNotIn('status', [
                SubmissionStatus::DRAFT, 
                SubmissionStatus::APPROVED, 
                SubmissionStatus::DISAPPROVED, 
                SubmissionStatus::ARCHIVED,
                SubmissionStatus::PUBLISHED
            ])->count(),
            'approved' => Submission::where('status', SubmissionStatus::APPROVED)->count(),
            'disapproved' => Submission::where('status', SubmissionStatus::DISAPPROVED)->count(),
            'resubmission' => Submission::where('status', SubmissionStatus::RESUBMISSION)->count(),
            'total_reviewers' => User::role('reviewer')->count(),
            'total_secretariat' => User::role('sekretariat')->count(),
        ];

        // 4. Admin Overview Sections Data
        $latestSubmissions = $submissionQuery->with('student')->latest()->limit(5)->get();

        $statusDistribution = Submission::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                // Handle case where status might be enum or string
                $key = is_object($item->status) ? $item->status->value : $item->status;
                return [$key => $item->count];
            });

        $latestActivities = StatusHistory::with(['submission', 'changer'])
            ->latest()
            ->limit(10)
            ->get();

        $userSummary = DB::table('roles')
            ->leftJoin('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.name', DB::raw('count(model_has_roles.model_id) as count'))
            ->groupBy('roles.name')
            ->get();

        return view('dashboard.admin', compact(
            'metrics', 
            'latestSubmissions', 
            'statusDistribution', 
            'latestActivities', 
            'userSummary'
        ));
    }
}
