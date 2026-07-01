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
        $documentTemplates = DocumentTemplate::visible()->orderBy('id')->get();

        return view('submissions.index', compact('submissions', 'documentTemplates'));
    }

    /**
     * Menampilkan Form Pembuatan Pengajuan Baru Sisi Mahasiswa
     */
    public function create()
    {
        // Validasi hak akses otorisasi policy
        $this->authorize('create', Submission::class);

        // AMBIL MASTER TEMPLATE DOKUMEN DARI DATABASE AGAR BISA DI-LOOP PADA BLOK KARTU VIEW
        $documentTemplates = DocumentTemplate::visible()->orderBy('id')->get();

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
            'files.*'       => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'hyperlinks.*'  => 'nullable|url',
        ]);

        // 2. Ambil Master Template untuk Validasi Aturan Wajib Atas Array Masukan
        $documentTemplates = DocumentTemplate::visible()->orderBy('id')->get();

        foreach ($documentTemplates as $template) {
            $hasFile = $request->hasFile("files.{$template->id}");
            $hasLink = $request->filled("hyperlinks.{$template->id}");

            // Jika Template bersifat WAJIB, pastikan salah satu (file/link) terisi
            if ($template->is_required && !$hasFile && !$hasLink) {
                return back()->withErrors(["files.{$template->id}" => "Dokumen '{$template->name}' wajib diisi melalui File Upload atau Link Dokumen."])->withInput();
            }

            if ($hasFile && $hasLink) {
                return back()->withErrors(["files.{$template->id}" => "Dokumen '{$template->name}' tidak boleh diisi keduanya (File dan Link). Silakan pilih salah satu."])->withInput();
            }
        }

        // 3. Buat Data Induk Pengajuan (Submission) dengan Cast Enum Valid
        $submission = Submission::create([
            'student_id' => auth()->id(), // Mengunci kepemilikan relasi mahasiswa pengusul
            'title'      => $request->title,
            'type'       => $request->type,
            'abstract'   => $request->abstract,
            'status'     => SubmissionStatus::NEW_PROPOSAL, // Menggunakan Enum asli terstandar proyek KEP SEMAR
            'submitted_at' => now(),
        ]);

        // 4. Proses Simpan File atau Link Secara Iteratif Berbasis ID Template
        foreach ($documentTemplates as $template) {
            $hasFile = $request->hasFile("files.{$template->id}");
            $hasLink = $request->filled("hyperlinks.{$template->id}");

            if ($hasFile) {
                $file = $request->file("files.{$template->id}");
                $path = $file->store('submissions/' . $submission->id, 'public'); 

                $submission->documents()->create([
                    'document_template_id' => $template->id,
                    'doc_type'             => $template->code,
                    'file_path'            => $path,
                    'original_name'        => $file->getClientOriginalName(),
                    'mime'                 => $file->getClientMimeType(),
                    'size'                 => $file->getSize(),
                    'uploaded_by'          => auth()->id(),
                ]);
            } 
            elseif ($hasLink) {
                $submission->documents()->create([
                    'document_template_id' => $template->id,
                    'doc_type'             => $template->code,
                    'file_path'            => $request->input("hyperlinks.{$template->id}"),
                    'original_name'        => 'Link Dokumen',
                    'mime'                 => 'text/url',
                    'size'                 => 0,
                    'uploaded_by'          => auth()->id(),
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
        }

        return redirect()->route('submissions.index')->with('success', 'Proposal beserta seluruh berkas berhasil diajukan!');
    }

    /**
     * Tampilan Detail Pengajuan Sisi Mahasiswa (Tempat Upload Dokumen Persyaratan)
     */
    public function show(Request $request, Submission $submission)
    {
        $this->authorize('view', $submission);

        // Eager load seluruh relasi pendukung
        $submission->load(['documents.template', 'student', 'assignments.reviewer', 'reviews.reviewer', 'statusHistories.changer', 'decisions.decider']);

        $documentTemplates = DocumentTemplate::visible()->orderBy('id')->get();
        $uploadedTemplateIds = $submission->documents->pluck('document_template_id')->toArray();
        $tab = $request->input('tab', 'details');

        // Kalkulasi state UI untuk Konfirmasi EC Draft Mahasiswa
        $latestHistory = $submission->statusHistories()->latest()->first();
        $isRevisionPending = $submission->status === SubmissionStatus::WAITING_STUDENT_CONFIRMATION 
                             && $latestHistory 
                             && str_contains($latestHistory->note ?? '', 'Permintaan perbaikan Draf EC');
        
        $revisionNote = $isRevisionPending ? str_replace('Permintaan perbaikan Draf EC: ', '', $latestHistory->note) : '';

        return view('submissions.show', compact(
            'submission', 
            'documentTemplates', 
            'uploadedTemplateIds', 
            'tab', 
            'isRevisionPending', 
            'revisionNote'
        ));
    }

    /**
     * Mengunduh file template master secara aman via implicit route model binding
     */
    public function downloadTemplate(DocumentTemplate $template)
    {
        if (empty($template->file_path)) {
            return back()->with('error', 'Template dokumen ini belum memiliki file terlampir.');
        }

        if (filter_var($template->file_path, FILTER_VALIDATE_URL)) {
            return redirect()->away($template->file_path);
        }

        if (!Storage::disk('public')->exists($template->file_path)) {
            return back()->with('error', 'Mohon maaf, fisik master berkas template tidak ditemukan di server penyimpanan lokal.');
        }

        $extension = pathinfo($template->file_path, PATHINFO_EXTENSION);
        $safeName = str_replace(' ', '_', $template->name) . '.' . $extension;

        return Storage::disk('public')->download($template->file_path, $safeName);
    }

    /**
     * Membuka berkas dokumen PDF secara inline/link di tab baru browser
     * Menggunakan scoped binding: document harus milik submission (IDOR prevention)
     */
    public function viewDocument(Submission $submission, SubmissionDocument $document)
    {
        $this->authorize('viewDocument', $submission);

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
        $this->authorize('update', $submission);
        $documentTemplates = DocumentTemplate::visible()->orderBy('id')->get();
        return view('submissions.edit', compact('submission', 'documentTemplates'));
    }

    public function update(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        $request->validate([
            'title' => 'required|string|max:500',
            'type' => 'required|string|max:100',
            'abstract' => 'nullable|string|max:5000',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'hyperlinks.*' => 'nullable|url',
        ]);

        $documentTemplates = DocumentTemplate::visible()->orderBy('id')->get();

        foreach ($documentTemplates as $template) {
            $hasFile = $request->hasFile("files.{$template->id}");
            $hasLink = $request->filled("hyperlinks.{$template->id}");
            $hasExisting = $submission->documents()->where('document_template_id', $template->id)->exists();

            if ($template->is_required && !$hasFile && !$hasLink && !$hasExisting) {
                return back()->withErrors(["files.{$template->id}" => "Dokumen '{$template->name}' wajib diisi melalui File Upload atau Link Dokumen."])->withInput();
            }

            if ($hasFile && $hasLink) {
                return back()->withErrors(["files.{$template->id}" => "Dokumen '{$template->name}' tidak boleh diisi keduanya (File dan Link). Silakan pilih salah satu."])->withInput();
            }
        }

        $submission->update([
            'title' => $request->title,
            'type' => $request->type,
            'abstract' => $request->abstract,
        ]);

        foreach ($documentTemplates as $template) {
            $hasFile = $request->hasFile("files.{$template->id}");
            $newLink = $request->input("hyperlinks.{$template->id}");
            $oldDoc = $submission->documents()->where('document_template_id', $template->id)->first();

            if ($hasFile) {
                if ($oldDoc) {
                    if ($oldDoc->type === 'file' && Storage::disk('public')->exists($oldDoc->file_path)) {
                        Storage::disk('public')->delete($oldDoc->file_path);
                    }
                    $oldDoc->delete();
                }

                $file = $request->file("files.{$template->id}");
                $path = $file->store('submissions/' . $submission->id, 'public'); 

                $submission->documents()->create([
                    'document_template_id' => $template->id,
                    'doc_type'             => $template->code,
                    'file_path'            => $path,
                    'original_name'        => $file->getClientOriginalName(),
                    'mime'                 => $file->getClientMimeType(),
                    'size'                 => $file->getSize(),
                    'uploaded_by'          => auth()->id(),
                ]);
            } elseif ($newLink) {
                if (!$oldDoc || $oldDoc->type !== 'link' || $oldDoc->file_path !== $newLink) {
                    if ($oldDoc) {
                        if ($oldDoc->type === 'file' && Storage::disk('public')->exists($oldDoc->file_path)) {
                            Storage::disk('public')->delete($oldDoc->file_path);
                        }
                        $oldDoc->delete();
                    }

                    $submission->documents()->create([
                        'document_template_id' => $template->id,
                        'doc_type'             => $template->code,
                        'file_path'            => $newLink,
                        'original_name'        => 'Link Dokumen',
                        'mime'                 => 'text/url',
                        'size'                 => 0,
                        'uploaded_by'          => auth()->id(),
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
     * Final Submit Ajuan Mahasiswa
     */
    public function submit(Request $request, Submission $submission)
    {
        $this->authorize('submit', $submission);

        $request->validate([
            'note' => 'required|string|max:5000',
            'revision_file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        // Validasi Kelengkapan Berkas Dinamis langsung dari berkas terunggah
        $requiredTemplateIds = DocumentTemplate::visible()->where('is_required', true)->pluck('id')->toArray();
        $uploadedTemplateIds = $submission->documents->pluck('document_template_id')->toArray();

        foreach ($requiredTemplateIds as $requiredId) {
            if (!in_array($requiredId, $uploadedTemplateIds)) {
                return back()->with('error', 'Gagal mengirim! Anda belum melengkapi berkas dokumen persyaratan yang bersifat Wajib.');
            }
        }

        if ($submission->status !== SubmissionStatus::REVISION_REQUIRED) {
            return back()->with('error', 'Pengajuan tidak dalam status yang bisa di-submit.');
        }

        if ($request->hasFile('revision_file')) {
            $file = $request->file('revision_file');
            $path = $file->store('submissions/' . $submission->id, 'public');

            $submission->documents()->create([
                'document_template_id' => null,
                'doc_type'             => 'REVISION',
                'file_path'            => $path,
                'original_name'        => $file->getClientOriginalName(),
                'mime'                 => $file->getClientMimeType(),
                'size'                 => $file->getSize(),
                'uploaded_by'          => auth()->id(),
            ]);
        }

        $this->workflow->transition($submission, SubmissionStatus::REVISED, $request->user(), $request->input('note'));
        $submission->update(['submitted_at' => now()]);

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Revisi proposal berhasil dikirim!');
    }

    /**
     * Proses Unggah Dokumen Berkas Satuan Mahasiswa di Halaman Show
     */
    public function uploadDocument(Request $request, Submission $submission)
    {
        $this->authorize('uploadDocument', $submission);

        if ($submission->status !== SubmissionStatus::REVISION_REQUIRED) {
            return back()->with('error', 'Tidak bisa upload dokumen pada status ini.');
        }

        $allowedTemplateIds = DocumentTemplate::visible()->pluck('id')->toArray();
        $allowedIdsString = implode(',', $allowedTemplateIds);

        $request->validate([
            'document_template_id' => 'required|in:' . $allowedIdsString,
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'hyperlink' => 'nullable|url',
        ]);

        $hasFile = $request->hasFile('file');
        $hasLink = $request->filled('hyperlink');

        if (!$hasFile && !$hasLink) {
            return back()->withErrors(['file' => 'Pilih file PDF, DOC, DOCX atau masukkan Link Dokumen.']);
        }

        $oldDoc = $submission->documents()->where('document_template_id', $request->document_template_id)->first();
        if ($oldDoc) {
            if ($oldDoc->type === 'file' && Storage::disk('public')->exists($oldDoc->file_path)) {
                Storage::disk('public')->delete($oldDoc->file_path);
            }
            $oldDoc->delete();
        }

        $currentTemplate = DocumentTemplate::find($request->document_template_id);
        $backupEnumStr = $currentTemplate->code;

        if ($hasFile) {
            $file = $request->file('file');
            $path = $file->store('submissions/' . $submission->id, 'public');

            $submission->documents()->create([
                'document_template_id' => $request->document_template_id,
                'doc_type' => $backupEnumStr,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $request->user()->id,
            ]);
        } else {
            $submission->documents()->create([
                'document_template_id' => $request->document_template_id,
                'doc_type' => $backupEnumStr,
                'file_path' => $request->hyperlink,
                'original_name' => 'Link Dokumen',
                'mime' => 'text/url',
                'size' => 0,
                'uploaded_by' => $request->user()->id,
            ]);
        }

        return back()->with('success', 'Dokumen berkas berhasil diupload.');
    }

    /**
     * Menghapus dokumen milik pengajuan (scoped binding + safe storage)
     */
    public function deleteDocument(Request $request, Submission $submission, SubmissionDocument $document)
    {
        $this->authorize('deleteDocument', $submission);

        if ($submission->status !== SubmissionStatus::REVISION_REQUIRED) {
            return back()->with('error', 'Tidak bisa menghapus dokumen pada status ini.');
        }

        // Safe storage: hanya hapus file fisik jika bukan hyperlink dan file ada
        if ($document->type === 'file' && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function confirmEcData(Request $request, Submission $submission)
    {
        $this->authorize('confirmEc', $submission);

        if ($submission->status !== SubmissionStatus::WAITING_STUDENT_CONFIRMATION) {
            return back()->with('error', 'Status pengajuan tidak valid untuk konfirmasi saat ini.');
        }

        if (empty($submission->ec_number)) {
            return back()->with('error', 'Draft Ethical Clearance belum dibuat oleh Admin.');
        }

        // Student now only confirms the pre-filled data, no edits allowed.
        
        $this->workflow->transition($submission, SubmissionStatus::WAITING_SIGNATURE, $request->user(), 'Peneliti telah mengonfirmasi draf sertifikat EC.');

        // Notify admins about the confirmation
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new \App\Notifications\EcDraftConfirmed($submission));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to notify admin on EC confirmation: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Draf sertifikat berhasil dikonfirmasi. Saat ini menunggu tanda tangan dari Ketua KEP.');
    }

    public function requestEcRevision(Request $request, Submission $submission)
    {
        $this->authorize('confirmEc', $submission);

        if ($submission->status !== SubmissionStatus::WAITING_STUDENT_CONFIRMATION) {
            return back()->with('error', 'Status pengajuan tidak valid untuk aksi ini.');
        }

        $request->validate([
            'note' => 'required|string|max:5000',
        ]);

        // Self-transition untuk mencatat di StatusHistory
        $this->workflow->transition($submission, SubmissionStatus::WAITING_STUDENT_CONFIRMATION, $request->user(), 'Permintaan perbaikan Draf EC: ' . $request->input('note'));

        // Notify admins
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new \App\Notifications\EcDraftRevisionRequested($submission));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to notify admin on EC revision request: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Permintaan perbaikan draf telah dikirim ke Admin.');
    }

    public function sign(Request $request, Submission $submission)
    {
        $this->authorize('sign', $submission);

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
        $submission->update([
            'ec_certificate_path' => $generatedPath,
            'signed_at' => now()
        ]);

        $this->workflow->transition($submission, SubmissionStatus::DONE, $request->user(), 'Sertifikat Laik Etik telah ditandatangani oleh Ketua KEP.');

        return back()->with('success', 'Sertifikat Laik Etik berhasil ditandatangani.');
    }

    public function previewFinalEc(Request $request, Submission $submission)
    {
        $this->authorize('sign', $submission);

        if ($submission->status !== SubmissionStatus::WAITING_SIGNATURE) {
            return back()->with('error', 'Status pengajuan tidak valid untuk melihat pratinjau final.');
        }

        $certificateService = new \App\Services\CertificateGenerator();
        $pdfContent = $certificateService->previewFinal($submission);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="PREVIEW-FINAL-EC-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $submission->ec_number ?? 'UNASSIGNED') . '.pdf"'
        ]);
    }

    public function previewDraftEc(Request $request, Submission $submission)
    {
        $this->authorize('confirmEc', $submission);

        if ($submission->status !== SubmissionStatus::WAITING_STUDENT_CONFIRMATION) {
            return back()->with('error', 'Status pengajuan tidak valid untuk melihat draf.');
        }

        if (empty($submission->ec_number)) {
            return back()->with('error', 'Draf belum lengkap.');
        }

        $certificateService = new \App\Services\CertificateGenerator();
        $pdfContent = $certificateService->generate($submission, true);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="DRAFT-EC-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $submission->ec_number ?? 'UNASSIGNED') . '.pdf"'
        ]);
    }

    /**
     * Mengunduh sertifikat EC milik mahasiswa
     */
    public function downloadEc(Request $request, Submission $submission)
    {
        $this->authorize('downloadEc', $submission);

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
        $this->authorize('downloadCertificate', $submission);

        if (empty($submission->ec_certificate_path) || ! Storage::exists($submission->ec_certificate_path)) {
            return back()->with('error', 'Mohon maaf, berkas fisik sertifikat belum terbit di sistem.');
        }

        $user = $request->user();

        // Log the download event
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'submission_id' => $submission->id,
            'old_status' => $submission->status->value,
            'new_status' => $submission->status->value,
            'description' => "Sertifikat diunduh oleh {$user->name}. IP: " . $request->ip(),
        ]);

        return Storage::download($submission->ec_certificate_path, 'EC-' . $submission->code . '.pdf');
    }
}