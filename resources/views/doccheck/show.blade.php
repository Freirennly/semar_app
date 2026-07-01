<x-layouts.app :title="'Cek Dokumen: ' . $submission->title">

<div class="mb-6">
    <nav class="flex items-center gap-2 mb-2 text-xs font-semibold text-text-secondary uppercase tracking-wider">
        <a href="{{ route('doccheck.index') }}" class="hover:text-primary transition-colors">Cek Dokumen</a>
        <span>/</span>
        <span>{{ $submission->code }}</span>
    </nav>
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <h1 class="text-[24px] md:text-[28px] font-bold text-text tracking-tight mb-2">{{ $submission->title }}</h1>
            <div class="flex flex-wrap items-center gap-3">
                <span class="font-mono text-[11px] font-semibold text-text-secondary px-2 py-1 rounded bg-soft-surface border border-border">
                    {{ $submission->code }}
                </span>
                <span class="text-sm text-text-secondary font-medium">
                    {{ $submission->student->name }}
                </span>
                <x-status-badge :status="$submission->status" />
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Dokumen Panel ──────────────────────────── --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-border overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border bg-slate-50/50">
            <h3 class="text-sm font-bold text-text uppercase tracking-wider">Dokumen yang Diupload</h3>
            @php
                $documentTemplates = \App\Models\DocumentTemplate::visible()->get();
                $uploadedCnt = 0;
                foreach ($documentTemplates as $template) {
                    if ($submission->documents->firstWhere('document_template_id', $template->id)) {
                        $uploadedCnt++;
                    }
                }
                $totalCnt    = count($documentTemplates);
                $allComplete = $uploadedCnt >= $totalCnt;
                
                $requiredCount = $submission->getRequiredDocumentCount() > 0 ? $submission->getRequiredDocumentCount() : 1;
                $calcProgress = min(100, round(($submission->getDocumentCount() / $requiredCount) * 100));
            @endphp
            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $allComplete ? 'bg-success/10 text-success' : 'bg-primary/10 text-primary' }}">
                {{ $uploadedCnt }}/{{ $totalCnt }} dokumen
            </span>
        </div>

        <div class="divide-y divide-border">
            @foreach($documentTemplates as $template)
                @php $doc = $submission->documents->firstWhere('document_template_id', $template->id); @endphp
                <div class="flex items-center justify-between px-6 py-4 hover:bg-soft-surface/50 transition-colors">
                    <div class="flex items-center gap-4 min-w-0">
                        @if($doc)
                        <div class="w-8 h-8 rounded-full flex items-center justify-center bg-success text-white shrink-0">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        @else
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 border {{ $template->is_required ? 'bg-danger/10 border-danger/20 text-danger' : 'bg-bg border-border text-text-muted' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        </div>
                        @endif

                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-text">
                                {{ $template->name }}
                                @if($template->is_required)
                                    <span class="text-danger font-bold ml-1">*</span>
                                @endif
                            </p>
                            @if($doc)
                                <p class="text-xs text-text-secondary truncate mt-0.5">{{ $doc->original_name }}</p>
                            @else
                                <p class="text-xs font-semibold mt-0.5 {{ $template->is_required ? 'text-danger' : 'text-text-muted' }}">
                                    {{ $template->is_required ? 'Belum diupload' : 'Opsional' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($doc)
                        @if($doc->type === 'file')
                        <a href="{{ route('submissions.view-document', [$submission, $doc]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold bg-soft-surface hover:bg-border text-primary rounded-lg transition-colors ml-4 shrink-0">
                            Lihat
                        </a>
                        @else
                        <a href="{{ $doc->file_path }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold bg-soft-surface hover:bg-border text-primary rounded-lg transition-colors ml-4 shrink-0">
                            Buka Link
                        </a>
                        @endif
                    @else
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg shrink-0 ml-4 bg-danger/10 text-danger">
                        Tidak ada
                    </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Aksi Panel ─────────────────────────────── --}}
    <div class="flex flex-col gap-6">
        {{-- Status card --}}
        <div class="bg-white rounded-xl border border-border p-5">
            <div class="flex items-center gap-3 mb-4">
                @if($submission->hasAllDocuments())
                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-success text-white shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-success">Dokumen Lengkap</p>
                    <p class="text-xs text-text-secondary mt-0.5">Siap untuk diproses</p>
                </div>
                @else
                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-warning text-white shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-warning">Belum Lengkap</p>
                    <p class="text-xs text-text-secondary mt-0.5">
                        {{ $submission->getDocumentCount() }}/{{ $submission->getRequiredDocumentCount() }} dokumen tersedia
                    </p>
                </div>
                @endif
            </div>

            <div class="w-full h-2 rounded-full overflow-hidden bg-bg border border-border">
                <div class="h-full rounded-full transition-all duration-500 {{ $submission->hasAllDocuments() ? 'bg-success' : 'bg-warning' }}" style="width:{{ $calcProgress }}%;"></div>
            </div>
        </div>

        {{-- Actions card --}}
        <div class="bg-white rounded-xl border border-border overflow-hidden">
            <div class="px-6 py-4 border-b border-border bg-slate-50/50">
                <h3 class="text-sm font-bold text-text uppercase tracking-wider">Keputusan</h3>
            </div>
            <div class="p-5 space-y-5">
                @php
                    $isVerified = $submission->status === \App\Enums\SubmissionStatus::PROCESS && $submission->activityLogs()->where('description', 'like', '%Dokumen dinyatakan lengkap%')->exists();
                    $canApprove = $submission->hasAllDocuments();
                @endphp
                
                @if($isVerified)
                    <div class="w-full py-2.5 rounded-xl text-sm font-bold bg-success/10 text-success border border-success/20 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Dokumen Telah Diterima
                    </div>
                @else
                    <form method="POST" action="{{ route('doccheck.approve', $submission) }}">
                        @csrf
                        <button type="button" @if(!$canApprove) disabled @endif class="w-full py-2.5 rounded-xl text-sm font-bold transition-colors flex items-center justify-center gap-2 {{ $canApprove ? 'bg-primary hover:bg-primary-hover text-white' : 'bg-bg text-text-muted cursor-not-allowed border border-border' }}" @if($canApprove) onclick="event.preventDefault(); window.confirmModal('Terima dokumen ini?', this.closest('form'));" @endif>
                            Terima Dokumen
                        </button>
                    </form>

                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-px bg-border"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-text-muted">atau</span>
                        <div class="flex-1 h-px bg-border"></div>
                    </div>

                    <form method="POST" action="{{ route('doccheck.return', $submission) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="note" class="block text-xs font-bold mb-1.5 text-text-secondary">
                                Catatan Pengembalian <span class="text-danger">*</span>
                            </label>
                            <textarea name="note" id="note" rows="3" class="input-field" placeholder="Jelaskan dokumen apa yang perlu diperbaiki...">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="text-xs mt-1 font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="button" class="w-full py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 border border-danger/30 text-danger hover:bg-danger/10 transition-colors" onclick="event.preventDefault(); window.confirmModal('Kembalikan dokumen ke pengusul?', this.closest('form'));">
                            Kembalikan ke Pengusul
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
</x-layouts.app>