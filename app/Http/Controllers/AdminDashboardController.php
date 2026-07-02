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

        // 3. Dashboard Metrics & Status Distribution
        $rawStatusDistribution = Submission::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
            
        $statusDistribution = [];
        foreach ($rawStatusDistribution as $item) {
            $key = is_object($item->status) ? $item->status->value : $item->status;
            $statusDistribution[$key] = $item->count;
        }

        $userSummary = DB::table('roles')
            ->leftJoin('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.name', DB::raw('count(model_has_roles.model_id) as count'))
            ->groupBy('roles.name')
            ->get();

        $roleCounts = $userSummary->pluck('count', 'name');

        $metrics = [
            'total_users' => User::count(),
            'total_students' => $roleCounts->get('student', 0),
            'total_reviewers' => $roleCounts->get('reviewer', 0),
            'total_secretariat' => $roleCounts->get('sekretariat', 0),
            
            'total_submissions' => Submission::count(),
            'new_proposal' => ($statusDistribution[SubmissionStatus::NEW_PROPOSAL->value] ?? 0),
            'process' => ($statusDistribution[SubmissionStatus::PROCESS->value] ?? 0),
            'on_review' => ($statusDistribution[SubmissionStatus::ON_REVIEW->value] ?? 0),
            'revision_required' => ($statusDistribution[SubmissionStatus::REVISION_REQUIRED->value] ?? 0),
            'revised' => ($statusDistribution[SubmissionStatus::REVISED->value] ?? 0),
            'approved' => ($statusDistribution[SubmissionStatus::APPROVED->value] ?? 0),
            'waiting_student_confirmation' => ($statusDistribution[SubmissionStatus::WAITING_STUDENT_CONFIRMATION->value] ?? 0),
            'rejected' => ($statusDistribution[SubmissionStatus::REJECTED->value] ?? 0),
            'waiting_signature' => ($statusDistribution[SubmissionStatus::WAITING_SIGNATURE->value] ?? 0),
            'done' => ($statusDistribution[SubmissionStatus::DONE->value] ?? 0),
        ];

        // 4. Admin Overview Sections Data
        $latestSubmissions = $submissionQuery->with('student')->latest()->limit(5)->get();

        $latestActivities = StatusHistory::with(['submission', 'changer'])
            ->latest()
            ->limit(10)
            ->get();


        $backupDir = storage_path('app/private/backups/ec');
        $backupFiles = glob("{$backupDir}/ec-backup-*.zip");
        $lastBackupTime = 'N/A';
        if (!empty($backupFiles)) {
            sort($backupFiles);
            $latestFile = end($backupFiles);
            $lastBackupTime = date('Y-m-d H:i:s', filemtime($latestFile));
        }

        // Document Template & Integrity Metrics
        $templateMetrics = [
            'total' => \App\Models\DocumentTemplate::count(),
            'active' => \App\Models\DocumentTemplate::where('is_archived', false)->where('is_shown', true)->count(),
            'archived' => \App\Models\DocumentTemplate::where('is_archived', true)->count(),
            'hidden' => \App\Models\DocumentTemplate::where('is_shown', false)->where('is_archived', false)->count(),
        ];

        $integrityService = app(\App\Services\DocumentIntegrityService::class);
        $integrityResult = $integrityService->validateAll();
        $integrityMetrics = [
            'missing_required' => $integrityResult['missing_required'],
            'broken_files' => $integrityResult['broken_files'],
            'invalid_links' => $integrityResult['invalid_links'],
            'orphan_records' => $integrityResult['orphan_records'],
        ];

        return view('dashboard.admin', compact(
            'metrics', 
            'latestSubmissions', 
            'statusDistribution', 
            'latestActivities', 
            'userSummary',
            'lastBackupTime',
            'templateMetrics',
            'integrityMetrics'
        ));
    }
}
