<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Submission;
use App\Services\DocumentIntegrityService;
use Illuminate\Console\Command;

class ValidateDocuments extends Command
{
    protected $signature = 'documents:validate';

    protected $description = 'Scan all submissions for document integrity issues';

    public function handle(DocumentIntegrityService $service): int
    {
        $this->info('🔍 Scanning all submissions for document integrity issues...');
        $this->newLine();

        $result = $service->validateAll();

        // Summary table
        $this->table(
            ['Metric', 'Value'],
            [
                ['Submissions Checked', $result['submissions_checked']],
                ['Missing Required', $result['missing_required']],
                ['Broken Files', $result['broken_files']],
                ['Invalid Links', $result['invalid_links']],
                ['Orphan Records', $result['orphan_records']],
                ['Duplicate Templates', $result['duplicate_templates']],
                ['Health Score', $result['health_score'] . '/100'],
            ]
        );

        // Detail errors
        if (!empty($result['errors'])) {
            $this->newLine();
            $this->error('Errors:');
            foreach ($result['errors'] as $error) {
                $this->line("  ❌ {$error}");
            }
        }

        // Detail warnings
        if (!empty($result['warnings'])) {
            $this->newLine();
            $this->warn('Warnings:');
            foreach ($result['warnings'] as $warning) {
                $this->line("  ⚠️  {$warning}");
            }
        }

        $totalIssues = $result['missing_required'] + $result['broken_files'] + $result['invalid_links'] + $result['orphan_records'] + $result['duplicate_templates'];

        // Write ActivityLog
        $firstSubmission = Submission::first();
        if ($firstSubmission) {
            ActivityLog::create([
                'user_id' => null,
                'submission_id' => null,
                'old_status' => null,
                'new_status' => 'DOCUMENT_VALIDATION',
                'description' => "Validasi dokumen dijalankan: {$result['submissions_checked']} pengajuan diperiksa, {$totalIssues} masalah ditemukan. Skor kesehatan: {$result['health_score']}/100.",
            ]);
        }

        if ($totalIssues > 0) {
            $this->newLine();
            $this->error("Found {$totalIssues} issue(s). Exit code: 1");
            return 1;
        }

        $this->newLine();
        $this->info('✅ All submissions are healthy. No issues found.');
        return 0;
    }
}
