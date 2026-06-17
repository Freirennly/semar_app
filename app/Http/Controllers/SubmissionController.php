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
        $documentTemplates = DocumentTemplate::where('is_shown', true)->get();

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
        $documentTemplates = DocumentTemplate::where('is_shown', true)->get();

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
            'files.*'       => 'nullable|file|mimes:pdf|max:10240', // Validasi file di dalam array max 10MB
            'hyperlinks.*'  => 'nullable|url',
        ]);

        // 2. Ambil Master Template untuk Validasi Aturan Wajib Atas Array Masukan
        $documentTemplates = DocumentTemplate::where('is_shown', true)->get();

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
                    'doc_type'             => str_contains(strtolower($template->name), 'proposal') ? DocType::PROPOSAL->value : DocType::ICF->value,
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
                    'doc_type'             => str_contains(strtolower($template->name), 'proposal') ? DocType::PROPOSAL->value : DocType::ICF->value,
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

        $documentTemplates = DocumentTemplate::where('is_shown', true)->get();
        $uploadedTemplateIds = $submission->documents->pluck('document_template_id')->toArray();
        $tab = $request->input('tab', 'details');

        return view('submissions.show', compact('submission', 'documentTemplates', 'uploadedTemplateIds', 'tab'));
    }

    /**
     * Mengunduh file template master secara aman lewat sistem controller
     */
    public function downloadTemplate(DocumentTemplate $documentTemplate)
    {
        if (!Storage::disk('public')->exists($documentTemplate->file_path)) {
            return back()->with('error', 'Mohon maaf, master berkas template tidak ditemukan di server.');
        }

        $extension = pathinfo($documentTemplate->file_path, PATHINFO_EXTENSION);
        $safeName = str_replace(' ', '_', $documentTemplate->name) . '.' . $extension;

        return Storage::disk('public')->download($documentTemplate->file_path, $safeName);
    }

    public function edit(Submission $submission)
    {
        Gate::authorize('update', $submission);
        $documentTemplates = DocumentTemplate::where('is_shown', true)->get();
        return view('submissions.edit', compact('submission', 'documentTemplates'));
    }

    public function update(Request $request, Submission $submission)
    {
        Gate::authorize('update', $submission);

        $request->validate([
            'title' => 'required|string|max:500',
            'type' => 'required|string|max:100',
            'abstract' => 'nullable|string|max:5000',
            'files.*' => 'nullable|file|mimes:pdf|max:10240',
            'hyperlinks.*' => 'nullable|url',
        ]);

        $documentTemplates = DocumentTemplate::where('is_shown', true)->get();

        foreach ($documentTemplates as $template) {
            $hasFile = $request->hasFile("files.{$template->id}");
            $hasLink = $request->filled("hyperlinks.{$template->id}");
            $hasExisting = $submission->documents()->where('document_template_id', $template->id)->exists();

            if ($template->is_required && !$hasFile && !$hasLink && !$hasExisting) {
                return back()->withErrors(["files.{$template->id}" => "Dokumen '{$template->name}' wajib diisi melalui File Upload atau Hyperlink GDrive."])->withInput();
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
                    if ($oldDoc->type === 'file') {
                        Storage::disk('public')->delete($oldDoc->file_path);
                    }
                    $oldDoc->delete();
                }

                $file = $request->file("files.{$template->id}");
                $path = $file->store('submissions/' . $submission->id, 'public'); 

                $submission->documents()->create([
                    'document_template_id' => $template->id,
                    'doc_type'             => str_contains(strtolower($template->name), 'proposal') ? DocType::PROPOSAL->value : DocType::ICF->value,
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
                        'doc_type'             => str_contains(strtolower($template->name), 'proposal') ? DocType::PROPOSAL->value : DocType::ICF->value,
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
     * Final Submit Ajuan Mahasiswa
     */
    public function submit(Request $request, Submission $submission)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);

        // Validasi Kelengkapan Berkas Dinamis langsung dari berkas terunggah
        $requiredTemplateIds = DocumentTemplate::where('is_shown', true)->where('is_required', true)->pluck('id')->toArray();
        $uploadedTemplateIds = $submission->documents->pluck('document_template_id')->toArray();

        foreach ($requiredTemplateIds as $requiredId) {
            if (!in_array($requiredId, $uploadedTemplateIds)) {
                return back()->with('error', 'Gagal mengirim! Anda belum melengkapi berkas dokumen persyaratan yang bersifat Wajib.');
            }
        }

        if ($submission->status !== SubmissionStatus::RESUBMISSION) {
            return back()->with('error', 'Pengajuan tidak dalam status yang bisa di-submit.');
        }

        $this->workflow->transition($submission, SubmissionStatus::REVISED, $user, 'Revisi dikirim oleh mahasiswa');
        $submission->update(['submitted_at' => now()]);

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Revisi proposal berhasil dikirim!');
    }

    /**
     * Proses Unggah Dokumen Berkas Satuan Mahasiswa di Halaman Show
     */
    public function uploadDocument(Request $request, Submission $submission)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);
        if ($submission->status !== SubmissionStatus::RESUBMISSION) {
            return back()->with('error', 'Tidak bisa upload dokumen pada status ini.');
        }

        $allowedTemplateIds = DocumentTemplate::where('is_shown', true)->pluck('id')->toArray();
        $allowedIdsString = implode(',', $allowedTemplateIds);

        $request->validate([
            'document_template_id' => 'required|in:' . $allowedIdsString,
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'hyperlink' => 'nullable|url',
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
        $backupEnumStr = str_contains(strtolower($currentTemplate->name), 'proposal') ? DocType::PROPOSAL->value : DocType::ICF->value;

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
                'uploaded_by' => $user->id,
                'type' => 'file'
            ]);
        } else {
            $submission->documents()->create([
                'document_template_id' => $request->document_template_id,
                'doc_type' => $backupEnumStr,
                'file_path' => $request->hyperlink,
                'original_name' => 'Link Google Drive',
                'mime' => 'text/url',
                'size' => 0,
                'uploaded_by' => $user->id,
                'type' => 'link'
            ]);
        }

        return back()->with('success', 'Dokumen berkas berhasil diupload.');
    }

    public function deleteDocument(Request $request, Submission $submission, SubmissionDocument $document)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);
        if ($submission->status !== SubmissionStatus::RESUBMISSION) {
            return back()->with('error', 'Tidak bisa menghapus dokumen pada status ini.');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function confirmEcData(Request $request, Submission $submission)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);

        if ($submission->status !== SubmissionStatus::APPROVED) {
            return back()->with('error', 'Status pengajuan tidak valid untuk konfirmasi saat ini.');
        }

        $this->workflow->transition($submission, SubmissionStatus::WAITING_SIGNATURE, $user, 'Peneliti telah mengonfirmasi draf sertifikat EC.');

        return back()->with('success', 'Draf sertifikat berhasil dikonfirmasi. Saat ini menunggu tanda tangan dari Ketua KEP.');
    }

    public function downloadEc(Request $request, Submission $submission)
    {
        $user = $request->user();
        
        if ($user->hasRole('student') && $submission->student_id !== $user->id) {
            abort(403);
        }

        if ($submission->status !== SubmissionStatus::DONE) {
            return back()->with('error', 'Sertifikat Laik Etik belum diterbitkan.');
        }

        $ecDocument = $submission->documents()->where('doc_type', 'EC_CERTIFICATE')->first();
        
        if (!$ecDocument || !Storage::disk('public')->exists($ecDocument->file_path)) {
            return back()->with('error', 'Mohon maaf, file sertifikat tidak ditemukan di dalam sistem.');
        }

        return Storage::disk('public')->download($ecDocument->file_path, 'Ethical_Clearance_' . $submission->code . '.pdf');
    }
}