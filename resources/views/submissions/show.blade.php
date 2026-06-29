<x-layouts.app :title="$submission->title">
    {{-- Atas: Navigasi Breadcrumb & Tombol Kembali Premium --}}
    <div class="mb-12 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in">
        <nav class="text-[12px] text-text-secondary" aria-label="Breadcrumb">
            <a href="{{ route('submissions.index') }}" class="hover:text-primary transition-colors">Pengajuan</a> 
            <span class="mx-1">/</span> 
            <span class="text-text font-medium">{{ $submission->code }}</span>
        </nav>
        
        {{-- Tombol Kembali --}}
        <a href="{{ route('submissions.index') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 border border-border bg-white hover:bg-slate-50 text-text-secondary hover:text-text text-[13px] font-semibold rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    {{-- Kotak Judul Premium --}}
    <div class="mb-12 p-6 bg-white border border-border rounded-2xl shadow-sm animate-fade-in">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2 max-w-3xl">
                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-text-secondary uppercase tracking-wider">
                    {{ $submission->type }}
                </span>
                <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3] font-serif">
                    {{ $submission->title }}
                </h1>
                <p class="text-[14px] text-text-secondary mt-2">
                    Registrasi: <span class="font-mono font-semibold text-text">{{ $submission->code }}</span> 
                    <span class="mx-1.5 text-border-strong">·</span> 
                    Oleh: <span class="font-semibold text-text">{{ $submission->student->name }}</span>
                </p>
            </div>
            <div class="shrink-0">
                <x-status-badge :status="$submission->status" />
            </div>
        </div>
    </div>

    {{-- Notifikasi Error Validasi --}}
    @if($errors->any())
        <div class="mb-6 bg-danger-bg border border-danger/20 text-danger rounded-xl px-5 py-3 text-[14px] shadow-sm">
            <p class="font-bold mb-1">Gagal Memproses File:</p>
            <ul class="list-disc pl-5 space-y-0.5 text-[12px]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tab Pilihan Konten --}}
    @php $tab = $tab ?? 'details'; @endphp
    <div class="border-b border-border mb-12">
        <nav class="flex gap-4 -mb-px" aria-label="Tabs">
            @foreach(['details' => 'Details', 'documents' => 'Dokumen', 'history' => 'Status History'] as $key => $label)
                <a href="{{ route('submissions.show', $submission) }}?tab={{ $key }}" class="px-4 py-3 text-[14px] font-medium border-b-2 transition-colors {{ $tab === $key ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text hover:border-border-strong' }}">{{ $label }}</a>
            @endforeach
        </nav>
    </div>

    {{-- KONTEN TAB 1: DETAILS --}}
    @if($tab === 'details')
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">
        <div class="lg:col-span-7 card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
            <h2 class="text-[24px] font-semibold text-text leading-[1.4] border-b border-border pb-2 mb-4">Informasi Pengajuan</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-[14px]">
                <div>
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">Kode</span>
                    <span class="font-semibold text-text mt-1 block font-mono">{{ $submission->code }}</span>
                </div>
                <div>
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">Jenis Kategori</span>
                    <span class="font-semibold text-text mt-1 block">{{ $submission->type }}</span>
                </div>
                <div>
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">Nama Pengaju</span>
                    <span class="font-semibold text-text mt-1 block">{{ $submission->student->name }}</span>
                </div>
                <div>
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">NIM / NIP</span>
                    <span class="font-semibold text-text mt-1 block">{{ $submission->student->nim_nip ?? '-' }}</span>
                </div>
                <div class="col-span-1 sm:col-span-2">
                    <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide mb-2">Abstrak Penelitian</span>
                    <div class="font-academic text-text p-4 bg-slate-50 border border-border rounded-xl whitespace-pre-line leading-relaxed">{{ $submission->abstract ?? 'Tidak ada abstrak.' }}</div>
                </div>
            </dl>
        </div>
        
        {{-- PANEL SIDEBAR AKSI MAHASISWA --}}
        <div class="lg:col-span-3 card p-6 bg-white border border-border rounded-2xl shadow-sm h-fit space-y-6">
            <h2 class="text-[24px] font-semibold text-text leading-[1.4] border-b border-border pb-2 mb-4">Checklist Dokumen</h2>
            
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
            {{-- Tombol Kirim Dokumen & Pengajuan Kembali (Mendukung Status DRAFT dan RESUBMISSION) --}}
            @if(in_array($submission->status, [\App\Enums\SubmissionStatus::DRAFT, \App\Enums\SubmissionStatus::RESUBMISSION]))
                @if($hasAllDocs)
                    <form method="POST" action="{{ route('submissions.submit', $submission) }}" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full btn-primary tracking-wide shadow-sm font-bold text-center">
                            {{ $submission->status === \App\Enums\SubmissionStatus::DRAFT ? 'Kirim Pengajuan Baru' : 'Kirim Revisi' }}
                        </button>
                    </form>
                @else
                    <div class="mt-6 bg-warning-bg border border-warning/20 rounded-xl px-4 py-3 text-[14px] text-warning" role="alert">
                        Upload semua dokumen wajib sebelum mengirimkan berkas. Buka tab <strong>Dokumen</strong> untuk melengkapi.
                    </div>
                @endif

                {{-- Kontrol Aksi Tambahan untuk Status DRAFT --}}
                @if($submission->status === \App\Enums\SubmissionStatus::DRAFT)
                    <div class="pt-4 border-t border-border mt-4 space-y-2">
                        <a href="{{ route('submissions.edit', $submission) }}" class="w-full text-center btn-outline text-xs py-2.5 block rounded-xl font-bold border-border hover:bg-slate-50 transition-colors">
                            Edit Informasi Penelitian
                        </a>

                        <form method="POST" action="{{ route('submissions.destroy', $submission) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen seluruh data pengajuan ini? Tindakan ini tidak bisa dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center border border-danger/20 bg-danger-bg text-danger hover:bg-danger/10 text-xs py-2.5 block rounded-xl font-bold transition-colors">
                                Hapus Pengajuan Permanen
                            </button>
                        </form>
                    </div>
                @endif
            @endif

            {{-- Formulir Konfirmasi Sertifikat EC --}}
            @if($submission->status === \App\Enums\SubmissionStatus::APPROVED && !empty($submission->ec_number))
                <div class="mt-6 border-t border-border pt-6 space-y-4">
                    <h3 class="text-sm font-bold text-text uppercase tracking-wider">Konfirmasi Ethical Clearance</h3>
                    <p class="text-xs text-text-secondary">Silakan periksa dan konfirmasi judul penelitian serta nama peneliti sebelum ditandatangani oleh Ketua.</p>
                    
                    <form method="POST" action="{{ route('submissions.confirm', $submission) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="confirmed_title" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Judul Penelitian</label>
                            <input type="text" name="confirmed_title" id="confirmed_title" value="{{ old('confirmed_title', $submission->title) }}" class="w-full bg-slate-50 border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors" required>
                        </div>
                        <div>
                            <label for="confirmed_researcher_name" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Nama Peneliti</label>
                            <input type="text" name="confirmed_researcher_name" id="confirmed_researcher_name" value="{{ old('confirmed_researcher_name', $submission->student->name) }}" class="w-full bg-slate-50 border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors" required>
                        </div>
                        <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-150">
                            Konfirmasi Data
                        </button>
                    </form>
                </div>
            @endif

            {{-- Fitur Batalkan Pengajuan (Recall ke Draf) --}}
            @if($submission->status === \App\Enums\SubmissionStatus::NEW_PROPOSAL && is_null($submission->secretary_id))
                <div class="mt-6 border-t border-border pt-6 space-y-3">
                    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 space-y-2">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-bold text-amber-800">Tarik Kembali Pengajuan?</h4>
                                <p class="text-xs text-amber-700 mt-1">Pengajuan ini akan dikembalikan ke status Draf dan bisa diedit kembali. Aksi ini hanya tersedia selama berkas belum ditugaskan ke Sekretaris.</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('submissions.cancel', $submission) }}" onsubmit="return confirm('Apakah Anda yakin ingin menarik dan membatalkan pengajuan ini? Berkas akan dikembalikan ke status Draf.')">
                            @csrf
                            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-150 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                                Tarik dan Batalkan Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            @endrole
        </div>
    </div>
    @endif

    {{-- KONTEN TAB 2: DOKUMEN PENDUKUNG --}}
    @if($tab === 'documents')
    <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
        <div>
            <h2 class="text-[24px] font-semibold text-text leading-[1.4] mb-4">Dokumen Pendukung</h2>
            <p class="text-[14px] text-text-secondary mt-1">Semua dokumen wajib harus diupload dalam format PDF (maks. 10MB).</p>
        </div>
        <div class="space-y-4">
            @foreach($documentTemplates as $template)
                @php
                    $doc = $submission->documents->firstWhere('document_template_id', $template->id);
                    $canUpload = auth()->user()->hasRole('student') && in_array($submission->status, [\App\Enums\SubmissionStatus::DRAFT, \App\Enums\SubmissionStatus::RESUBMISSION]);
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
                            <a href="{{ route('submissions.view-document', $doc) }}" target="_blank" class="btn-outline text-[12px] px-3 py-1.5">Lihat</a>
                            @if($canUpload)
                                <form method="POST" action="{{ route('submissions.delete-document', [$submission, $doc]) }}" class="inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="text-[12px] text-danger hover:text-danger/80 font-bold px-3 py-1.5 border border-danger/20 rounded-lg hover:bg-danger-bg transition-colors" onclick="return confirm('Hapus dokumen ini?')" aria-label="Hapus dokumen {{ $template->name }}">Hapus</button>
                                </form>
                            @endif
                        @endif
                        
                        @if($canUpload && !$doc)
                            <form method="POST" action="{{ route('submissions.upload-document', $submission) }}" enctype="multipart/form-data" class="w-full bg-soft-surface/50 p-4 rounded-xl border border-border mt-2">
                                @csrf
                                <input type="hidden" name="document_template_id" value="{{ $template->id }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                                    <div class="space-y-2">
                                        <label class="block text-[12px] font-semibold text-text-secondary">Unggah Berkas PDF</label>
                                        <input type="file" name="file" accept=".pdf" class="w-full text-[12px] text-text border border-border rounded-lg bg-white file:mr-3 file:py-1.5 file:px-3 file:border-0 file:text-[12px] file:font-semibold file:bg-slate-100 file:text-text-secondary hover:file:bg-slate-200 cursor-pointer" aria-label="Pilih file {{ $template->name }}">
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
    @endif

    {{-- KONTEN TAB 3: RIWAYAT HISTORI STATUS --}}
    @if($tab === 'history')
    <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
        <h2 class="text-[24px] font-semibold text-text leading-[1.4] border-b border-border pb-2 mb-4">Riwayat Status</h2>
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
                                        <span class="text-text-muted">-&gt;</span>
                                    @endif
                                    <x-status-badge :status="$h->to_status" />
                                </div>
                                <p class="text-[12px] text-text-secondary mt-1">
                                    {{ $h->created_at->format('d M Y, H:i') }} — oleh <span class="font-semibold text-text">{{ optional($h->changer)->name ?? 'System' }}</span>
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