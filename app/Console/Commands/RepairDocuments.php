<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RepairDocuments extends Command
{
    protected $signature = 'documents:repair {--force : Actually perform repairs instead of dry run}';

    protected $description = 'Scan and optionally repair document integrity issues';

    public function handle(): int
    {
        $isForce = $this->option('force');

        if ($isForce) {
            $this->warn('⚡ Running in FORCE mode. Records will be modified.');
        } else {
            $this->info('🔍 Running in DRY RUN mode. No records will be modified.');
        }

        $this->newLine();

        $brokenFiles = 0;
        $orphanRecords = 0;
        $repaired = 0;

        $documents = SubmissionDocument::with('template')->get();

        foreach ($documents as $doc) {
            // Check broken file paths (non-link documents)
            if ($doc->mime !== 'text/url') {
                if (!Storage::exists($doc->file_path)) {
                    $brokenFiles++;
                    $this->line("  ❌ Broken file: Document #{$doc->id} → {$doc->file_path}");

                    if ($isForce) {
                        $doc->delete();
                        $repaired++;
                        $this->line("     → Removed orphan record #{$doc->id}");
                    }
                }
            }

            // Check missing template reference
            if ($doc->document_template_id && !$doc->template) {
                $orphanRecords++;
                $this->line("  ⚠️  Orphan template ref: Document #{$doc->id} → Template ID {$doc->document_template_id} (missing)");

                if ($isForce) {
                    $doc->update(['document_template_id' => null]);
                    $repaired++;
                    $this->line("     → Cleared template reference for #{$doc->id}");
                }
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Broken Files Found', $brokenFiles],
                ['Orphan Records Found', $orphanRecords],
                ['Records Repaired', $isForce ? $repaired : 'N/A (dry run)'],
            ]
        );

        // Write ActivityLog
        if ($isForce && $repaired > 0) {
            ActivityLog::create([
                'user_id' => null,
                'submission_id' => null,
                'old_status' => null,
                'new_status' => 'DOCUMENT_REPAIR',
                'description' => "Perbaikan dokumen dijalankan: {$repaired} record diperbaiki ({$brokenFiles} file rusak, {$orphanRecords} referensi orphan).",
            ]);
        }

        $totalIssues = $brokenFiles + $orphanRecords;

        if ($totalIssues === 0) {
            $this->info('✅ No issues found. All documents are healthy.');
            return 0;
        }

        if (!$isForce) {
            $this->newLine();
            $this->warn("Found {$totalIssues} issue(s). Run with --force to repair.");
        } else {
            $this->newLine();
            $this->info("Repaired {$repaired} record(s).");
        }

        return $isForce ? 0 : 1;
    }
}
