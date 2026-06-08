<x-layouts.app :title="$submission->title">
    <div class="mb-6">
        <nav class="text-sm text-text-muted mb-2" aria-label="Breadcrumb"><a href="{{ route('submissions.index') }}" class="hover:text-primary">Pengajuan</a> <span class="mx-1">/</span> <span class="text-text">{{ $submission->code }}</span></nav>
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-bold text-text">{{ $submission->title }}</h2>
            <x-status-badge :status="$submission->status" />
        </div>
        <p class="text-sm text-text-secondary mt-1">{{ $submission->code }} · {{ $submission->type }} · {{ $submission->student->name }}</p>
    </div>

    {{-- KOTAK INDIKATOR EROR & SUKSES (Penting untuk melihat balikan dari Controller) --}}
    @if(session('success'))
        <div class="mb-6 bg-success-bg border border-success/20 text-success rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-danger-bg border border-danger/20 text-danger rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm">
            <p class="font-bold mb-1">⚠️ Gagal Memproses File:</p>
            <ul class="list-disc pl-5 space-y-1 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabs --}}
    @php $tab = $tab ?? 'details'; @endphp
    <div class="border-b border-border mb-6">
        <nav class="flex gap-0 -mb-px" aria-label="Tabs">
            @foreach(['details' => 'Details', 'documents' => 'Dokumen', 'history' => 'Status History'] as $key => $label)
            <a href="{{ route('submissions.show', $submission) }}?tab={{ $key }}" class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $tab === $key ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text hover:border-border-strong' }}">{{ $label }}</a>
            @endforeach
        </nav>
    </div>

    @if($tab === 'details')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6">
            <h3 class="text-base font-semibold text-text mb-4">Informasi Pengajuan</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-text-secondary text-xs font-medium block">Kode</span><span class="font-medium text-text mt-0.5 block">{{ $submission->code }}</span></div>
                <div><span class="text-text-secondary text-xs font-medium block">Jenis</span><span class="font-medium text-text mt-0.5 block">{{ $submission->type }}</span></div>
                <div><span class="text-text-secondary text-xs font-medium block">Pengaju</span><span class="font-medium text-text mt-0.5 block">{{ $submission->student->name }}</span></div>
                <div><span class="text-text-secondary text-xs font-medium block">NIM/NIP</span><span class="font-medium text-text mt-0.5 block">{{ $submission->student->nim_nip ?? '-' }}</span></div>
                <div class="col-span-1 sm:col-span-2"><span class="text-text-secondary text-xs font-medium block">Abstrak</span><span class="font-medium text-text mt-0.5 block whitespace-pre-line">{{ $submission->abstract ?? 'Tidak ada abstrak.' }}</span></div>
            </dl>
        </div>
        
        <div class="card p-6">
            <h3 class="text-base font-semibold text-text mb-4">Checklist Dokumen</h3>
            
            {{-- Penghitungan kelengkapan dokumen berbasis data dinamis --}}
            @php
                $requiredTemplateIds = $documentTemplates->where('is_required', true)->pluck('id')->toArray();
                $uploadedTemplateIds = $submission->documents->pluck('document_template_id')->toArray();
                $hasAllDocs = collect($requiredTemplateIds)->every(fn($id) => in_array($id, $uploadedTemplateIds));
            @endphp

            <p class="text-sm text-text-secondary mb-3">
                {{ $submission->documents->whereNotNull('document_template_id')->count() }} / {{ $documentTemplates->where('is_required', true)->count() }} dokumen wajib terupload
            </p>
            
            <div class="space-y-2.5">
                @foreach($documentTemplates as $template)
                @php $uploaded = in_array($template->id, $uploadedTemplateIds); @endphp
                <div class="flex items-center gap-2.5 py-1.5">
                    @if($uploaded)
                    <svg class="w-5 h-5 text-success shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    @else
                    <svg class="w-5 h-5 text-text-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    @endif
                    <span class="text-sm {{ $uploaded ? 'text-text font-medium' : 'text-text-muted' }} truncate max-w-[150px]">{{ $template->name }}</span>
                    <span class="text-xs {{ $uploaded ? 'text-success' : 'text-danger' }} ml-auto">{{ $uploaded ? 'Sudah' : 'Belum' }}</span>
                </div>
                @endforeach
            </div>

            @role('student')
            @if(in_array($submission->status, [\App\Enums\SubmissionStatus::DRAFT, \App\Enums\SubmissionStatus::RESUBMISSION]))
                @if($hasAllDocs)
                <form method="POST" action="{{ route('submissions.submit', $submission) }}" class="mt-5">
                    @csrf
                    <button type="submit" class="w-full btn-primary py-2.5">Submit Pengajuan</button>
                </form>
                @else
                <div class="mt-5 bg-warning-bg border border-warning/20 rounded-lg px-4 py-3 text-sm text-warning" role="alert">
                    Upload semua dokumen wajib sebelum submit. Buka tab <strong>Dokumen</strong> untuk mengupload.
                </div>
                @endif
            @endif
            @endrole
        </div>
    </div>
    @endif

    {{-- KONTEN TAB 2: DOKUMEN (DIUBAH MENJADI DINAMIS DATABASE) --}}
    @if($tab === 'documents')
    <div class="card p-6">
        <h3 class="text-base font-semibold text-text mb-2">Dokumen Pendukung</h3>
        <p class="text-sm text-text-secondary mb-5">Semua dokumen wajib harus diupload dalam format PDF (maks. 10MB).</p>
        <div class="space-y-4">
            @foreach($documentTemplates as $template)
            @php
                // Cari berkas mahasiswa berdasarkan id master template dokumen
                $doc = $submission->documents->firstWhere('document_template_id', $template->id);
                $canUpload = auth()->user()->hasRole('student') && in_array($submission->status, [\App\Enums\SubmissionStatus::DRAFT, \App\Enums\SubmissionStatus::RESUBMISSION]);
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-border rounded-lg {{ $doc ? 'bg-surface' : 'bg-bg' }}">
                <div class="flex items-center gap-3 min-w-0 mb-3 sm:mb-0">
                    @if($doc)
                    <div class="w-9 h-9 rounded-lg bg-success-bg flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg></div>
                    @else
                    <div class="w-9 h-9 rounded-lg bg-danger-bg flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg></div>
                    @endif
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-text">{{ $template->name }}</p>
                            @if($template->is_required)
                                <span class="text-[9px] font-extrabold px-1 bg-red-50 text-red-500 rounded border border-red-100 uppercase">Wajib</span>
                            @endif
                        </div>
                        @if($doc)<p class="text-xs text-text-muted mt-0.5">{{ $doc->original_name }} · {{ number_format($doc->size / 1024, 0) }} KB</p>
                        @else<p class="text-xs text-danger mt-0.5">Belum diupload</p>@endif
                    </div>
                </div>
                
                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    @if($doc)
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn-outline text-xs px-3 py-1.5">Lihat</a>
                        @if($canUpload)
                        <form method="POST" action="{{ route('submissions.delete-document', [$submission, $doc]) }}" class="inline">@csrf @method('DELETE')
                            <button type="submit" class="text-xs text-danger hover:text-danger/80 font-medium px-3 py-1.5 border border-danger/20 rounded-lg hover:bg-danger-bg transition-colors" onclick="return confirm('Hapus dokumen ini?')" aria-label="Hapus dokumen {{ $template->name }}">Hapus</button>
                        </form>
                        @endif
                    @endif
                    
                    {{-- Form Upload yang Baru: Mengirimkan document_template_id --}}
                    @if($canUpload && !$doc)
                    <form method="POST" action="{{ route('submissions.upload-document', $submission) }}" enctype="multipart/form-data" class="flex items-center gap-2">@csrf
                        <input type="hidden" name="document_template_id" value="{{ $template->id }}">
                        <input type="file" name="file" accept=".pdf" required class="text-xs text-text-secondary file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-soft-surface file:text-primary hover:file:bg-info-soft" aria-label="Pilih file {{ $template->name }}">
                        <button type="submit" class="btn-primary text-xs px-3 py-1.5">Upload</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($tab === 'history')
    <div class="card p-6">
        <h3 class="text-base font-semibold text-text mb-4">Riwayat Status</h3>
        @if($submission->statusHistories->isEmpty())
        <p class="text-sm text-text-secondary">Belum ada riwayat.</p>
        @else
        <div class="relative">
            <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-border"></div>
            @foreach($submission->statusHistories as $h)
            <div class="relative flex gap-4 pb-6 last:pb-0">
                <div class="relative z-10 w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 {{ $loop->first ? 'bg-primary border-primary' : 'bg-surface border-border-strong' }}">
                    @if($loop->first)<div class="w-2 h-2 rounded-full bg-white"></div>@endif
                </div>
                <div class="flex-1 pt-0.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($h->from_status)<x-status-badge :status="$h->from_status" /><span class="text-text-muted">→</span>@endif
                        <x-status-badge :status="$h->to_status" />
                    </div>
                    <p class="text-xs text-text-muted mt-1">{{ $h->created_at->format('d M Y, H:i') }} — oleh {{ optional($h->changer)->name ?? 'System' }}</p>
                    @if($h->note)<p class="text-sm text-text-secondary mt-1 bg-soft-surface rounded-lg px-3 py-2">"{{ $h->note }}"</p>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif
</x-layouts.app>