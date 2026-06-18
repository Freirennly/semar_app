<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Services\CertificateGenerator;
use Illuminate\Support\Facades\Storage;

class RegenerateCertificates extends Command
{
    protected $signature = 'ec:regenerate {submission?}';
    protected $description = 'Regenerate missing Ethical Clearance certificate PDF files for DONE submissions';

    public function handle()
    {
        $generator = app(CertificateGenerator::class);
        $submissionId = $this->argument('submission');

        $query = Submission::where('status', SubmissionStatus::DONE);
        if ($submissionId) {
            $query->where('id', $submissionId);
        }

        $submissions = $query->get();
        $count = 0;

        foreach ($submissions as $submission) {
            $path = $submission->ec_certificate_path;
            if (empty($path) || !Storage::exists($path)) {
                $this->info("Regenerating certificate for: {$submission->code}");
                $newPath = $generator->generate($submission);
                $submission->ec_certificate_path = $newPath;
                $submission->save();
                $count++;

                // Log the regeneration event
                \App\Models\ActivityLog::create([
                    'user_id' => null, // CLI
                    'submission_id' => $submission->id,
                    'old_status' => $submission->status->value,
                    'new_status' => $submission->status->value,
                    'description' => "Sertifikat laik etik diregenerasi secara otomatis via CLI command. Path: {$newPath}",
                ]);
            }
        }

        $this->info("Regeneration complete. Total certificates regenerated: {$count}.");
    }
}
