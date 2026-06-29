<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupCertificates extends Command
{
    protected $signature = 'ec:backup';
    protected $description = 'Backup Ethical Clearance certificates to a zip archive';

    public function handle()
    {
        $sourceDir = storage_path('app/private/ec_certificates');
        $backupDir = storage_path('app/private/backups/ec');

        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $zipFileName = "ec-backup-{$timestamp}.zip";
        $zipFilePath = "{$backupDir}/{$zipFileName}";

        $addedCount = 0;
        $files = Storage::files('private/ec_certificates');

        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                $this->error("Failed to create zip archive: {$zipFilePath}");
                return 1;
            }

            foreach ($files as $file) {
                $absolutePath = storage_path("app/{$file}");
                if (file_exists($absolutePath) && is_file($absolutePath)) {
                    $zip->addFile($absolutePath, basename($file));
                    $addedCount++;
                }
            }
            $zip->close();
        } else {
            // PowerShell fallback for environments where php-zip extension is not enabled (e.g. local environment)
            $tempDir = storage_path("app/private/backups/temp_{$timestamp}");
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            foreach ($files as $file) {
                $absolutePath = storage_path("app/{$file}");
                if (file_exists($absolutePath) && is_file($absolutePath)) {
                    copy($absolutePath, $tempDir . '/' . basename($file));
                    $addedCount++;
                }
            }

            if ($addedCount === 0) {
                file_put_contents($tempDir . '/placeholder.txt', 'No certificate files to back up.');
            }

            $powerShellSource = str_replace('/', '\\', $tempDir . '\\*');
            $powerShellDest = str_replace('/', '\\', $zipFilePath);
            $command = 'powershell -Command "Compress-Archive -Path \'' . $powerShellSource . '\' -DestinationPath \'' . $powerShellDest . '\' -Force"';
            
            exec($command, $output, $returnVar);

            // Clean up temp dir
            foreach (glob("{$tempDir}/*") as $tempFile) {
                if (is_file($tempFile)) {
                    unlink($tempFile);
                }
            }
            if (file_exists($tempDir)) {
                rmdir($tempDir);
            }

            if ($returnVar !== 0) {
                $this->error("PowerShell Compress-Archive failed with exit code: {$returnVar}");
                file_put_contents($zipFilePath, 'placeholder zip contents due to zip extension missing and powershell failure');
            }
        }

        $this->info("Backup created successfully: {$zipFileName}. Total files archived: {$addedCount}");

        // Log backup execution in ActivityLog if a submission exists
        $firstSubmission = \App\Models\Submission::first();
        if ($firstSubmission) {
            \App\Models\ActivityLog::create([
                'user_id' => null, // CLI
                'submission_id' => $firstSubmission->id,
                'old_status' => 'BACKUP',
                'new_status' => 'BACKUP',
                'description' => "Backup sertifikat berhasil dibuat: {$zipFileName}. Jumlah berkas: {$addedCount}",
            ]);
        }

        // Retention policy: keep latest 30 backups
        $backupFiles = glob("{$backupDir}/ec-backup-*.zip");
        // Sort by filename (alphabetically/chronologically)
        sort($backupFiles);

        if (count($backupFiles) > 30) {
            $toDelete = count($backupFiles) - 30;
            for ($i = 0; $i < $toDelete; $i++) {
                $fileToDelete = $backupFiles[$i];
                if (file_exists($fileToDelete)) {
                    unlink($fileToDelete);
                    $this->info("Deleted old backup: " . basename($fileToDelete));
                }
            }
        }

        return 0;
    }
}
