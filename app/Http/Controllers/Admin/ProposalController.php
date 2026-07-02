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

    /**
     * Menyimpan draf Ethical Clearance ke database
     */
    public function storeDraft(Request $request, Submission $proposal)
    {
        // Izinkan simpan jika status APPROVED atau sedang dalam tahap revisi draf WAITING_STUDENT_CONFIRMATION
        if (!in_array($proposal->status, [SubmissionStatus::APPROVED, SubmissionStatus::WAITING_STUDENT_CONFIRMATION])) {
            abort(403, 'Draft Ethical Clearance hanya dapat dibuat jika proposal telah disetujui (APPROVED) atau dalam status revisi draf.');
        }

        $validated = $request->validate([
            'ec_number' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('submissions', 'ec_number')->ignore($proposal->id)
            ],
            'signatory_id' => 'required|exists:users,id',
            'confirmed_title' => 'required|string|max:1000', // Validasi input Judul Terkonfirmasi
            'confirmed_researcher_name' => 'nullable|string|max:255',
        ], [
            'ec_number.unique' => 'Nomor Ethical Clearance sudah digunakan oleh proposal lain.'
        ]);

        $signatory = User::findOrFail($validated['signatory_id']);
        if (!$signatory->hasRole('ketua')) {
            return back()->withErrors(['signatory_id' => 'Penandatangan harus memiliki peran ketua.'])->withInput();
        }

        // Tentukan nilai baru berdasarkan data dari form yang dikirimkan Admin
        $confirmedTitle = $validated['confirmed_title'];
        $confirmedResearcherName = $request->filled('confirmed_researcher_name') 
            ? $validated['confirmed_researcher_name'] 
            : ($proposal->confirmed_researcher_name ?: optional($proposal->student)->name);

        $proposal->update([
            'ec_number' => $validated['ec_number'],
            'signatory_id' => $validated['signatory_id'],
            'confirmed_title' => $confirmedTitle,
            'confirmed_researcher_name' => $confirmedResearcherName,
        ]);

        return redirect()->route('admin.proposals.show', $proposal)
            ->with('success', 'Draft Ethical Clearance berhasil disimpan (Belum dikirim).');
    }

    /**
     * Mengirimkan draf Ethical Clearance ke mahasiswa untuk dikonfirmasi
     */
    public function sendDraft(Request $request, Submission $proposal)
    {
        // Izinkan kirim jika status APPROVED atau sedang draf ulang revisi WAITING_STUDENT_CONFIRMATION
        if (!in_array($proposal->status, [SubmissionStatus::APPROVED, SubmissionStatus::WAITING_STUDENT_CONFIRMATION])) {
            abort(403, 'Hanya draft pada proposal yang berstatus APPROVED atau dalam masa revisi draf yang dapat dikirim.');
        }

        // Update data judul dan nama jika ada perubahan instan langsung saat menekan kirim
        $updateData = [];
        if ($request->has('confirmed_title')) {
            $updateData['confirmed_title'] = $request->confirmed_title;
        }
        if ($request->has('confirmed_researcher_name')) {
            $updateData['confirmed_researcher_name'] = $request->confirmed_researcher_name;
        }
        
        if (!empty($updateData)) {
            $proposal->update($updateData);
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
            'Admin telah mengirim ulang draf Ethical Clearance hasil perbaikan ke mahasiswa untuk konfirmasi'
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

        $proposal->update(['secretary_id' => $secretary->id]);

        $this->workflow->transition($proposal, SubmissionStatus::PROCESS, auth()->user(), 'Admin menugaskan Secretariat: ' . $secretary->name);

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
        $validated = $request->validate([
            'status'       => 'required|string',
            'title'        => 'nullable|string|max:500',
            'secretary_id' => 'nullable|exists:users,id' 
        ]);

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

        Cache::forget('admin_reports_stats');

        if ($request->has('secretary_id') || !$request->has('title')) {
            return redirect()->route('admin.proposals.show', ['proposal' => $proposal->id])
                ->with('success', 'Alur pengajuan berhasil diperbarui ke tahap berikutnya!');
        }

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