<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        // Load settings, default to empty string if not found
        $settings = [
            'application_name' => Setting::getVal('application_name', 'SEMAR System'),
            'institution_name' => Setting::getVal('institution_name', 'Universitas Contoh'),
            'contact_email' => Setting::getVal('contact_email', 'admin@semar.ac.id'),
            'maintenance_mode' => Setting::getVal('maintenance_mode', '0'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update pengaturan sistem (HTTP PUT, route name tetap admin.settings.store untuk backward compatibility)
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'application_name' => 'required|string|max:255',
            'institution_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        // Map maintenance_mode to '1' or '0'
        $validated['maintenance_mode'] = $request->has('maintenance_mode') ? '1' : '0';

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            Cache::forget("setting_{$key}");
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
