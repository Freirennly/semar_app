<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function show(Request $request, $token)
    {
        // Gunakan eager loading untuk relasi yang akan ditampilkan di publik
        $submission = Submission::with(['student', 'signatory'])->where('verification_token', $token)->first();

        if (! $submission || $submission->status->value !== 'DONE') {
            return response()->view('verification.show', [
                'isValid' => false,
                'message' => !$submission ? 'Verification Token tidak ditemukan.' : 'Dokumen belum diterbitkan.'
            ], 404);
        }

        // Log the verification
        \App\Models\ActivityLog::create([
            'submission_id' => $submission->id,
            'old_status' => $submission->status->value,
            'new_status' => $submission->status->value,
            'description' => 'QR Code Validasi Verifikasi Publik diakses. IP: ' . $request->ip(),
        ]);

        return view('verification.show', [
            'isValid' => true,
            'submission' => $submission,
            'verificationDate' => now()->translatedFormat('d F Y, H:i:s T'),
            'verificationId' => 'VER-' . strtoupper(substr($token, 0, 8))
        ]);
    }
}
