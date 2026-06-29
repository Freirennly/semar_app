<?php

namespace App\Http\Controllers;

use App\Enums\DocType;
use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Models\DocumentTemplate;
use App\Models\StatusHistory;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    /**
     * Tampilan Beranda Pengajuan (Daftar Riwayat Pengajuan & Kartu Unduhan Template)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $q = $request->input('q');

        if ($user->hasRole('student')) {
            $query = $user->submissions()->latest();
        } elseif ($user->hasAnyRole(['sekretariat', 'ketua', 'admin'])) {
            $query = Submission::with('student')->latest();
        } else {
            abort(403);
        }

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('title', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        }

        $submissions = $query->get();

        // Mengambil master berkas template aktif untuk diunduh mahasiswa
        $documentTemplates = DocumentTemplate::visible()->get();

        return view('submissions.index', compact('submissions', 'documentTemplates'));
    }

    /**
     * Menampilkan Form Pembuatan Pengajuan Baru Sisi Mahasiswa
     */
    public function create()
    {
        // Validasi hak akses otorisasi policy
        Gate::authorize('create', Submission::class);

        // AMBIL MASTER TEMPLATE DOKUMEN DARI DATABASE AGAR BISA DI-LOOP PADA BLOK KARTU VIEW
        $documentTemplates = DocumentTemplate::visible()->get();

        return view('submissions.create', compact('documentTemplates'));
    }

    /**
     * Menyimpan Draf Pengajuan Pertama Kali Beserta Seluruh Dokumen Array
     */
    public function store(Request $request)
    {
        // 1. Validasi Informasi Utama Penelitian & Array Masukan Berkas
        $request->validate([
            'title'         => 'required|string|max:255',
            'type'          => 'required|string',
            'abstract'      => 'nullable|string',
            'files.*'       => 'nullable|file|mimes:pdf|max:10240', // Diperluas menjadi 10MB sesuai visual antarmuka lapangan
            'hyperlinks.*'  => 'nullable|url',
        ]);

        // 2. Ambil Master Template untuk Validasi Aturan Wajib Atas Array Masukan
        $documentTemplates = DocumentTemplate::visible()->get();

        foreach ($documentTemplates as $template) {
            $hasFile = $request->hasFile("files.{$template->id}");
            $hasLink = $request->filled("hyperlinks.{$template->id}");

            // Jika Template bersifat WAJIB, pastikan salah satu (file/link) terisi
            if ($template->is_required && !$hasFile && !$hasLink) {
                return back()->withErrors(["files.{$template->id}" => "Dokumen '{$template->name}' wajib diisi melalui File Upload atau Hyperlink GDrive."])->withInput();
            }
        }

        // 3. Buat Data Induk Pengajuan (Submission) dengan Cast Enum Valid
        $submission = Submission::create([
            'student_id'   => auth()->id(), // Mengunci kepemilikan relasi mahasiswa pengusul
            'title'        => $request->title,
            'type'         => $request->type,
            'abstract'     => $request->abstract,
            'status'       => SubmissionStatus::NEW_PROPOSAL, // Menggunakan Enum asli terstandar proyek KEP SEMAR
            'submitted_at' => now(),
        ]);

        // 4. Proses Simpan File atau Link Secara Iteratif Berbasis ID Template
        foreach ($documentTemplates as $template) {
            $hasFile = $request->hasFile("files.{$template->id}");
            $hasLink = $request->filled("hyperlinks.{$template->id}");

            // Mengamankan pemetaan doc_type menggunakan properti code template database bawaan secara aman
            $backupEnumStr = !empty($template->code) ? $template->code : 'PROPOSAL';

            if ($hasFile) {
                $file = $request->file("files.{$template->id}");
                $path = $file->store('submissions/' . $submission->id, 'public'); 

                $submission->documents()->create([
                    'document_template_id' => $template->id,
                    'doc_type'             => $backupEnumStr, // 🟢 FIX: Mengunci kode dari model master template database secara aman
                    'file_path'            => $path,
                    'original_name'        => $file->getClientOriginalName(),
                    'mime'                 => $file->getClientMimeType(),
                    'size'                 => $file->getSize(),
                    'uploaded_by'          => auth()->id(),
                    'type'                 => 'file'
                ]);
            } 
            elseif ($hasLink) {
                $submission->documents()->create([
                    'document_template_id' => $template->id,
                    'doc_type'             => $backupEnumStr, // 🟢 FIX: Mengunci kode dari model master template database secara aman
                    'file_path'            => $request->input("hyperlinks.{$template->id}"),
                    'original_name'        => 'Link Google Drive',
                    'mime'                 => 'text/url',
                    'size'                 => 0,
                    'uploaded_by'          => auth()->id(),
                    'type'                 => 'link'
                ]);
            }
        }

        // Notify admins about new proposal
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new \App\Notifications\NewProposalSubmitted($submission));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to notify admin on new proposal: " . $e->getMessage());
            }

            $admin->notify(new \App\Notifications\SubmissionWorkflowNotification(
                'Proposal Baru Diajukan',
                "Mahasiswa {$submission->student->name} telah mengajukan proposal baru: \"{$submission->title}\".",
                $submission->id,
                route('admin.proposals.show', $submission)
            ));
        }

        return redirect()->route('submissions.index')->with('success', 'Proposal beserta seluruh berkas berhasil diajukan!');
    }

    /**
     * Tampilan Detail Pengajuan Sisi Mahasiswa (Tempat Upload Dokumen Persyaratan)
     */
    public function show(Request $request, Submission $submission)
    {
        $user = $request->user();

        if ($user->hasRole('student') && $submission->student_id !== $user->id) {
            abort(403);
        }
        if ($user->hasRole('reviewer')) {
            $isAssigned = $submission->assignments()->where('reviewer_id', $user->id)->exists();
            if (! $isAssigned) abort(403);
        }

        // Eager load seluruh relasi pendukung
        $submission->load(['documents.template', 'student', 'assignments.reviewer', 'reviews.reviewer', 'statusHistories.changer', 'decisions.decider']);

        $documentTemplates = DocumentTemplate::visible()->get();
        $uploadedTemplateIds = $submission->documents->pluck('document_template_id')->toArray();
        $tab = $request->input('tab', 'details');

        return view('submissions.show', compact('submission', 'documentTemplates', 'uploadedTemplateIds', 'tab'));
    }

    /**
     * Mengunduh file template master secara aman lewat sistem manual ID parameter kueri
     */
    public function downloadTemplate($id)
    {
        $documentTemplate = DocumentTemplate::find($id);

        if (!$documentTemplate) {
            return back()->with('error', 'Data master template tidak ditemukan di dalam sistem.');
        }

        if (!$documentTemplate->file_path || !Storage::disk('public')->exists($documentTemplate->file_path)) {
            return back()->with('error', 'Mohon maaf, fisik master berkas template tidak ditemukan di server penyimpanan lokal.');
        }

        $extension = pathinfo($documentTemplate->file_path, PATHINFO_EXTENSION);
        $safeName = str_replace(' ', '_', $documentTemplate->name) . '.' . $extension;

        return Storage::disk('public')->download($documentTemplate->file_path, $safeName);
    }

    /**
     * Membuka berkas dokumen PDF secara inline/link di tab baru browser tanpa memicu error 403
     */
    public function viewDocument(SubmissionDocument $document)
    {
        $user = auth()->user();
        $submission = $document->submission;

        // Otorisasi Keamanan Dokumen: Mahasiswa hanya boleh melihat berkas milik pengajuannya sendiri
        if ($user->hasRole('student') && $submission->student_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat dokumen ini.');
        }

        // Jika dokumen disimpan sebagai hyperlink Google Drive, alihkan langsung ke URL terkait
        if ($document->type === 'link') {
            return redirect()->away($document->file_path);
        }

        // Validasi fisik file jika tipe dokumen adalah upload file biasa
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'Fisik file PDF dokumen pendukung tidak ditemukan di server penyimpanan lokal.');
        }

        $file = Storage::disk('public')->get($document->file_path);

        return response($file, 200, [
            'Content-Type' => $document->mime ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"'
        ]);
    }

    public function edit(Submission $submission)
    {
        Gate::authorize('update', $submission);
        $documentTemplates = DocumentTemplate::visible()->get();
        return view('submissions.edit', compact('submission', 'documentTemplates'));
    }

    public function update(Request $request, Submission $submission)
    {
        Gate::authorize('update', $submission);

        $request->validate([
            'title'        => 'required|string|max:500',
            'type'         => 'required|string|max:100',
            'abstract'     => 'nullable|string|max:5000',
            'files.*'      => 'nullable|file|mimes:pdf|max:10240',
            'hyperlinks.*' => 'nullable|url',
        ]);

        $documentTemplates = DocumentTemplate::visible()->get();

        foreach ($documentTemplates as $template) {
            $hasFile = $request->hasFile("files.{$template->id}");
            $hasLink = $request->filled("hyperlinks.{$template->id}");
            $hasExisting = $submission->documents()->where('document_template_id', $template->id)->exists();

            if ($template->is_required && !$hasFile && !$hasLink && !$hasExisting) {
                return back()->withErrors(["files.{$template->id}" => "Dokumen '{$template->name}' wajib diisi melalui File Upload atau Hyperlink GDrive."])->withInput();
            }
        }

        $submission->update([
            'title'    => $request->title,
            'type'     => $request->type,
            'abstract' => $request->abstract,
        ]);

        foreach ($documentTemplates as $template) {
            $hasFile = $request->hasFile("files.{$template->id}");
            $newLink = $request->input("hyperlinks.{$template->id}");
            $oldDoc = $submission->documents()->where('document_template_id', $template->id)->first();
            
            $backupEnumStr = !empty($template->code) ? $template->code : 'PROPOSAL';

            if ($hasFile) {
                if ($oldDoc) {
                    if ($oldDoc->type === 'file') {
                        Storage::disk('public')->delete($oldDoc->file_path);
                    }
                    $oldDoc->delete();
                }

                $file = $request->file("files.{$template->id}");
                $path = $file->store('submissions/' . $submission->id, 'public'); 

                $submission->documents()->create([
                    'document_template_id' => $template->id,
                    'doc_type'             => $backupEnumStr,
                    'file_path'            => $path,
                    'original_name'        => $file->getClientOriginalName(),
                    'mime'                 => $file->getClientMimeType(),
                    'size'                 => $file->getSize(),
                    'uploaded_by'          => auth()->id(),
                    'type'                 => 'file'
                ]);
            } elseif ($newLink) {
                if (!$oldDoc || $oldDoc->type !== 'link' || $oldDoc->file_path !== $newLink) {
                    if ($oldDoc) {
                        if ($oldDoc->type === 'file') {
                            Storage::disk('public')->delete($oldDoc->file_path);
                        }
                        $oldDoc->delete();
                    }

                    $submission->documents()->create([
                        'document_template_id' => $template->id,
                        'doc_type'             => $backupEnumStr,
                        'file_path'            => $newLink,
                        'original_name'        => 'Link Google Drive',
                        'mime'                 => 'text/url',
                        'size'                 => 0,
                        'uploaded_by'          => auth()->id(),
                        'type'                 => 'link'
                    ]);
                }
            } elseif ($oldDoc && $oldDoc->type === 'link' && $request->has("hyperlinks.{$template->id}") && !$newLink) {
                $oldDoc->delete();
            }
        }

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    /**
     * Final Submit Ajuan Mahasiswa (Mendukung DRAFT & RESUBMISSION)
     */
    public function submit(Request $request, Submission $submission)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);

        // Validasi Kelengkapan Berkas Dinamis langsung dari berkas terunggah
        $requiredTemplateIds = DocumentTemplate::visible()->where('is_required', true)->pluck('id')->toArray();
        $uploadedTemplateIds = $submission->documents->pluck('document_template_id')->toArray();

        foreach ($requiredTemplateIds as $requiredId) {
            if (!in_array($requiredId, $uploadedTemplateIds)) {
                return back()->with('error', 'Gagal mengirim! Anda belum melengkapi berkas dokumen persyaratan yang bersifat Wajib.');
            }
        }

        // 🟢 FIX: Izinkan status DRAFT atau RESUBMISSION untuk melakukan pengiriman
        if (!in_array($submission->status, [SubmissionStatus::DRAFT, SubmissionStatus::RESUBMISSION])) {
            return back()->with('error', 'Pengajuan tidak dalam status yang bisa di-submit.');
        }

        // 🟢 FIX: Alihkan alur transisi status berdasarkan kondisi asal berkas
        if ($submission->status === SubmissionStatus::DRAFT) {
            $submission->update(['status' => SubmissionStatus::NEW_PROPOSAL]);
            
            StatusHistory::create([
                'submission_id' => $submission->id,
                'from_status'   => SubmissionStatus::DRAFT->value,
                'to_status'     => SubmissionStatus::NEW_PROPOSAL->value,
                'changed_by'    => $user->id,
                'note'          => 'Proposal diajukan kembali oleh mahasiswa setelah ditarik dari antrean draf.',
            ]);
        } else {
            $this->workflow->transition($submission, SubmissionStatus::REVISED, $user, 'Revisi dikirim oleh mahasiswa');
        }

        $submission->update(['submitted_at' => now()]);

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Proposal berhasil diajukan kembali ke tim KEP!');
    }

    /**
     * Menarik/Membatalkan Pengajuan (Hanya untuk status NEW_PROPOSAL & belum ditugaskan ke Sekretaris)
     */
    public function cancel(Request $request, Submission $submission)
    {
        // Pastikan hanya pemilik pengajuan yang bisa membatalkan
        if ($submission->student_id !== auth()->id()) {
            abort(403);
        }

        // Hanya bisa dibatalkan jika masih berstatus NEW_PROPOSAL
        if ($submission->status !== SubmissionStatus::NEW_PROPOSAL) {
            return back()->with('error', 'Gagal menarik pengajuan! Status pengajuan sudah tidak memungkinkan untuk dibatalkan.');
        }

        // Hanya bisa dibatalkan jika belum ditugaskan ke Sekretaris oleh Admin
        if (!is_null($submission->secretary_id)) {
            return back()->with('error', 'Gagal menarik pengajuan! Berkas sudah ditugaskan ke Sekretaris.');
        }

        try {
            $this->workflow->transition($submission, SubmissionStatus::DRAFT, $request->user(), 'Pengajuan ditarik kembali oleh mahasiswa.');
        } catch (\Throwable $e) {
            // Fallback: langsung update status jika transisi workflow gagal
            $submission->update(['status' => SubmissionStatus::DRAFT]);

            StatusHistory::create([
                'submission_id' => $submission->id,
                'from_status'   => SubmissionStatus::NEW_PROPOSAL->value,
                'to_status'     => SubmissionStatus::DRAFT->value,
                'changed_by'    => auth()->id(),
                'note'          => 'Pengajuan ditarik kembali oleh mahasiswa (fallback).',
                'created_at'    => now(),
            ]);
        }

        // Reset tanggal pengajuan agar menjadi draf bersih
        $submission->update(['submitted_at' => null]);

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil ditarik kembali dan dikembalikan ke status Draf.');
    }

    /**
     * Proses Unggah Dokumen Berkas Satuan Mahasiswa di Halaman Show (Mendukung DRAFT & RESUBMISSION)
     */
    public function uploadDocument(Request $request, Submission $submission)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);
        
        // 🟢 FIX: Buka blok akses unggah dokumen jika berstatus DRAFT maupun RESUBMISSION
        if (!in_array($submission->status, [SubmissionStatus::DRAFT, SubmissionStatus::RESUBMISSION])) {
            return back()->with('error', 'Tidak bisa upload dokumen pada status ini.');
        }

        $allowedTemplateIds = DocumentTemplate::visible()->pluck('id')->toArray();
        $allowedIdsString = implode(',', $allowedTemplateIds);

        $request->validate([
            'document_template_id' => 'required|in:' . $allowedIdsString,
            'file'                 => 'nullable|file|mimes:pdf|max:10240',
            'hyperlink'            => 'nullable|url',
        ]);

        $hasFile = $request->hasFile('file');
        $hasLink = $request->filled('hyperlink');

        if (!$hasFile && !$hasLink) {
            return back()->withErrors(['file' => 'Pilih file PDF atau masukkan link Google Drive.']);
        }

        $oldDoc = $submission->documents()->where('document_template_id', $request->document_template_id)->first();
        if ($oldDoc) {
            if ($oldDoc->type === 'file') {
                Storage::disk('public')->delete($oldDoc->file_path);
            }
            $oldDoc->delete();
        }

        $currentTemplate = DocumentTemplate::find($request->document_template_id);
        $backupEnumStr = !empty($currentTemplate->code) ? $currentTemplate->code : 'PROPOSAL';

        if ($hasFile) {
            $file = $request->file('file');
            $path = $file->store('submissions/' . $submission->id, 'public');

            $submission->documents()->create([
                'document_template_id' => $request->document_template_id,
                'doc_type'             => $backupEnumStr,
                'file_path'            => $path,
                'original_name'        => $file->getClientOriginalName(),
                'mime'                 => $file->getClientMimeType(),
                'size'                 => $file->getSize(),
                'uploaded_by'          => $user->id,
                'type'                 => 'file'
            ]);
        } else {
            $submission->documents()->create([
                'document_template_id' => $request->document_template_id,
                'doc_type'             => $backupEnumStr,
                'file_path'            => $request->hyperlink,
                'original_name'        => 'Link Google Drive',
                'mime'                 => 'text/url',
                'size'                 => 0,
                'uploaded_by'          => $user->id,
                'type'                 => 'link'
            ]);
        }

        return back()->with('success', 'Dokumen berkas berhasil diupload.');
    }

    /**
     * Proses Hapus Dokumen Berkas Satuan Mahasiswa di Halaman Show (Mendukung DRAFT & RESUBMISSION)
     */
    public function deleteDocument(Request $request, Submission $submission, SubmissionDocument $document)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);
        
        if (!in_array($submission->status, [SubmissionStatus::DRAFT, SubmissionStatus::RESUBMISSION])) {
            return back()->with('error', 'Tidak bisa menghapus dokumen pada status ini.');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Proses Hapus Induk Data Pengajuan (DRAFT) – Rute: DELETE /submissions/{submission}
     */
    public function destroy(Submission $submission)
    {
        Gate::authorize('delete', $submission);

        // Hapus semua berkas fisik PDF dokumen pendukung yang ada di folder local storage
        foreach ($submission->documents as $document) {
            if ($document->type === 'file' && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
        }

        // Hapus data relasi dokumen anak di DB, lalu hapus induk data pengajuannya
        $submission->documents()->delete();
        $submission->delete();

        return redirect()->route('submissions.index')
            ->with('success', 'Pengajuan beserta seluruh dokumen pendukung di dalamnya berhasil dihapus permanen.');
    }

    public function confirmEcData(Request $request, Submission $submission)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);

        if ($submission->status !== SubmissionStatus::APPROVED) {
            return back()->with('error', 'Status pengajuan tidak valid untuk konfirmasi saat ini.');
        }

        if (empty($submission->ec_number)) {
            return back()->with('error', 'Draft Ethical Clearance belum dibuat oleh Admin.');
        }

        $request->validate([
            'confirmed_title'           => 'required|string|max:500',
            'confirmed_researcher_name' => 'required|string|max:255',
        ]);

        $submission->update([
            'confirmed_title'           => $request->confirmed_title,
            'confirmed_researcher_name' => $request->confirmed_researcher_name,
        ]);

        $this->workflow->transition($submission, SubmissionStatus::WAITING_SIGNATURE, $user, 'Peneliti telah mengonfirmasi draf sertifikat EC.');

        return back()->with('success', 'Draf sertifikat berhasil dikonfirmasi. Saat ini menunggu tanda tangan dari Ketua KEP.');
    }

    public function sign(Request $request, Submission $submission)
    {
        $user = $request->user();
        if (! $user->hasRole('ketua')) abort(403);
        if ($submission->signatory_id !== $user->id) abort(403);

        if ($submission->status !== SubmissionStatus::WAITING_SIGNATURE) {
            return back()->with('error', 'Status pengajuan tidak valid untuk ditandatangani saat ini.');
        }

        if (empty($submission->ec_number) ||
            empty($submission->signatory_id) ||
            empty($submission->confirmed_title) ||
            empty($submission->confirmed_researcher_name)) {
            return back()->with('error', 'Dokumen Ethical Clearance belum lengkap untuk ditandatangani.');
        }

        // Pemicu pembuatan file fisik PDF dan QR-Code beralih ke CertificateGenerator secara dinamis
        $certificateService = new \App\Services\CertificateGenerator();
        $generatedPath = $certificateService->generate($submission);

        // Perbarui rekam path sertifikat privat ke database submissions
        $submission->update(['ec_certificate_path' => $generatedPath]);

        $this->workflow->transition($submission, SubmissionStatus::DONE, $user, 'Sertifikat Laik Etik telah ditandatangani oleh Ketua KEP.');

        return back()->with('success', 'Sertifikat Laik Etik berhasil ditandatangani.');
    }

    /**
     * Menyelaraskan kueri unduhan sertifikat di menu Ethical Clearance 
     */
    public function downloadEc(Request $request, Submission $submission)
    {
        $user = $request->user();
        
        if ($user->hasRole('student') && $submission->student_id !== $user->id) {
            abort(403);
        }

        if ($submission->status !== SubmissionStatus::DONE) {
            return back()->with('error', 'Sertifikat Laik Etik belum diterbitkan.');
        }

        $safeEcNumber = preg_replace('/[^A-Za-z0-9_\-]/', '_', $submission->ec_number);
        $fileName     = 'private/ec_certificates/EC-' . $safeEcNumber . '.pdf';

        if (empty($fileName) || !Storage::exists($fileName)) {
            return back()->with('error', 'Mohon maaf, file fisik sertifikat PDF tidak ditemukan di dalam sistem.');
        }

        return Storage::download($fileName, 'Ethical_Clearance_' . $safeEcNumber . '.pdf');
    }

    public function downloadCertificate(Request $request, Submission $submission)
    {
        $user = $request->user();

        $allowed = false;
        if ($user->hasRole('admin') || $user->hasRole('sekretariat')) {
            $allowed = true;
        } elseif ($user->hasRole('student') && $submission->student_id === $user->id) {
            $allowed = true;
        } elseif ($user->hasRole('ketua') && $submission->signatory_id === $user->id) {
            $allowed = true;
        }

        if (! $allowed) {
            abort(403);
        }

        if (empty($submission->ec_certificate_path) || ! Storage::exists($submission->ec_certificate_path)) {
            return back()->with('error', 'Mohon maaf, berkas fisik sertifikat belum terbit di sistem.');
        }

        // Log the download event
        \App\Models\ActivityLog::create([
            'user_id'       => $user->id,
            'submission_id' => $submission->id,
            'old_status'    => $submission->status->value,
            'new_status'    => $submission->status->value,
            'description'   => "Sertifikat diunduh oleh {$user->name}. IP: " . $request->ip(),
        ]);

        return Storage::download($submission->ec_certificate_path, 'EC-' . $submission->code . '.pdf');
    }
}