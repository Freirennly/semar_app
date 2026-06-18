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
                SubmissionStatus::REJECTED,
                SubmissionStatus::DONE
            ])->count(),
            'approved' => Submission::whereIn('status', [SubmissionStatus::APPROVED, SubmissionStatus::DONE])->count(),
            'disapproved' => Submission::where('status', SubmissionStatus::REJECTED)->count(),
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

        $draftEcRequired = Submission::with('student')
            ->where('status', SubmissionStatus::APPROVED)
            ->whereNull('ec_number')
            ->latest()
            ->get();

        $certificatesGenerated = Submission::with('student')
            ->where('status', SubmissionStatus::DONE)
            ->latest()
            ->get();

        $missingCertificatesCount = 0;
        $doneSubmissions = Submission::where('status', SubmissionStatus::DONE)->get();
        foreach ($doneSubmissions as $sub) {
            if (empty($sub->ec_certificate_path) || !\Illuminate\Support\Facades\Storage::exists($sub->ec_certificate_path)) {
                $missingCertificatesCount++;
            }
        }

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
            'draftEcRequired',
            'certificatesGenerated',
            'missingCertificatesCount',
            'lastBackupTime',
            'templateMetrics',
            'integrityMetrics'
        ));
    }
}
