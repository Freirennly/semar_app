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
        $proposal->load(['documents.template', 'student', 'signatory']);

        // Ambil data user yang memiliki role 'sekretariat' untuk dropdown di kolom kanan
        $secretaries = User::whereHas('roles', function($q) {
            $q->where('name', 'sekretariat');
        })->get();

        $chairmen = User::role('ketua')->get();

        // Ambil parameter tab dari request (default ke 'details' jika kosong)
        $tab = request()->input('tab', 'details');

        // Kalkulasi state UI untuk form Draft EC
        $latestHistory = $proposal->statusHistories()->latest()->first();
        $isRevisionRequested = $latestHistory && str_contains($latestHistory->note ?? '', 'Permintaan perbaikan Draf EC');
        $isDraftReadonly = $proposal->status->value === SubmissionStatus::WAITING_STUDENT_CONFIRMATION->value && !$isRevisionRequested;

        return view('admin.proposals.show', [
            'proposal'    => $proposal,
            'submission'  => $proposal, 
            'secretaries' => $secretaries,
            'chairmen'    => $chairmen,
            'tab'         => $tab,
            'isDraftReadonly' => $isDraftReadonly,
        ]);
    }

    public function storeDraft(Request $request, Submission $proposal)
    {
        if ($proposal->status !== SubmissionStatus::APPROVED) {
            abort(403, 'Draft Ethical Clearance hanya dapat dibuat jika proposal telah disetujui (APPROVED).');
        }

        $validated = $request->validate([
            'ec_number' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('submissions', 'ec_number')->ignore($proposal->id)
            ],
            'signatory_id' => 'required|exists:users,id',
        ], [
            'ec_number.unique' => 'Nomor Ethical Clearance sudah digunakan oleh proposal lain.'
        ]);

        $signatory = User::findOrFail($validated['signatory_id']);
        if (!$signatory->hasRole('ketua')) {
            return back()->withErrors(['signatory_id' => 'Penandatangan harus memiliki peran ketua.'])->withInput();
        }

        // Auto-fill confirmed_title and confirmed_researcher_name if empty to prepare draft
        if (empty($proposal->confirmed_title)) {
            $proposal->confirmed_title = $proposal->title;
        }
        if (empty($proposal->confirmed_researcher_name)) {
            $proposal->confirmed_researcher_name = optional($proposal->student)->name;
        }

        $proposal->update([
            'ec_number' => $validated['ec_number'],
            'signatory_id' => $validated['signatory_id'],
            'confirmed_title' => $proposal->confirmed_title,
            'confirmed_researcher_name' => $proposal->confirmed_researcher_name,
        ]);

        return redirect()->route('admin.proposals.show', $proposal)
            ->with('success', 'Draft Ethical Clearance berhasil disimpan (Belum dikirim).');
    }

    public function sendDraft(Request $request, Submission $proposal)
    {
        if ($proposal->status !== SubmissionStatus::APPROVED) {
            abort(403, 'Hanya draft pada proposal yang berstatus APPROVED yang dapat dikirim.');
        }

        if (empty($proposal->ec_number) || empty($proposal->signatory_id) || empty($proposal->confirmed_title) || empty($proposal->confirmed_researcher_name)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'draft' => 'Draft Ethical Clearance belum lengkap (Pastikan Nomor EC, Penandatangan, Judul, dan Peneliti terisi).'
            ]);
        }

        $this->workflow->transition(
            $proposal, 
            SubmissionStatus::WAITING_STUDENT_CONFIRMATION, 
            auth()->user(), 
            'Admin telah mengirim draf Ethical Clearance ke mahasiswa untuk konfirmasi'
        );

        return redirect()->route('admin.proposals.show', $proposal)
            ->with('success', 'Draft Ethical Clearance berhasil dikirim ke mahasiswa untuk konfirmasi.');
    }

    /**
     * Menetapkan sekretariat untuk proposal baru
     */
    public function assignSecretary(Request $request, Submission $proposal)
    {
        if ($proposal->status !== SubmissionStatus::NEW_PROPOSAL) {
            return back()->with('error', 'Penugasan sekretariat hanya dapat dilakukan pada proposal baru (NEW_PROPOSAL).');
        }

        $validated = $request->validate([
            'secretary_id' => 'required|exists:users,id',
        ]);

        $secretary = User::findOrFail($validated['secretary_id']);
        if (!$secretary->hasRole('sekretariat')) {
            return back()->with('error', 'User yang dipilih tidak memiliki peran sekretariat.');
        }

        // Simpan secretary_id
        $proposal->update(['secretary_id' => $secretary->id]);

        // Transisi menggunakan WorkflowService agar ActivityLog & StatusHistory tercatat konsisten
        $this->workflow->transition($proposal, SubmissionStatus::PROCESS, auth()->user(), 'Admin menugaskan Sekretariat: ' . $secretary->name);

        // Kirim notifikasi
        try {
            $secretary->notify(new \App\Notifications\ProposalAssignedToSecretary($proposal));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify secretary: " . $e->getMessage());
        }

        return redirect()->route('admin.proposals.show', $proposal)
            ->with('success', 'Sekretariat berhasil ditugaskan dan proposal diproses.');
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
            $document = $proposal->documents()->where('type', 'file')->first();
        }

        if (!$document || $document->type !== 'file') {
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