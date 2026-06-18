<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Submission;
use App\Enums\SubmissionStatus;
use Illuminate\Support\Facades\Storage;

class StorageHealthCheck extends Command
{
    protected $signature = 'ec:health';
    protected $description = 'Perform storage health check on Ethical Clearance certificates';

    public function handle()
    {
        // 1. Missing PDF Files
        $doneSubmissions = Submission::where('status', SubmissionStatus::DONE)->get();
        $missingCount = 0;
        $invalidDoneCount = 0;
        $missingList = [];
        $invalidList = [];

        foreach ($doneSubmissions as $sub) {
            $path = $sub->ec_certificate_path;
            
            // Check missing file
            if (empty($path) || !Storage::exists($path)) {
                $missingCount++;
                $missingList[] = [
                    'code' => $sub->code,
                    'status' => 'Missing File',
                    'path' => $path ?: 'N/A',
                ];
            }

            // Check invalid DONE records
            if (
                is_null($sub->ec_number) ||
                is_null($sub->signatory_id) ||
                is_null($sub->confirmed_title) ||
                is_null($sub->confirmed_researcher_name) ||
                is_null($sub->signed_at) ||
                is_null($sub->ec_certificate_path) ||
                is_null($sub->verification_token)
            ) {
                $invalidDoneCount++;
                $invalidList[] = [
                    'code' => $sub->code,
                    'status' => 'Invalid DONE Record',
                    'reason' => 'Missing essential metadata',
                ];
            }
        }

        // 2. Orphan PDF Files
        $allFiles = Storage::files('private/ec_certificates');
        $activePaths = Submission::pluck('ec_certificate_path')->filter()->toArray();
        $orphanCount = 0;
        $orphanList = [];

        foreach ($allFiles as $file) {
            if (!in_array($file, $activePaths)) {
                $orphanCount++;
                $orphanList[] = [
                    'file' => $file,
                    'status' => 'Orphan File',
                ];
            }
        }

        // Display results
        $this->info("=== SEMAR EC Storage Health Report ===");
        
        $totalCertificates = count($doneSubmissions);
        $this->line("Total DONE Submissions: {$totalCertificates}");
        $this->line("Missing Certificate Files: {$missingCount}");
        $this->line("Orphan Certificate Files: {$orphanCount}");
        $this->line("Invalid DONE Database Records: {$invalidDoneCount}");
        
        // Show CLI Tables
        if (!empty($missingList)) {
            $this->warn("\nMissing PDF Files:");
            $this->table(['Code', 'Status', 'Path'], $missingList);
        }

        if (!empty($orphanList)) {
            $this->warn("\nOrphan PDF Files:");
            $this->table(['File Path', 'Status'], $orphanList);
        }

        if (!empty($invalidList)) {
            $this->error("\nInvalid DONE Records:");
            $this->table(['Code', 'Status', 'Reason'], $invalidList);
        }

        // Log health check execution
        $statusMsg = "Total Certificates: {$totalCertificates}, Missing: {$missingCount}, Orphans: {$orphanCount}, Invalid DONE: {$invalidDoneCount}";
        $logSubmission = $doneSubmissions->first() ?: \App\Models\Submission::first();
        if ($logSubmission) {
            \App\Models\ActivityLog::create([
                'user_id' => null, // CLI
                'submission_id' => $logSubmission->id,
                'old_status' => 'HEALTH_CHECK',
                'new_status' => 'HEALTH_CHECK',
                'description' => "Storage health check dieksekusi via CLI command. Hasil: {$statusMsg}",
            ]);
        }

        if ($invalidDoneCount > 0) {
            $this->error("\nHealth check returned CRITICAL issues.");
            return 2; // Critical
        }

        if ($missingCount > 0 || $orphanCount > 0) {
            $this->warn("\nHealth check returned WARNING issues.");
            return 1; // Warnings
        }

        $this->info("\nHealth check completed successfully. Storage is healthy.");
        return 0; // Healthy
    }
}
