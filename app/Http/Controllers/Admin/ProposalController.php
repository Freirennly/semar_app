<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProposalController extends Controller
{
    /**
     * Menampilkan daftar seluruh proposal (Halaman Index Admin)
     */
    public function index(Request $request)
    {
        $query = Submission::with('student')->latest();

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhereHas('student', function($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proposals = $query->paginate(15)->withQueryString();

        return view('admin.proposals.index', compact('proposals'));
    }

    /**
     * Menampilkan Detail Proposal Versi Admin (Halaman 2 Kolom Premium)
     */
    public function show(Submission $proposal)
    {
        // Eager load dokumen beserta master template dari DB, dan data mahasiswa pengusul
        $proposal->load(['documents.template', 'student']);

        // Ambil data user yang memiliki role 'sekretariat' untuk dropdown di kolom kanan
        $secretaries = User::whereHas('roles', function($q) {
            $q->where('name', 'sekretariat');
        })->get();

        // Ambil parameter tab dari request (default ke 'details' jika kosong)
        $tab = request()->input('tab', 'details');

        return view('admin.proposals.show', [
            'proposal'    => $proposal,
            'submission'  => $proposal, 
            'secretaries' => $secretaries,
            'tab'         => $tab,
        ]);
    }

    /**
     * Halaman Edit Formal (Jalur Belakang Admin)
     */
    public function edit(Submission $proposal)
    {
        return view('admin.proposals.edit', compact('proposal'));
    }

    /**
     * Memproses Perbaruan Data dari Halaman Edit maupun Halaman Show Action
     */
    public function update(Request $request, Submission $proposal)
    {
        // Validasi dibuat fleksibel karena title hanya wajib jika datang dari form edit biasa
        $validated = $request->validate([
            'status'       => 'required|string',
            'title'        => 'nullable|string|max:500',
            'secretary_id' => 'nullable|exists:users,id' 
        ]);

        // Ambil data lama jika title tidak dikirim (berarti eksekusi datang dari tombol cepat kolom kanan)
        if (!$request->filled('title')) {
            $validated['title'] = $proposal->title;
        }

        // Jalankan update ke database
        $proposal->update($validated);

        // Bersihkan cache statistik report admin
        Cache::forget('admin_reports_stats');

        // LOGIKA REDIRECT FIX: Dipaksa melempar ID secara eksplisit ke rute Admin agar tidak tabrakan dengan rute global
        if ($request->has('secretary_id') || !$request->has('title')) {
            return redirect()->route('admin.proposals.show', ['proposal' => $proposal->id])
                ->with('success', 'Alur pengajuan berhasil diperbarui ke tahap berikutnya!');
        }

        // Jika datang dari form edit biasa, kembalikan ke index utama
        return redirect()->route('admin.proposals.index')
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    /**
     * Menghapus Proposal Permanen
     */
    public function destroy(Submission $proposal)
    {
        $proposal->delete();
        
        Cache::forget('admin_reports_stats');
        
        return redirect()->route('admin.proposals.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}