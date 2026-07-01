<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Services\WorkflowService;
use App\Enums\SubmissionStatus;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    /**
     * Menampilkan daftar seluruh proposal (Halaman Index Admin)
     */
    public function index(Request $request)
    {
        // 🟢 FIX: Menyembunyikan status DRAFT agar tidak mengotori antrean admin/sekretariat/ketua
        $query = Submission::with('student')
            ->where('status', '!=', SubmissionStatus::DRAFT)
            ->latest();

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
        $proposal->load(['documents.template', 'student', 'signatory']);

        // Ambil data user yang memiliki role 'sekretariat' untuk dropdown di kolom kanan
        $secretaries = User::whereHas('roles', function($q) {
            $q->where('name', 'sekretariat');
        })->get();

        $chairmen = User::role('ketua')->get();

        // Ambil parameter tab dari request (default ke 'details' jika kosong)
        $tab = request()->input('tab', 'details');

        return view('admin.proposals.show', [
            'proposal'    => $proposal,
            'submission'  => $proposal, 
            'secretaries' => $secretaries,
            'chairmen'    => $chairmen,
            'tab'         => $tab,
        ]);
    }

    /**
     * Menyimpan draf Ethical Clearance oleh Admin
     */
    public function storeDraft(Request $request, Submission $proposal)
    {
        if ($proposal->status !== SubmissionStatus::APPROVED) {
            abort(403, 'Draft Ethical Clearance hanya dapat dibuat jika proposal telah disetujui (APPROVED).');
        }

        $validated = $request->validate([
            'ec_number' => 'required|string|max:255',
            'signatory_id' => 'required|exists:users,id',
        ]);

        $signatory = User::findOrFail($validated['signatory_id']);
        if (!$signatory->hasRole('ketua')) {
            return back()->withErrors(['signatory_id' => 'Penandatangan harus memiliki peran ketua.'])->withInput();
        }

        $proposal->update([
            'ec_number' => $validated['ec_number'],
            'signatory_id' => $validated['signatory_id'],
        ]);

        try {
            if ($proposal->student) {
                $proposal->student->notify(new \App\Notifications\EcDraftCreated($proposal));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify student of EC Draft: " . $e->getMessage());
        }

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'submission_id' => $proposal->id,
            'old_status' => $proposal->status->value,
            'new_status' => $proposal->status->value,
            'description' => 'Admin membuat/memperbarui draf Ethical Clearance dengan nomor ' . $validated['ec_number'],
        ]);

        return redirect()->route('admin.proposals.show', $proposal)
            ->with('success', 'Draft Ethical Clearance berhasil disimpan.');
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

        if ($request->filled('title')) {
            $proposal->title = $validated['title'];
        }

        $secretaryChanged = false;
        if ($request->has('secretary_id')) {
            if ($proposal->secretary_id != $validated['secretary_id']) {
                $proposal->secretary_id = $validated['secretary_id'];
                $secretaryChanged = true;
            }
        }

        $proposal->save();

        if ($secretaryChanged && $proposal->secretary_id) {
            try {
                $secretary = User::find($proposal->secretary_id);
                if ($secretary) {
                    $secretary->notify(new \App\Notifications\SecretaryAssigned($proposal));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to notify secretary: " . $e->getMessage());
            }
        }

        $newStatus = SubmissionStatus::tryFrom($validated['status']);

        if (!$newStatus) {
            return redirect()->back()->with('error', 'Status pengajuan tidak valid.');
        }

        if ($proposal->status !== $newStatus) {
            $this->workflow->transition($proposal, $newStatus, $request->user(), 'Status diperbarui oleh Admin');
        }

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
     * Mengunduh berkas proposal utama mahasiswa
     */
    public function downloadProposal(Submission $proposal)
    {
        $document = $proposal->documents()
            ->where(function($q) {
                $q->where('doc_type', \App\Enums\DocType::PROPOSAL->value ?? 'PROPOSAL')
                  ->orWhere('original_name', 'like', '%proposal%');
            })
            ->first();

        if (!$document) {
            $document = $proposal->documents()->where('mime', '!=', 'text/url')->first();
        }

        if (!$document || $document->mime === 'text/url') {
            return back()->with('error', 'Berkas proposal utama tidak ditemukan atau hanya berupa link.');
        }

        return Storage::disk('public')->download($document->file_path, $document->original_name);
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