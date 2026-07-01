<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateGenerator
{
    /**
     * Membuat dokumen sertifikat Laik Etik (EC) berformat PDF secara otomatis
     * lengkap dengan QR Code verifikasi bertanda tangan digital terenkripsi.
     */
    public function generate(Submission $submission): string
    {
        // 1. Validasi token verifikasi, buat UUID baru jika data masih kosong
        if (empty($submission->verification_token)) {
            $submission->verification_token = (string) Str::uuid();
            $submission->save();
        }

        // 2. Buat rute verifikasi bertanda tangan (Temporary Signed Route) yang valid selama 10 tahun
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addYears(10),
            ['token' => $submission->verification_token]
        );

        // 3. Generate QR code berformat SVG dan enkripsi ke dalam bentuk Base64 Data URI
        // Perbaikan: Memastikan data berupa string murni dengan membungkus URL ke string atau memanggil method generate secara runtut.
        $qrCodeSvg = QrCode::format('svg')
            ->size(120)
            ->margin(1)
            ->generate((string) $verificationUrl);
            
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        // 4. Sinkronisasi format penulisan tanggal penandatanganan sertifikat
        $signedDate = $submission->signed_at 
            ? ($submission->signed_at instanceof \Carbon\Carbon ? $submission->signed_at->format('d F Y') : \Carbon\Carbon::parse($submission->signed_at)->format('d F Y'))
            : now()->format('d F Y');
        
        // 5. Render view template dokumen ke dalam format PDF DomPDF
        $pdf = Pdf::loadView('pdf.certificate', [
            'submission' => $submission,
            'qrCode'     => $qrCodeBase64,
            'signedDate' => $signedDate,
        ]);

        // 6. Normalisasi penamaan file dari karakter ilegal agar terhindar dari write issues / directory traversal
        $safeEcNumber = preg_replace('/[^A-Za-z0-9_\-]/', '_', $submission->ec_number);
        $fileName     = 'private/ec_certificates/EC-' . $safeEcNumber . '.pdf';

        // 7. Simpan dokumen fisik PDF ke dalam storage lokal privat aplikasi
        Storage::put($fileName, $pdf->output());

        return $fileName;
    }
}