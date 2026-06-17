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
        $totalProtokol = 0;
        try {
            $totalProtokol = Submission::count();
        } catch (\Throwable $e) {
            $totalProtokol = 0;
        }

        // 2. Total reviewer aktif/tervalidasi
        $totalReviewer = 0;
        try {
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $roleExists = \Spatie\Permission\Models\Role::where('name', 'reviewer')
                    ->where('guard_name', 'web')
                    ->exists();
                if ($roleExists) {
                    $totalReviewer = User::role('reviewer')->count();
                }
            }
        } catch (\Throwable $e) {
            $totalReviewer = 0;
        }

        // 3. Hari kerja rata-rata (statis)
        $avgWorkDays = 14;

        // 4. Tingkat keakuratan (statis)
        $accuracyRate = 98;

        return view('landing', compact('totalProtokol', 'totalReviewer', 'avgWorkDays', 'accuracyRate'));
    }
}
