<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function show(Request $request, $token)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Tautan verifikasi tidak sah atau telah kedaluwarsa.');
        }

        $submission = Submission::where('verification_token', $token)->first();

        if (! $submission) {
            abort(404, 'Sertifikat tidak ditemukan.');
        }

        // Log verification access: IP address, verification token, actor (guest/auth), timestamp (created_at)
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'submission_id' => $submission->id,
            'old_status' => $submission->status->value,
            'new_status' => $submission->status->value,
            'description' => "Verifikasi sertifikat diakses secara publik. Token: {$token}. IP: {$request->ip()}",
        ]);

        if ($submission->status->value !== 'DONE') {
            return view('verification.show', [
                'isValid' => false,
                'message' => 'Certificate Not Valid'
            ]);
        }

        return view('verification.show', [
            'isValid' => true,
            'submission' => $submission
        ]);
    }
}
