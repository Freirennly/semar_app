<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Show the application landing page.
     */
    public function index()
    {
        // 1. Total jumlah proposal/protokol penelitian
        $totalProtokol = Submission::count();

        // 2. Total reviewer aktif/tervalidasi
        $totalReviewer = User::role('reviewer')->count();

        // 3. Hari kerja rata-rata (statis)
        $avgWorkDays = 14;

        // 4. Tingkat keakuratan (statis)
        $accuracyRate = 98;

        return view('landing', compact('totalProtokol', 'totalReviewer', 'avgWorkDays', 'accuracyRate'));
    }
}
