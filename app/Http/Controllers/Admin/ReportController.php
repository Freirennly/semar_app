<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use App\Models\Announcement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Cache report data for 15 minutes to improve performance
        $stats = Cache::remember('admin_reports_stats', 900, function () {
            // Proposals count
            $totalProposals = Submission::count();
            
            // Proposal Status Distribution
            $statusDistribution = Submission::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            // Users Count
            $totalReviewers = User::role('reviewer')->count();
            $totalSecretariat = User::role('sekretariat')->count();
            
            // Announcements Count
            $totalAnnouncements = Announcement::count();

            return [
                'total_proposals' => $totalProposals,
                'status_distribution' => $statusDistribution,
                'total_reviewers' => $totalReviewers,
                'total_secretariat' => $totalSecretariat,
                'total_announcements' => $totalAnnouncements,
            ];
        });

        // Also get latest 5 submissions without caching (to feel dynamic)
        $latestSubmissions = Submission::with('student')->latest()->take(5)->get();

        return view('admin.reports.index', compact('stats', 'latestSubmissions'));
    }
}
