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
        $type = $request->input('type');
        $year = $request->input('year');
        $month = $request->input('month');
        $q = $request->input('q');

        $query = Submission::query();

        if ($status) {
            $query->where('status', $status);
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        if ($month) {
            $query->whereMonth('created_at', $month);
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

    private function getStatsAndTrends($query)
    {
        // Clone query for stats to apply the same filters
        $statsQuery = clone $query;
        
        $totalProposals = $statsQuery->count();
        $rawStatusDistribution = $statsQuery->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusDistribution = [];
        foreach ($rawStatusDistribution as $st => $count) {
            $key = is_object($st) ? $st->value : $st;
            $statusDistribution[$key] = $count;
        }

        // Calculate "Selesai" (DONE status)
        $doneCount = $statusDistribution[SubmissionStatus::DONE->value] ?? 0;

        // Calculate "Ditolak" (REJECTED status)
        $rejectedCount = $statusDistribution[SubmissionStatus::REJECTED->value] ?? 0;

        // Calculate "Proposal Aktif"
        $activeCount = ($statusDistribution[SubmissionStatus::PROCESS->value] ?? 0)
            + ($statusDistribution[SubmissionStatus::ON_REVIEW->value] ?? 0)
            + ($statusDistribution[SubmissionStatus::REVISION_REQUIRED->value] ?? 0)
            + ($statusDistribution[SubmissionStatus::REVISED->value] ?? 0)
            + ($statusDistribution[SubmissionStatus::APPROVED->value] ?? 0)
            + ($statusDistribution[SubmissionStatus::WAITING_STUDENT_CONFIRMATION->value] ?? 0)
            + ($statusDistribution[SubmissionStatus::WAITING_SIGNATURE->value] ?? 0);

        $metrics = [
            'total' => $totalProposals,
            'done' => $doneCount,
            'active' => $activeCount,
            'rejected' => $rejectedCount,
        ];

        return [
            'metrics' => $metrics,
            'statusDistribution' => $statusDistribution,
        ];
    }

    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $data = $this->getStatsAndTrends($query);

        // Limit to latest 10 proposals for the report view
        $latestSubmissions = $query->with('student')->latest()->limit(10)->get();
        $researchTypes = Submission::whereNotNull('type')->distinct()->pluck('type');

        return view('admin.reports.index', [
            'metrics' => $data['metrics'],
            'statusDistribution' => $data['statusDistribution'],
            'latestSubmissions' => $latestSubmissions,
            'researchTypes' => $researchTypes,
        ]);
    }

    public function print(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $data = $this->getStatsAndTrends($query);

        $allSubmissions = $query->with('student')->latest()->get();

        return view('admin.reports.print', [
            'metrics' => $data['metrics'],
            'statusDistribution' => $data['statusDistribution'],
            'allSubmissions' => $allSubmissions,
        ]);
    }
}
