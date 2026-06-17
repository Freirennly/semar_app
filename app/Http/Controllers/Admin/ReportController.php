<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Enums\SubmissionStatus;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function getFilteredQuery(Request $request)
    {
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $q = $request->input('q');

        $query = Submission::query();

        if ($status) {
            $query->where('status', $status);
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($q) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('title', 'like', "%{$q}%")
                         ->orWhere('code', 'like', "%{$q}%")
                         ->orWhereHas('student', function ($uQuery) use ($q) {
                             $uQuery->where('name', 'like', "%{$q}%");
                         });
            });
        }

        return $query;
    }

    private function getStatsAndTrends($query, Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Clone query for stats to apply the same filters
        $statsQuery = clone $query;
        
        $totalProposals = $statsQuery->count();
        $statusDistribution = $statsQuery->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Calculate "Selesai" (DONE status)
        $doneCount = $statusDistribution[SubmissionStatus::DONE->value] ?? 0;

        // Calculate "Ditolak" (REJECTED status)
        $rejectedCount = $statusDistribution[SubmissionStatus::REJECTED->value] ?? 0;

        // Calculate "Sedang Diproses" (All except DONE and REJECTED)
        $processedCount = 0;
        foreach ($statusDistribution as $st => $count) {
            if ($st !== SubmissionStatus::DONE->value && $st !== SubmissionStatus::REJECTED->value) {
                $processedCount += $count;
            }
        }

        $metrics = [
            'total' => $totalProposals,
            'done' => $doneCount,
            'processed' => $processedCount,
            'rejected' => $rejectedCount,
        ];

        // PHP-based monthly grouping to be DB-agnostic
        $allFilteredSubmissions = $query->with('student')->latest()->get();

        $monthlyTrend = [];
        foreach ($allFilteredSubmissions as $sub) {
            $month = $sub->created_at->format('Y-m'); // e.g. "2026-06"
            if (!isset($monthlyTrend[$month])) {
                $monthlyTrend[$month] = 0;
            }
            $monthlyTrend[$month]++;
        }
        ksort($monthlyTrend);

        // Limit monthly trend to the last 6 months for clear visualization
        $monthlyTrend = array_slice($monthlyTrend, -6, 6, true);

        // Stats for decisions (Tren Keputusan)
        $decisionsQuery = \App\Models\Decision::query();
        if ($startDate) {
            $decisionsQuery->whereDate('decided_at', '>=', $startDate);
        }
        if ($endDate) {
            $decisionsQuery->whereDate('decided_at', '<=', $endDate);
        }
        $decisions = $decisionsQuery->get();

        $decisionStats = [
            'APPROVED' => 0,
            'APPROVED_WITH_REVISION' => 0,
            'REJECTED' => 0,
        ];
        foreach ($decisions as $d) {
            $type = $d->decision->value ?? $d->decision;
            if (isset($decisionStats[$type])) {
                $decisionStats[$type]++;
            }
        }

        // Reviewer stats (Aktivitas Reviewer)
        $reviewerStats = \App\Models\User::role('reviewer')
            ->withCount(['reviews' => function ($rQuery) use ($startDate, $endDate) {
                if ($startDate) {
                    $rQuery->whereDate('submitted_at', '>=', $startDate);
                }
                if ($endDate) {
                    $rQuery->whereDate('submitted_at', '<=', $endDate);
                }
            }])
            ->orderByDesc('reviews_count')
            ->limit(5)
            ->get();

        // Get status histories for submissions that match the filtered query
        $submissionIds = $allFilteredSubmissions->pluck('id');
        $latestActivities = \App\Models\StatusHistory::with(['submission', 'changer'])
            ->whereIn('submission_id', $submissionIds)
            ->latest()
            ->limit(10)
            ->get();

        return [
            'metrics' => $metrics,
            'statusDistribution' => $statusDistribution,
            'monthlyTrend' => $monthlyTrend,
            'latestActivities' => $latestActivities,
            'allSubmissions' => $allFilteredSubmissions,
            'decisionStats' => $decisionStats,
            'reviewerStats' => $reviewerStats,
        ];
    }

    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $data = $this->getStatsAndTrends($query, $request);

        // Paginate submissions list for the detail table
        $paginatedSubmissions = $query->with('student')->latest()->paginate(10)->withQueryString();

        return view('admin.reports.index', [
            'metrics' => $data['metrics'],
            'statusDistribution' => $data['statusDistribution'],
            'monthlyTrend' => $data['monthlyTrend'],
            'latestActivities' => $data['latestActivities'],
            'paginatedSubmissions' => $paginatedSubmissions,
            'decisionStats' => $data['decisionStats'],
            'reviewerStats' => $data['reviewerStats'],
        ]);
    }

    public function print(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $data = $this->getStatsAndTrends($query, $request);

        return view('admin.reports.print', [
            'metrics' => $data['metrics'],
            'statusDistribution' => $data['statusDistribution'],
            'monthlyTrend' => $data['monthlyTrend'],
            'allSubmissions' => $data['allSubmissions'],
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
            'decisionStats' => $data['decisionStats'],
            'reviewerStats' => $data['reviewerStats'],
        ]);
    }
}
