<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateGenerator
{
    public function generate(Submission $submission): string
    {
        if (empty($submission->verification_token)) {
            $submission->verification_token = (string) \Illuminate\Support\Str::uuid();
            $submission->save();
        }

        // 1. Generate temporary signed URL (valid for 10 years to satisfy requirement)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addYears(10),
            ['token' => $submission->verification_token]
        );

        // 2. Generate QR code SVG and embed as base64
        $qrCodeSvg = QrCode::format('svg')
            ->size(120)
            ->margin(1)
            ->generate($verificationUrl);
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        // 3. Render and Generate PDF
        $signedDate = $submission->signed_at ? $submission->signed_at->format('d F Y') : now()->format('d F Y');
        
        $pdf = Pdf::loadView('pdf.certificate', [
            'submission' => $submission,
            'qrCode' => $qrCodeBase64,
            'signedDate' => $signedDate,
        ]);

        // Clean filename (replace slashes to avoid directory traversal or write issues)
        $safeEcNumber = preg_replace('/[^A-Za-z0-9_\-]/', '_', $submission->ec_number);
        $fileName = 'private/ec_certificates/EC-' . $safeEcNumber . '.pdf';

        // 4. Store PDF
        Storage::put($fileName, $pdf->output());

        return $fileName;
    }
}
