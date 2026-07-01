<x-layouts.app :title="$submission->title">
    {{-- Header & Breadcrumb --}}
    <div class="mb-6 animate-fade-in">
        <nav class="text-[12px] text-text-secondary mb-2" aria-label="Breadcrumb">
            <a href="{{ route('submissions.index') }}" class="hover:text-primary transition-colors">Pengajuan</a> 
            <span class="mx-1">/</span> 
            <span class="text-text">{{ $submission->code }}</span>
        </nav>
        <div class="flex items-start md:items-center justify-between gap-4 flex-wrap">
            <div class="space-y-2">
                <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight">{{ $submission->title }}</h1>
                <p class="text-[14px] text-text-secondary">
                    <span class="font-semibold text-text">{{ $submission->code }}</span> · {{ $submission->type }} · {{ $submission->student->name }}
                </p>
            </div>
            <x-status-badge :status="$submission->status" />
        </div>
    </div>

    {{-- Error Alerts --}}
    @if($errors->any())
        <x-alert type="error" title="Gagal Memproses File" class="mb-6">
            <ul class="list-disc pl-5 space-y-0.5 text-[12px]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    {{-- Tabs --}}
    @php $tab = $tab ?? 'details'; @endphp
    <div class="border-b border-border mb-6">
        <nav class="flex gap-4 -mb-px" aria-label="Tabs">
            @foreach(['details' => 'Details', 'documents' => 'Dokumen', 'history' => 'Status History'] as $key => $label)
                <a href="{{ route('submissions.show', $submission) }}?tab={{ $key }}" class="px-4 py-3 text-[14px] font-medium border-b-2 transition-colors {{ $tab === $key ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text hover:border-border-strong' }}">{{ $label }}</a>
            @endforeach
        </nav>
    </div>

    @if($tab === 'details')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
            <h2 class="text-xl md:text-[24px] font-semibold text-text border-b border-border pb-2">Informasi Pengajuan</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-[14px]">
                <div>
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">Kode</span>
                    <span class="font-semibold text-text mt-1 block">{{ $submission->code }}</span>
                </div>
                <div>
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">Jenis</span>
                    <span class="font-semibold text-text mt-1 block">{{ $submission->type }}</span>
                </div>
                <div>
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">Pengaju</span>
                    <span class="font-semibold text-text mt-1 block">{{ $submission->student->name }}</span>
                </div>
                <div>
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">NIM/NIP</span>
                    <span class="font-semibold text-text mt-1 block">{{ $submission->student->nim_nip ?? '-' }}</span>
                </div>
                <div class="col-span-1 sm:col-span-2">
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide mb-2">Abstrak</span>
                    <div class="font-academic text-text p-4 bg-slate-50 border border-border rounded-xl whitespace-pre-line">{{ $submission->abstract ?? 'Tidak ada abstrak.' }}</div>
                </div>
            </dl>

            @if($submission->reviews->whereNotNull('submitted_at')->isNotEmpty())
            <div class="pt-6 border-t border-border space-y-4">
                <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">Riwayat Revisi & Review</span>
                
                @php
                    $groupedReviews = $submission->reviews->whereNotNull('submitted_at')
                        ->sortBy(function($r) { return $r->reviewer->name ?? ''; })
                        ->groupBy('revision_round')
                        ->sortKeys();
                    $revisionHistories = $submission->statusHistories()->where('to_status', \App\Enums\SubmissionStatus::REVISED)->orderBy('created_at', 'asc')->get();
                    $maxRound = $groupedReviews->keys()->max();
                @endphp

                <div class="space-y-6">
                    @foreach($groupedReviews as $round => $reviews)
                        <div class="bg-slate-50 border border-border rounded-xl overflow-hidden">
                            <div class="bg-slate-100/50 border-b border-border px-4 py-2.5 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <span class="text-[13px] font-bold text-text">Putaran {{ $round }}</span>
                                    @if($round === $maxRound && in_array($submission->status->value, ['PROCESS', 'ON_REVIEW', 'REVISION_REQUIRED', 'REVISED']))
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-primary/10 text-primary uppercase">Putaran Aktif</span>
                                    @endif
                                </div>
                            </div>
                            <div class="p-4 space-y-4">
                                @foreach($reviews as $review)
                                    <div class="p-4 bg-amber-50/50 border border-amber-200/50 rounded-xl space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[12px] font-bold text-amber-800">{{ $review->reviewer->name ?? 'Reviewer' }}</span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-900 uppercase">
                                                {{ $review->recommendation->label() }}
                                            </span>
                                        </div>
                                        <p class="text-[13px] text-text-secondary whitespace-pre-line leading-relaxed">{{ $review->notes }}</p>
                                        @php
                                            $attachment = $submission->documents->where('uploaded_by', $review->reviewer_id)
                                                ->where('doc_type', 'REVIEW_ATTACHMENT_R' . $round . '_U' . $review->reviewer_id)
                                                ->first();
                                        @endphp
                                        @if($attachment)
                                            <div class="mt-2 text-xs flex items-center gap-1 border-t border-amber-200/50 pt-2">
                                                <span class="text-text-secondary font-medium">Lampiran:</span>
                                                <a href="{{ route('submissions.view-document', [$submission, $attachment]) }}" target="_blank" class="text-primary hover:text-primary-hover font-semibold inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    {{ $attachment->original_name }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if(isset($revisionHistories[$round - 1]))
                                    <div class="p-4 bg-blue-50/50 border border-blue-200/50 rounded-xl space-y-2 mt-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[12px] font-bold text-blue-800">Catatan Revisi Mahasiswa</span>
                                            <span class="text-[10px] font-medium text-blue-600">{{ $revisionHistories[$round - 1]->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</span>
                                        </div>
                                        <p class="text-[13px] text-text-secondary whitespace-pre-line leading-relaxed">{{ $revisionHistories[$round - 1]->note }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm h-fit space-y-6">
            <h2 class="text-xl md:text-[24px] font-semibold text-text border-b border-border pb-2">Checklist Dokumen</h2>
            
            @php
                $requiredTemplateIds = $documentTemplates->where('is_required', true)->pluck('id')->toArray();
                $uploadedTemplateIds = $submission->documents->pluck('document_template_id')->toArray();
                $hasAllDocs = collect($requiredTemplateIds)->every(fn($id) => in_array($id, $uploadedTemplateIds));
            @endphp

            <p class="text-[14px] text-text-secondary">
                <span class="font-bold text-text">{{ $submission->documents->whereNotNull('document_template_id')->count() }}</span> / {{ $documentTemplates->where('is_required', true)->count() }} dokumen wajib terupload
            </p>
            
            <div class="space-y-3">
                @foreach($documentTemplates as $template)
                    @php $uploaded = in_array($template->id, $uploadedTemplateIds); @endphp
                    <div class="flex items-center gap-3 py-1.5">
                        @if($uploaded)
                            <svg class="w-5 h-5 text-success shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @else
                            <svg class="w-5 h-5 text-text-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        @endif
                        <span class="text-[14px] {{ $uploaded ? 'text-text font-medium' : 'text-text-secondary' }} truncate max-w-[160px]">{{ $template->name }}</span>
                        <span class="text-[12px] {{ $uploaded ? 'text-success font-semibold' : 'text-danger font-semibold' }} ml-auto">{{ $uploaded ? 'Sudah' : 'Belum' }}</span>
                    </div>
                @endforeach
            </div>

            @role('student')
            @if($submission->status === \App\Enums\SubmissionStatus::REVISION_REQUIRED)
                @if($hasAllDocs)
                    <form method="POST" action="{{ route('submissions.submit', $submission) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label for="note" class="block text-[12px] font-semibold text-text-secondary mb-1">Catatan Revisi <span class="text-danger">*</span></label>
                            <textarea name="note" id="note" rows="3" required class="w-full bg-slate-50 border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors" placeholder="Tulis ringkasan perbaikan atau catatan revisi Anda...">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="text-[11px] text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="revision_file" class="block text-[12px] font-semibold text-text-secondary mb-1">Lampiran Revisi (Opsional)</label>
                            <input type="file" name="revision_file" id="revision_file" accept=".pdf,.doc,.docx,.zip,.rar" class="w-full text-[12px] text-text border border-border rounded-lg bg-white file:mr-3 file:py-1.5 file:px-3 file:border-0 file:text-[12px] file:font-semibold file:bg-slate-100 file:text-text-secondary hover:file:bg-slate-200 cursor-pointer">
                            @error('revision_file')
                                <p class="text-[11px] text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="button" class="w-full btn-primary" onclick="event.preventDefault(); window.confirmModal('Kirim revisi ini ke sekretariat?', this.closest('form'));">Kirim Revisi</button>
                    </form>
                @else
                    <x-alert type="warning" class="mt-6">
                        Upload semua dokumen wajib sebelum kirim revisi. Buka tab <strong>Dokumen</strong> untuk mengupload.
                    </x-alert>
                @endif
            @endif

            @if($submission->status === \App\Enums\SubmissionStatus::WAITING_STUDENT_CONFIRMATION && !empty($submission->ec_number))
                <div class="mt-6 border-t border-border pt-6 space-y-4">
                    <h3 class="text-sm font-bold text-text uppercase tracking-wider">Konfirmasi Draft Ethical Clearance</h3>
                    
                    <div class="space-y-3 text-sm p-4 bg-slate-50 border border-border rounded-xl">
                        <div>
                            <p class="text-text-secondary text-xs font-medium uppercase tracking-wide">Nomor EC</p>
                            <p class="font-semibold text-text mt-0.5">{{ $submission->ec_number }}</p>
                        </div>
                        <div>
                            <p class="text-text-secondary text-xs font-medium uppercase tracking-wide">Judul Penelitian</p>
                            <p class="font-semibold text-text mt-0.5">{{ $submission->confirmed_title }}</p>
                        </div>
                        <div>
                            <p class="text-text-secondary text-xs font-medium uppercase tracking-wide">Nama Peneliti</p>
                            <p class="font-semibold text-text mt-0.5">{{ $submission->confirmed_researcher_name }}</p>
                        </div>
                        <div>
                            <p class="text-text-secondary text-xs font-medium uppercase tracking-wide">Penandatangan (Ketua)</p>
                            <p class="font-semibold text-text mt-0.5">{{ optional($submission->signatory)->name }}</p>
                        </div>
                        <div>
                            <p class="text-text-secondary text-xs font-medium uppercase tracking-wide">Status Draft</p>
                            <div class="mt-1 flex items-center justify-between">
                                <span class="px-2 py-1 text-[11px] font-bold uppercase rounded-md bg-info-bg text-info border border-info/20">Menunggu Konfirmasi</span>
                                <a href="{{ route('submissions.preview-draft', $submission) }}" target="_blank" class="text-[11px] font-bold text-primary hover:text-primary-hover underline underline-offset-2">Preview Draft PDF</a>
                            </div>
                        </div>
                    </div>

                    @if(isset($isRevisionPending) && $isRevisionPending)
                        <div class="mt-4 p-4 bg-warning/10 border border-warning/20 rounded-xl">
                            <p class="text-xs font-bold text-warning-dark">Status: Menunggu Perbaikan Admin</p>
                            <p class="text-xs text-warning-dark mt-1">Anda telah meminta perbaikan draf ini. Silakan tunggu Admin mengirimkan draf terbaru.</p>
                            <div class="mt-3 p-3 bg-white/60 rounded-lg text-xs italic text-text-secondary">
                                " {{ $revisionNote }} "
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                            <form method="POST" action="{{ route('submissions.confirm', $submission) }}">
                                @csrf
                                <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-150" onclick="event.preventDefault(); window.confirmModal('Apakah Anda yakin data Draft EC sudah benar dan siap dikonfirmasi?', this.closest('form'));">
                                    Konfirmasi Draft
                                </button>
                            </form>

                            <button type="button" onclick="document.getElementById('revision-form-container').classList.toggle('hidden');" class="w-full bg-white hover:bg-slate-50 text-danger border border-danger/20 hover:border-danger text-xs font-bold py-2.5 rounded-xl transition-all duration-150">
                                Minta Perbaikan
                            </button>
                        </div>

                        <div id="revision-form-container" class="hidden mt-4 p-4 border border-border rounded-xl bg-slate-50">
                            <form method="POST" action="{{ route('submissions.request-revision', $submission) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="note" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Catatan Perbaikan <span class="text-danger">*</span></label>
                                    <textarea name="note" id="note" rows="3" required class="w-full bg-white border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors" placeholder="Jelaskan bagian mana dari Draft EC yang perlu diperbaiki..."></textarea>
                                </div>
                                <button type="submit" class="w-full bg-danger hover:bg-danger/90 text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-150">
                                    Kirim Permintaan Perbaikan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            @if($submission->status === \App\Enums\SubmissionStatus::DONE)
                <div class="mt-6 border-t border-border pt-6">
                    <div class="flex items-center justify-between p-4 bg-success/10 border border-success/20 rounded-xl">
                        <div>
                            <h3 class="text-sm font-bold text-success-dark uppercase tracking-wider">Ethical Clearance Diterbitkan</h3>
                            <p class="text-xs text-success-dark mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Siap diunduh
                            </p>
                        </div>
                        <a href="{{ route('submissions.download-ec', $submission) }}" target="_blank" class="bg-success hover:bg-success-hover text-white text-xs font-bold py-2.5 px-6 rounded-xl transition-all duration-150 whitespace-nowrap">
                            Unduh PDF
                        </a>
                    </div>
                </div>
            @endif
            @endrole

            @role('ketua')
            @if($submission->status === \App\Enums\SubmissionStatus::WAITING_SIGNATURE)
                <div class="mt-6 border-t border-border pt-6 space-y-4">
                    <h3 class="text-sm font-bold text-text uppercase tracking-wider">Tandatangani Ethical Clearance</h3>
                    
                    <div class="p-4 bg-slate-50 border border-border rounded-xl">
                        <p class="text-xs text-text-secondary mb-3">Mohon periksa pratinjau dokumen Final Ethical Clearance di bawah ini sebelum memberikan tanda tangan digital persetujuan Anda.</p>
                        
                        <a href="{{ route('submissions.preview-final', $submission) }}" target="_blank" class="inline-flex items-center gap-1 text-[12px] font-bold text-primary hover:text-primary-hover mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Lihat Pratinjau Final PDF
                        </a>

                        <form method="POST" action="{{ route('submissions.sign', $submission) }}">
                            @csrf
                            <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-150" onclick="event.preventDefault(); window.confirmModal('Apakah Anda yakin ingin menyetujui dan menandatangani dokumen Ethical Clearance ini? Tindakan ini tidak dapat dibatalkan.', this.closest('form'));">
                                Tandatangani Ethical Clearance
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            @endrole
        </div>
    </div>
    @endif

    {{-- KONTEN TAB 2: DOKUMEN --}}
    @if($tab === 'documents')
    <div class="space-y-6">
    <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
        <div>
            <h2 class="text-xl md:text-[24px] font-semibold text-text">Lampiran Persyaratan</h2>
            <p class="text-[14px] text-text-secondary mt-1">Semua dokumen wajib harus diupload dalam format PDF, DOC, DOCX (maks. 5MB) atau link Google Drive.</p>
        </div>
        <div class="space-y-4">
            @foreach($documentTemplates as $template)
                @php
                    $doc = $submission->documents->firstWhere('document_template_id', $template->id);
                    $canUpload = auth()->user()->hasRole('student') && $submission->status === \App\Enums\SubmissionStatus::REVISION_REQUIRED;
                @endphp
                <div class="flex flex-col lg:flex-row lg:items-center justify-between p-4 border border-border rounded-xl {{ $doc ? 'bg-surface' : 'bg-slate-50/50' }} gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        @if($doc)
                            <div class="w-10 h-10 rounded-xl bg-success-bg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-success" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-xl bg-danger-bg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-[14px] font-semibold text-text">{{ $template->name }}</p>
                                @if($template->is_required)
                                    <span class="text-[10px] font-bold px-1.5 bg-red-50 text-red-500 rounded border border-red-100 uppercase">Wajib</span>
                                @endif
                            </div>
                            @if($doc)
                                <p class="text-[12px] text-text-secondary mt-1 truncate max-w-md">{{ $doc->original_name }} · {{ number_format($doc->size / 1024, 0) }} KB</p>
                            @else
                                <p class="text-[12px] text-danger mt-1">Belum diupload</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
                        @if($doc)
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn-outline text-[12px] px-3 py-1.5">Lihat</a>
                            @if($canUpload)
                                <form method="POST" action="{{ route('submissions.delete-document', [$submission, $doc]) }}" class="inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="button" class="text-[12px] text-danger hover:text-danger/80 font-bold px-3 py-1.5 border border-danger/20 rounded-lg hover:bg-danger-bg transition-colors" onclick="event.preventDefault(); window.confirmModal('Hapus dokumen ini?', this.closest('form'));" aria-label="Hapus dokumen {{ $template->name }}">Hapus</button>
                                </form>
                            @endif
                        @endif
                        
                        @if($canUpload && !$doc)
                            <form method="POST" action="{{ route('submissions.upload-document', $submission) }}" enctype="multipart/form-data" class="w-full bg-soft-surface/50 p-4 rounded-xl border border-border mt-2">
                                @csrf
                                <input type="hidden" name="document_template_id" value="{{ $template->id }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                                    <div class="space-y-2">
                                        <label class="block text-[12px] font-semibold text-text-secondary">Unggah Berkas Baru</label>
                                        <input type="file" name="file" accept=".pdf,.doc,.docx" class="w-full text-[12px] text-text border border-border rounded-lg bg-white file:mr-3 file:py-1.5 file:px-3 file:border-0 file:text-[12px] file:font-semibold file:bg-slate-100 file:text-text-secondary hover:file:bg-slate-200 cursor-pointer" aria-label="Pilih file {{ $template->name }}">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[12px] font-semibold text-text-secondary">Atau Link Google Drive</label>
                                        <div class="flex gap-2">
                                            <input type="url" name="hyperlink" placeholder="https://drive.google.com/..." class="input-field py-2">
                                            <button type="submit" class="btn-primary text-xs shrink-0 py-2">Upload</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
        <div>
            <h2 class="text-xl md:text-[24px] font-semibold text-text">Dokumen Hasil Review</h2>
            <p class="text-[14px] text-text-secondary mt-1">Catatan etik dan dokumen balasan dari reviewer.</p>
        </div>
        <div class="space-y-4">
            @php
                $reviewerDocs = $submission->documents->filter(function($doc) {
                    return str_starts_with($doc->doc_type, 'REVIEW_ATTACHMENT_');
                });
            @endphp
            @forelse($reviewerDocs as $rDoc)
                <div class="flex flex-col lg:flex-row lg:items-center justify-between p-4 border border-border rounded-xl bg-surface gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-info-bg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-info" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[14px] font-semibold text-text">{{ $rDoc->original_name }}</p>
                            <p class="text-[12px] text-text-secondary mt-1 truncate max-w-md">Ukuran: {{ number_format($rDoc->size / 1024, 0) }} KB</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
                        <a href="{{ route('submissions.view-document', [$submission, $rDoc]) }}" target="_blank" class="btn-outline text-[12px] px-3 py-1.5">Lihat</a>
                    </div>
                </div>
            @empty
                <p class="text-[14px] text-text-secondary italic">Belum ada dokumen hasil review yang diunggah.</p>
            @endforelse
        </div>
    </div>
    
    {{-- Dokumen Revisi (Dari Mahasiswa) --}}
    @php
        $revisionDocs = $submission->documents->where('doc_type', 'REVISION');
    @endphp
    @if($revisionDocs->count() > 0)
    <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6 mt-6">
        <div>
            <h2 class="text-xl md:text-[24px] font-semibold text-text">Lampiran Revisi</h2>
            <p class="text-[14px] text-text-secondary mt-1">Dokumen tambahan yang diunggah oleh mahasiswa saat revisi.</p>
        </div>
        <div class="space-y-4">
            @foreach($revisionDocs as $doc)
            <div class="flex flex-col lg:flex-row lg:items-center justify-between p-4 border border-border rounded-xl bg-surface gap-4">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[14px] font-semibold text-text">{{ $doc->original_name }}</p>
                        <p class="text-[12px] text-text-secondary mt-1 truncate max-w-md">Diupload: {{ $doc->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
                    @if($doc->type === 'file')
                        <a href="{{ route('submissions.view-document', [$submission, $doc]) }}" target="_blank" class="btn-outline text-[12px] px-3 py-1.5">Lihat</a>
                    @else
                        <a href="{{ $doc->file_path }}" target="_blank" class="btn-outline text-[12px] px-3 py-1.5">Buka Tautan</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    </div>
    @endif

    {{-- KONTEN TAB 3: RIWAYAT --}}
    @if($tab === 'history')
    <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
        <h2 class="text-xl md:text-[24px] font-semibold text-text border-b border-border pb-2">Riwayat Status</h2>
        @if($submission->statusHistories->isEmpty())
            <p class="text-[14px] text-text-secondary">Belum ada riwayat.</p>
        @else
            <div class="relative pl-6">
                <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-border"></div>
                <div class="space-y-6">
                    @foreach($submission->statusHistories as $h)
                        <div class="relative flex gap-4">
                            <div class="absolute -left-[23px] top-1.5 z-10 w-4.5 h-4.5 rounded-full border-2 flex items-center justify-center shrink-0 {{ $loop->first ? 'bg-primary border-primary' : 'bg-white border-border-strong' }}">
                                @if($loop->first)<div class="w-1.5 h-1.5 rounded-full bg-white"></div>@endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if($h->from_status)
                                        <x-status-badge :status="$h->from_status" />
                                        <span class="text-text-muted">→</span>
                                    @endif
                                    <x-status-badge :status="$h->to_status" />
                                </div>
                                <p class="text-[12px] text-text-secondary mt-1">
                                    {{ $h->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} — oleh <span class="font-semibold text-text">{{ optional($h->changer)->name ?? 'System' }}</span>
                                </p>
                                @if($h->note)
                                    <div class="text-[14px] text-text-secondary mt-2 bg-slate-50 rounded-xl px-4 py-3 border border-border italic">
                                        "{{ $h->note }}"
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @endif
</x-layouts.app>