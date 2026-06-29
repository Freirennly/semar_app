<x-layouts.app :title="'Cek Dokumen: ' . $submission->title">

<div class="mb-8">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 mb-4 text-xs font-medium" style="color:#8E8CAD">
        <a href="{{ route('doccheck.index') }}"
           class="transition-colors duration-150 hover:text-primary hover:underline"
           style="color:#463EE3">Cek Dokumen</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color:#5A587A">{{ $submission->code }}</span>
    </nav>

    <div class="flex flex-wrap items-start gap-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1.5">
                <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
                <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Verifikasi Dokumen</p>
            </div>
            <h1 class="text-2xl font-bold tracking-tight leading-tight mb-2" style="color:#0F0E2E">
                {{ $submission->title }}
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <span class="font-mono text-[11px] px-2 py-1 rounded-lg"
                      style="background:#F5F5F5; color:#5A587A; border:1px solid rgba(0,0,0,0.06);">
                    {{ $submission->code }}
                </span>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold"
                         style="background:#F5F5F5; color:#5A587A;">
                        {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                    </div>
                    <span class="text-sm font-light" style="color:#5A587A">{{ $submission->student->name }}</span>
                </div>
                <x-status-badge :status="$submission->status" />
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Dokumen Panel --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border overflow-hidden"
         style="border-color:rgba(70,62,227,0.14);
                box-shadow: 0 2px 16px rgba(70,62,227,0.07);">

        <div class="flex items-center gap-2.5 px-5 py-4"
             style="border-bottom:1.5px solid rgba(70,62,227,0.08);
                    background:rgba(70,62,227,0.025);">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                 style="background:#E6E6FA; box-shadow:0 1px 4px rgba(70,62,227,0.15);">
                <svg class="w-4 h-4" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold" style="color:#0F0E2E">Dokumen yang Diupload</h3>

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
            <span class="ml-auto text-[10px] font-bold px-2.5 py-1 rounded-full"
                  style="background:{{ $allComplete ? 'rgba(34,197,94,0.12)' : '#E6E6FA' }};
                         color:{{ $allComplete ? '#15803d' : '#463EE3' }};">
                {{ $uploadedCnt }}/{{ $totalCnt }} dokumen
            </span>
        </div>

        <div class="divide-y" style="border-color:rgba(70,62,227,0.05);">
            @foreach($documentTemplates as $template)
                @php $doc = $submission->documents->firstWhere('document_template_id', $template->id); @endphp
                <div class="flex items-center justify-between px-5 py-4 transition-colors duration-150"
                     onmouseover="this.style.background='rgba(70,62,227,0.02)'"
                     onmouseout="this.style.background='transparent'">

                    <div class="flex items-center gap-3.5 min-w-0">
                        @if($doc)
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.2);">
                             <svg class="w-4 h-4" fill="none" stroke="#15803d" stroke-width="2.5" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                             </svg>
                        </div>
                        @else
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:{{ $template->is_required ? 'rgba(239,68,68,0.08)' : 'rgba(142,140,173,0.08)' }};
                                    border:1px solid {{ $template->is_required ? 'rgba(239,68,68,0.18)' : 'rgba(142,140,173,0.18)' }};">
                            <svg class="w-4 h-4" fill="none" stroke="{{ $template->is_required ? '#b91c1c' : '#5A587A' }}" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        @endif

                        <div class="min-w-0">
                            <p class="text-sm font-semibold" style="color:#0F0E2E">
                                {{ $template->name }}
                                @if($template->is_required)
                                    <span class="text-red-500 font-bold" title="Wajib">*</span>
                                @endif
                            </p>
                            @if($doc)
                                <p class="text-xs font-light truncate mt-0.5" style="color:#8E8CAD">{{ $doc->original_name }}</p>
                            @else
                                <p class="text-xs font-semibold mt-0.5" style="color:{{ $template->is_required ? '#b91c1c' : '#5A587A' }}">
                                    {{ $template->is_required ? 'Belum diupload' : 'Opsional' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($doc)
                        @if($doc->type === 'file')
                        <a href="{{ route('submissions.view-document', $doc->id) }}" target="_blank"
                           class="text-xs font-bold px-3.5 py-1.5 rounded-lg border flex-shrink-0 ml-4 inline-flex items-center gap-1.5 transition-all duration-150"
                           style="color:#463EE3; border-color:rgba(70,62,227,0.22); background:white; box-shadow:0 1px 4px rgba(70,62,227,0.08);"
                           onmouseover="this.style.background='#463EE3'; this.style.color='white'; this.style.borderColor='#463EE3'; this.style.boxShadow='0 3px 10px rgba(70,62,227,0.25)'"
                           onmouseout="this.style.background='white'; this.style.color='#463EE3'; this.style.borderColor='rgba(70,62,227,0.22)'; this.style.boxShadow='0 1px 4px rgba(70,62,227,0.08)'">
                            Lihat
                        </a>
                        @else
                        <a href="{{ $doc->file_path }}" target="_blank"
                           class="text-xs font-bold px-3.5 py-1.5 rounded-lg border flex-shrink-0 ml-4 inline-flex items-center gap-1.5 transition-all duration-150"
                           style="color:#463EE3; border-color:rgba(70,62,227,0.22); background:white; box-shadow:0 1px 4px rgba(70,62,227,0.08);"
                           onmouseover="this.style.background='#463EE3'; this.style.color='white'; this.style.borderColor='#463EE3'; this.style.boxShadow='0 3px 10px rgba(70,62,227,0.25)'"
                           onmouseout="this.style.background='white'; this.style.color='#463EE3'; this.style.borderColor='rgba(70,62,227,0.22)'; this.style.boxShadow='0 1px 4px rgba(70,62,227,0.08)'">
                            Buka Link
                        </a>
                        @endif
                    @else
                    <span class="text-[10px] font-bold px-3 py-1.5 rounded-lg flex-shrink-0 ml-4"
                          style="background:rgba(239,68,68,0.08); color:#b91c1c; border:1px solid rgba(239,68,68,0.15);">
                        Tidak ada
                    </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Aksi Panel --}}
    <div class="flex flex-col gap-4">

        {{-- Status card --}}
        <div class="bg-white rounded-2xl border overflow-hidden"
             style="border-color:{{ $submission->hasAllDocuments() ? 'rgba(34,197,94,0.25)' : 'rgba(245,158,11,0.25)' }};
                    box-shadow: 0 2px 12px {{ $submission->hasAllDocuments() ? 'rgba(34,197,94,0.08)' : 'rgba(245,158,11,0.08)' }};">
            <div class="px-5 py-4 flex items-center gap-3">
                @if($submission->hasAllDocuments())
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.2);">
                    <svg class="w-4 h-4" fill="none" stroke="#15803d" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold" style="color:#15803d">Dokumen Lengkap</p>
                    <p class="text-xs font-light" style="color:#5A587A">Siap untuk diproses</p>
                </div>
                @else
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.22);">
                    <svg class="w-4 h-4" fill="none" stroke="#b45309" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold" style="color:#b45309">Belum Lengkap</p>
                    <p class="text-xs font-light" style="color:#5A587A">
                        {{ $submission->getDocumentCount() }}/{{ $submission->getRequiredDocumentCount() }} dokumen tersedia
                    </p>
                </div>
                @endif
            </div>

            <div class="px-5 pb-4">
                <div class="w-full h-1.5 rounded-full overflow-hidden" style="background:rgba(0,0,0,0.06);">
                    <div class="h-full rounded-full transition-all duration-500"
                         style="width:{{ $calcProgress }}%; background:{{ $submission->hasAllDocuments() ? '#22C55E' : '#F59E0B' }};">
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions card --}}
        <div class="bg-white rounded-2xl border overflow-hidden"
             style="border-color:rgba(70,62,227,0.14);
                    box-shadow: 0 2px 16px rgba(70,62,227,0.07);">

            <div class="flex items-center gap-2.5 px-5 py-4"
                 style="border-bottom:1.5px solid rgba(70,62,227,0.08); background:rgba(70,62,227,0.025);">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                     style="background:#E6E6FA; box-shadow:0 1px 4px rgba(70,62,227,0.15);">
                    <svg class="w-4 h-4" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold" style="color:#0F0E2E">Keputusan</h3>
            </div>

            <div class="p-5 space-y-4">
                {{-- 🟢 FIX: Gunakan guard status ketat agar form hanya muncul saat berkas baru masuk atau hasil revisi --}}
                @if(in_array($submission->status, [\App\Enums\SubmissionStatus::NEW_PROPOSAL, \App\Enums\SubmissionStatus::REVISED]))

                    {{-- 1. Jalur Normal: Terima Dokumen untuk Lanjut ke Penugasan Reviewer --}}
                    <form method="POST" action="{{ route('doccheck.approve', $submission) }}">
                        @csrf
                        <button type="submit"
                                @if(!$submission->hasAllDocuments()) disabled @endif
                                class="w-full py-2.5 rounded-xl text-sm font-bold transition-all duration-150 flex items-center justify-center gap-2"
                                style="{{ $submission->hasAllDocuments()
                                    ? 'background:#463EE3; color:white; box-shadow:0 2px 8px rgba(70,62,227,0.28); cursor:pointer;'
                                    : 'background:#F5F5F5; color:#b0aec8; cursor:not-allowed; border:1px solid rgba(0,0,0,0.06);' }}"
                                @if($submission->hasAllDocuments())
                                onmouseover="this.style.background='#332DB8'; this.style.boxShadow='0 5px 16px rgba(70,62,227,0.35)'"
                                onmouseout="this.style.background='#463EE3'; this.style.boxShadow='0 2px 8px rgba(70,62,227,0.28)'"
                                @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Terima Dokumen
                        </button>
                    </form>

                    {{-- 2. Jalur Cepat Baru: Auto Approve Pengajuan Tanpa Melalui Reviewer --}}
                    <form method="POST" action="{{ route('doccheck.auto-approve', $submission) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui langsung pengajuan ini tanpa melalui penilaian Reviewer?')">
                        @csrf
                        <button type="submit"
                                @if(!$submission->hasAllDocuments()) disabled @endif
                                class="w-full py-2.5 rounded-xl text-sm font-bold transition-all duration-150 flex items-center justify-center gap-2 mt-3"
                                style="{{ $submission->hasAllDocuments()
                                    ? 'background:#059669; color:white; box-shadow:0 2px 8px rgba(5,150,105,0.28); cursor:pointer;'
                                    : 'background:#F5F5F5; color:#b0aec8; cursor:not-allowed; border:1px solid rgba(0,0,0,0.06);' }}"
                                @if($submission->hasAllDocuments())
                                onmouseover="this.style.background='#047857'; this.style.boxShadow='0 5px 16px rgba(5,150,105,0.35)'"
                                onmouseout="this.style.background='#059669'; this.style.boxShadow='0 2px 8px rgba(5,150,105,0.28)'"
                                @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Auto Approve Pengajuan
                        </button>
                    </form>

                    <div class="flex items-center gap-3 my-2">
                        <div class="flex-1 h-px" style="background:rgba(70,62,227,0.07);"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest" style="color:#b0aec8">atau</span>
                        <div class="flex-1 h-px" style="background:rgba(70,62,227,0.07);"></div>
                    </div>

                    {{-- 3. Jalur Penolakan: Kembalikan Berkas ke Draf Mahasiswa --}}
                    <form method="POST" action="{{ route('doccheck.return', $submission) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label for="note" class="block text-xs font-bold mb-1.5" style="color:#5A587A">
                                Catatan Pengembalian
                                <span style="color:#EF4444">*</span>
                            </label>
                            <textarea name="note" id="note" rows="3"
                                      class="w-full text-sm rounded-xl border px-3.5 py-2.5 outline-none transition-all duration-150 resize-none font-light"
                                      style="border-color:rgba(70,62,227,0.18); color:#0F0E2E; background:#fafafa;"
                                      placeholder="Jelaskan dokumen apa yang perlu diperbaiki..."
                                      onfocus="this.style.borderColor='#463EE3'; this.style.boxShadow='0 0 0 3px rgba(70,62,227,0.1)'; this.style.background='white'"
                                      onblur="this.style.borderColor='rgba(70,62,227,0.18)'; this.style.boxShadow='none'; this.style.background='#fafafa'">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="text-xs mt-1 font-medium" style="color:#b91c1c">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                class="w-full py-2.5 rounded-xl text-sm font-bold border transition-all duration-150 flex items-center justify-center gap-2"
                                style="color:#b45309; border-color:rgba(245,158,11,0.3); background:white; box-shadow:0 1px 4px rgba(245,158,11,0.1);"
                                onmouseover="this.style.background='rgba(245,158,11,0.08)'; this.style.borderColor='rgba(245,158,11,0.5)'; this.style.boxShadow='0 3px 10px rgba(245,158,11,0.15)'"
                                onmouseout="this.style.background='white'; this.style.borderColor='rgba(245,158,11,0.3)'; this.style.boxShadow='0 1px 4px rgba(245,158,11,0.1)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Kembalikan ke Student
                        </button>
                    </form>

                @else
                    {{-- Blok Tampilan Alternatif Jika Status Sudah Berubah --}}
                    <div class="p-4 bg-slate-50 border border-border rounded-xl text-center text-xs text-text-secondary italic">
                        Pengajuan ini telah melewati tahap verifikasi dokumen awal. Aksi keputusan dinonaktifkan.
                    </div>
                @endif
            </div>
        </div>

    </div>{{-- end aksi column --}}
</div>

</x-layouts.app>