<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

class DocumentIntegrityService
{
    public const CACHE_KEY = 'document_integrity_metrics';

    /**
     * Validate all documents for a given submission.
     *
     * @return array{errors: array, warnings: array, health_score: int, missing_required: int, broken_files: int, invalid_links: int, orphan_records: int, duplicate_templates: int}
     */
    public function validateSubmissionDocuments(Submission $submission): array
    {
        $errors = [];
        $warnings = [];
        $missingRequired = 0;
        $brokenFiles = 0;
        $invalidLinks = 0;
        $orphanRecords = 0;
        $duplicateTemplates = 0;

        $documents = $submission->documents()->with('template')->get();

        // 1. Missing required templates
        $requiredTemplates = DocumentTemplate::where('is_required', true)
            ->where('is_shown', true)
            ->where('is_archived', false)
            ->get();

        foreach ($requiredTemplates as $template) {
            $hasDoc = $documents->where('document_template_id', $template->id)->isNotEmpty();
            if (!$hasDoc) {
                $missingRequired++;
                $errors[] = "Dokumen wajib '{$template->name}' (kode: {$template->code}) belum diunggah.";
            }
        }

        // 2. Broken files and invalid links
        foreach ($documents as $doc) {
            if ($doc->mime === 'text/url') {
                // Link validation
                if (!filter_var($doc->file_path, FILTER_VALIDATE_URL)) {
                    $invalidLinks++;
                    $warnings[] = "Link tidak valid pada dokumen #{$doc->id}: {$doc->file_path}";
                }
            } else {
                // Physical file validation
                if (!Storage::exists($doc->file_path)) {
                    $brokenFiles++;
                    $errors[] = "File fisik tidak ditemukan untuk dokumen #{$doc->id}: {$doc->file_path}";
                }
            }
        }

        // 3. Orphan records (missing template reference)
        foreach ($documents as $doc) {
            if ($doc->document_template_id && !$doc->template) {
                $orphanRecords++;
                $errors[] = "Dokumen #{$doc->id} mereferensikan template yang sudah dihapus (ID: {$doc->document_template_id}).";
            }
        }

        // 4. Duplicate template documents
        $grouped = $documents->groupBy('document_template_id');
        foreach ($grouped as $templateId => $docs) {
            if ($templateId && $docs->count() > 1) {
                $duplicateTemplates++;
                $warnings[] = "Template ID {$templateId} memiliki {$docs->count()} dokumen duplikat pada submission #{$submission->id}.";
            }
        }

        // 5. Archived template usage anomaly
        foreach ($documents as $doc) {
            if ($doc->template && $doc->template->is_archived) {
                $warnings[] = "Dokumen #{$doc->id} menggunakan template yang sudah diarsipkan: '{$doc->template->name}'.";
            }
        }

        // Calculate health score
        $totalIssues = $missingRequired + $brokenFiles + $invalidLinks + $orphanRecords + $duplicateTemplates;
        $healthScore = max(0, 100 - ($totalIssues * 10));

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'health_score' => $healthScore,
            'missing_required' => $missingRequired,
            'broken_files' => $brokenFiles,
            'invalid_links' => $invalidLinks,
            'orphan_records' => $orphanRecords,
            'duplicate_templates' => $duplicateTemplates,
        ];
    }

    /**
     * Run integrity validation across ALL submissions and aggregate results.
     */
    public function validateAll(): array
    {
        return \Illuminate\Support\Facades\Cache::remember(self::CACHE_KEY, 3600, function () {
            $submissions = Submission::with('documents.template')->get();

            $globalErrors = [];
            $globalWarnings = [];
            $totalMissing = 0;
            $totalBroken = 0;
            $totalInvalidLinks = 0;
            $totalOrphans = 0;
            $totalDuplicates = 0;

            foreach ($submissions as $submission) {
                $result = $this->validateSubmissionDocuments($submission);
                $globalErrors = array_merge($globalErrors, $result['errors']);
                $globalWarnings = array_merge($globalWarnings, $result['warnings']);
                $totalMissing += $result['missing_required'];
                $totalBroken += $result['broken_files'];
                $totalInvalidLinks += $result['invalid_links'];
                $totalOrphans += $result['orphan_records'];
                $totalDuplicates += $result['duplicate_templates'];
            }

            $totalIssues = $totalMissing + $totalBroken + $totalInvalidLinks + $totalOrphans + $totalDuplicates;
            $healthScore = $submissions->isEmpty() ? 100 : max(0, 100 - ($totalIssues * 5));

            return [
                'submissions_checked' => $submissions->count(),
                'errors' => $globalErrors,
                'warnings' => $globalWarnings,
                'health_score' => $healthScore,
                'missing_required' => $totalMissing,
                'broken_files' => $totalBroken,
                'invalid_links' => $totalInvalidLinks,
                'orphan_records' => $totalOrphans,
                'duplicate_templates' => $totalDuplicates,
            ];
        });
    }
}
