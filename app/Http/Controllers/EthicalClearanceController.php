<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use Illuminate\Http\Request;

class EthicalClearanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Mengambil pengajuan milik mahasiswa yang statusnya sudah DONE
        $approvedSubmissions = $user->submissions()
            ->where('status', SubmissionStatus::DONE)
            ->latest()
            ->get();

        return view('ethical_clearance.index', compact('approvedSubmissions'));
    }
}