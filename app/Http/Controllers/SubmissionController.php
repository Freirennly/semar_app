<?php

namespace App\Http\Controllers;

use App\Enums\DocType;
use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

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

        return view('submissions.index', compact('submissions'));
    }

    public function create()
    {
        Gate::authorize('create', Submission::class);
        return view('submissions.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Submission::class);

        $data = $request->validate([
            'title' => 'required|string|max:500',
            'type' => 'required|string|max:100',
            'abstract' => 'nullable|string|max:5000',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $user = $request->user();

        $submission = Submission::create([
            'code' => Submission::generateCode(),
            'title' => $data['title'],
            'type' => $data['type'],
            'abstract' => $data['abstract'] ?? null,
            'status' => SubmissionStatus::DRAFT,
            'student_id' => $user->id,
        ]);

        // Record initial status via WorkflowService pattern (manual for initial creation)
        \App\Models\StatusHistory::create([
            'submission_id' => $submission->id,
            'from_status' => null,
            'to_status' => SubmissionStatus::DRAFT->value,
            'changed_by' => $user->id,
            'note' => 'Pengajuan dibuat',
            'created_at' => now(),
        ]);

        // Handle optional PDF file upload (stored as PROPOSAL doc_type)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('submissions/' . $submission->id, 'public');

            SubmissionDocument::create([
                'submission_id' => $submission->id,
                'doc_type' => DocType::PROPOSAL->value,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);
        }

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil dibuat. Silakan upload dokumen yang diperlukan.');
    }

    public function show(Request $request, Submission $submission)
    {
        $user = $request->user();

        // Authorization: student can only view own, reviewer only assigned, others based on permission
        if ($user->hasRole('student') && $submission->student_id !== $user->id) {
            abort(403);
        }
        if ($user->hasRole('reviewer')) {
            $isAssigned = $submission->assignments()->where('reviewer_id', $user->id)->exists();
            if (! $isAssigned) abort(403);
        }

        $submission->load(['documents', 'student', 'assignments.reviewer', 'reviews.reviewer', 'statusHistories.changer', 'decisions.decider']);

        $docTypes = DocType::cases();
        $uploadedTypes = $submission->documents->pluck('doc_type')->map(fn($d) => $d->value)->toArray();
        $tab = $request->input('tab', 'details');

        return view('submissions.show', compact('submission', 'docTypes', 'uploadedTypes', 'tab'));
    }

    public function edit(Submission $submission)
    {
        Gate::authorize('update', $submission);
        return view('submissions.edit', compact('submission'));
    }

    public function update(Request $request, Submission $submission)
    {
        Gate::authorize('update', $submission);

        $data = $request->validate([
            'title' => 'required|string|max:500',
            'type' => 'required|string|max:100',
            'abstract' => 'nullable|string|max:5000',
        ]);

        $submission->update($data);
        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function submit(Request $request, Submission $submission)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);

        if (! $submission->hasAllDocuments()) {
            return back()->with('error', 'Semua dokumen wajib harus diupload sebelum submit.');
        }

        if (! in_array($submission->status, [SubmissionStatus::DRAFT, SubmissionStatus::RESUBMISSION])) {
            return back()->with('error', 'Pengajuan tidak dalam status yang bisa di-submit.');
        }

        $this->workflow->transition($submission, SubmissionStatus::SUBMITTED, $user, 'Pengajuan disubmit oleh mahasiswa');
        $submission->update(['submitted_at' => now()]);

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil disubmit!');
    }

    public function uploadDocument(Request $request, Submission $submission)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);
        if (! in_array($submission->status, [SubmissionStatus::DRAFT, SubmissionStatus::RESUBMISSION])) {
            return back()->with('error', 'Tidak bisa upload dokumen pada status ini.');
        }

        $request->validate([
            'doc_type' => 'required|in:PROPOSAL,ICF,SURAT_PENGANTAR',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('submissions/' . $submission->id, 'public');

        SubmissionDocument::updateOrCreate(
            ['submission_id' => $submission->id, 'doc_type' => $request->doc_type],
            [
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]
        );

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function deleteDocument(Request $request, Submission $submission, SubmissionDocument $document)
    {
        $user = $request->user();
        if ($submission->student_id !== $user->id) abort(403);
        if (! in_array($submission->status, [SubmissionStatus::DRAFT, SubmissionStatus::RESUBMISSION])) {
            return back()->with('error', 'Tidak bisa menghapus dokumen pada status ini.');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
    public function downloadTemplate()
    {
        // Tentukan lokasi file template disimpan (misal: storage/app/public/templates/template_protokol.docx)
        $filePath = storage_path('app/public/templates/template_protokol.docx');

        // Cek apakah file fisik tersebut benar-benar ada
        if (!file_exists($filePath)) {
            // Jika belum ada, kembalikan ke halaman sebelumnya dengan pesan error
            return back()->with('error', 'Mohon maaf, file template saat ini belum diunggah oleh Admin.');
        }

        // Jika ada, langsung download filenya
        return response()->download($filePath);
    }
    public function confirmEcData(Request $request, Submission $submission)
    {
        $user = $request->user();
        
        // Pastikan hanya pemilik pengajuan yang bisa melakukan konfirmasi
        if ($submission->student_id !== $user->id) abort(403);

        // Pastikan statusnya memang sedang dikirim ke user (KIRIM_USER)
        if ($submission->status !== SubmissionStatus::KIRIM_USER) {
            return back()->with('error', 'Status pengajuan tidak valid untuk konfirmasi saat ini.');
        }

        // Ubah status ke tahap selanjutnya (misal: WAITING_TTD atau menunggu Ketua KEP)
        // Catatan: Sesuaikan nama status WAITING_TTD dengan Enum yang Anda miliki di SubmissionStatus
        $this->workflow->transition($submission, SubmissionStatus::WAITING_TTD, $user, 'Peneliti telah mengonfirmasi draf sertifikat EC.');

        return back()->with('success', 'Draf sertifikat berhasil dikonfirmasi. Saat ini menunggu tanda tangan dari Ketua KEP.');
    }

    /**
     * Mengunduh file Sertifikat Laik Etik yang sudah di-publish.
     */
    public function downloadEc(Request $request, Submission $submission)
    {
        $user = $request->user();
        
        // Cek kepemilikan khusus untuk student
        if ($user->hasRole('student') && $submission->student_id !== $user->id) {
            abort(403);
        }

        // Pastikan sertifikat sudah dipublikasikan oleh Admin
        if ($submission->status !== SubmissionStatus::PUBLISHED) {
            return back()->with('error', 'Sertifikat Laik Etik belum diterbitkan.');
        }

        // Asumsi: File sertifikat final disimpan di tabel SubmissionDocument dengan doc_type 'EC_CERTIFICATE'
        // Anda bisa menyesuaikan tipe dokumennya dengan Enum DocType Anda
        $ecDocument = $submission->documents()->where('doc_type', 'EC_CERTIFICATE')->first();
        
        if (!$ecDocument || !Storage::disk('public')->exists($ecDocument->file_path)) {
            return back()->with('error', 'Mohon maaf, file sertifikat tidak ditemukan di dalam sistem.');
        }

        // Mengunduh file
        return Storage::disk('public')->download($ecDocument->file_path, 'Ethical_Clearance_' . $submission->code . '.pdf');
    }
}
