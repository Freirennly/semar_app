<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class SecretariatController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('sekretariat')->latest();

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('position', 'like', "%{$searchTerm}%");
            });
        }

        $secretariat = $query->paginate(15)->withQueryString();

        return view('admin.secretariat.index', compact('secretariat'));
    }

    public function create()
    {
        return view('admin.secretariat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'position' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        $user = User::create($validated);
        $user->assignRole('sekretariat');

        Cache::forget('admin_reports_stats');

        return redirect()->route('admin.secretariat.index')->with('success', 'Anggota sekretariat berhasil ditambahkan.');
    }

    public function edit(User $secretariat)
    {
        return view('admin.secretariat.edit', compact('secretariat'));
    }

    public function update(Request $request, User $secretariat)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($secretariat->id)],
            'password' => 'nullable|string|min:8',
            'position' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $secretariat->update($validated);

        Cache::forget('admin_reports_stats');

        return redirect()->route('admin.secretariat.index')->with('success', 'Data sekretariat berhasil diperbarui.');
    }

    public function destroy(User $secretariat)
    {
        $secretariat->delete();
        Cache::forget('admin_reports_stats');
        return redirect()->route('admin.secretariat.index')->with('success', 'Anggota sekretariat berhasil dihapus.');
    }
}
