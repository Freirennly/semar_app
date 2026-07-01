<x-layouts.app :title="'Dashboard'">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-start justify-between mb-6 gap-4">
        <div>
            <nav class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">
                Home <span class="mx-1">/</span> Dashboard
            </nav>
            <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Selamat Datang, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="text-sm text-text-secondary mt-1">Kelola pengajuan etik penelitian dan pantau progress usulan Anda.</p>
        </div>
        <div class="flex flex-col md:items-end gap-3">
            <div class="text-sm font-semibold text-text-secondary hidden md:block">
                {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('submissions.create') }}" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold px-4 py-2 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Pengajuan Baru
                </a>
            </div>
        </div>
    </div>

    {{-- Ringkasan Pengajuan (Personal Stats, flat cards) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @foreach($metrics as $m)
            @if($m['label'] !== 'Proposal Baru') {{-- Avoid duplicate detail metrics --}}
                <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
                    <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">{{ $m['label'] }}</p>
                    <p class="text-3xl font-bold text-text leading-none">{{ $m['value'] }}</p>
                </div>
            @endif
        @endforeach
    </div>

    @php
        $latestSub = $submissions->first();
    @endphp

    @if($latestSub)
        {{-- Revision Alert --}}
        @if($latestSub->status === \App\Enums\SubmissionStatus::REVISION_REQUIRED)
            <x-alert type="warning" title="Perlu Tindakan: Revisi Proposal Diperlukan" class="mb-6">
                <p class="mt-1">Status pengajuan <strong>{{ $latestSub->code }}</strong> saat ini memerlukan perbaikan dokumen. Harap periksa catatan revisi di halaman detail dan unggah kembali dokumen yang diperbarui.</p>
                <a href="{{ route('submissions.show', $latestSub) }}" class="inline-block mt-2 text-xs font-bold text-primary hover:underline">Lihat Catatan & Unggah Revisi →</a>
            </x-alert>
        @endif

        {{-- Document Completion Checklist --}}
        @if(isset($docCompletionData) && $docCompletionData)
            <div class="bg-white border border-border p-6 rounded-xl mb-6">
                <div class="border-b border-border pb-3 mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-text uppercase tracking-wider">Kelengkapan Dokumen</h2>
                        <p class="text-xs text-text-secondary mt-0.5">Pengajuan: <span class="font-semibold text-text">{{ $docCompletionData['submission']->code }}</span></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold {{ $docCompletionData['completion_pct'] >= 100 ? 'text-success' : 'text-primary' }}">
                            {{ $docCompletionData['uploaded_count'] }} / {{ $docCompletionData['total_required'] }} Dokumen Wajib
                        </span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $docCompletionData['completion_pct'] >= 100 ? 'bg-emerald-50 text-emerald-700' : 'bg-soft-surface text-primary' }}">
                            {{ $docCompletionData['completion_pct'] }}%
                        </span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="w-full h-2 rounded-full bg-soft-surface mb-4 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $docCompletionData['completion_pct'] >= 100 ? 'bg-success' : 'bg-primary' }}"
                         style="width: {{ $docCompletionData['completion_pct'] }}%"></div>
                </div>

                {{-- Checklist --}}
                <div class="space-y-2">
                    @foreach($docCompletionData['required'] as $tpl)
                        <div class="flex items-center gap-3 px-3 py-2 rounded-lg {{ in_array($tpl->id, $docCompletionData['uploaded_ids']) ? 'bg-emerald-50/50' : 'bg-red-50/50' }}">
                            @if(in_array($tpl->id, $docCompletionData['uploaded_ids']))
                                <svg class="w-4 h-4 text-success flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-xs font-medium text-text">{{ $tpl->name }}</span>
                                <span class="ml-auto text-[10px] font-bold text-success">Diunggah</span>
                            @else
                                <svg class="w-4 h-4 text-danger flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span class="text-xs font-medium text-text">{{ $tpl->name }}</span>
                                <span class="ml-auto text-[10px] font-bold text-danger">Belum Diunggah</span>
                            @endif
                        </div>
                    @endforeach
                    @foreach($docCompletionData['optional'] as $tpl)
                        <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-soft-surface/30">
                            @if(in_array($tpl->id, $docCompletionData['uploaded_ids']))
                                <svg class="w-4 h-4 text-success flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-xs font-medium text-text">{{ $tpl->name }}</span>
                                <span class="ml-auto text-[10px] font-bold text-success">Diunggah</span>
                            @else
                                <svg class="w-4 h-4 text-warning flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span class="text-xs font-medium text-text-secondary">{{ $tpl->name }}</span>
                                <span class="ml-auto text-[10px] font-bold text-text-muted">Opsional</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Timeline Section (Flat & Elegant) --}}
        <div class="bg-white border border-border p-6 rounded-xl mb-6">
            <div class="border-b border-border pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-[20px] font-semibold text-text">Status Penelitian Aktif</h2>
                    <p class="text-xs text-text-secondary mt-0.5">Judul: <span class="font-semibold text-text">{{ \Illuminate\Support\Str::title($latestSub->title) }}</span></p>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <span class="text-xs text-text-secondary">Status saat ini:</span>
                    <x-status-badge :status="$latestSub->status" />
                </div>
            </div>

            @php
                $statusVal = $latestSub->status->value ?? $latestSub->status;
                
                // Map status to visual step index (1-5)
                $activeStep = 1;
                if (in_array($statusVal, ['PROCESS'])) {
                    $activeStep = 2;
                } elseif (in_array($statusVal, ['ON_REVIEW'])) {
                    $activeStep = 3;
                } elseif (in_array($statusVal, ['APPROVED', 'REJECTED', 'WAITING_SIGNATURE', 'REVISION_REQUIRED', 'REVISED'])) {
                    $activeStep = 4;
                } elseif (in_array($statusVal, ['DONE'])) {
                    $activeStep = 5;
                }
            @endphp

            {{-- Timeline steps --}}
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 relative">
                {{-- Step 1 --}}
                <div class="flex sm:flex-col items-center gap-3 text-left sm:text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 border {{ $activeStep >= 1 ? 'bg-primary text-white border-primary' : 'bg-bg text-text-muted border-border' }}">1</div>
                    <div>
                        <p class="text-xs font-bold {{ $activeStep >= 1 ? 'text-text' : 'text-text-muted' }}">Diajukan</p>
                        <p class="text-[10px] text-text-secondary mt-0.5">Usulan berhasil dikirim.</p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex sm:flex-col items-center gap-3 text-left sm:text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 border {{ $activeStep >= 2 ? 'bg-primary text-white border-primary' : 'bg-bg text-text-muted border-border' }}">2</div>
                    <div>
                        <p class="text-xs font-bold {{ $activeStep >= 2 ? 'text-text' : 'text-text-muted' }}">Validasi Dokumen</p>
                        <p class="text-[10px] text-text-secondary mt-0.5">Pemeriksaan berkas oleh sekretariat.</p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="flex sm:flex-col items-center gap-3 text-left sm:text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 border {{ $activeStep >= 3 ? 'bg-primary text-white border-primary' : 'bg-bg text-text-muted border-border' }}">3</div>
                    <div>
                        <p class="text-xs font-bold {{ $activeStep >= 3 ? 'text-text' : 'text-text-muted' }}">Penilaian Reviewer</p>
                        <p class="text-[10px] text-text-secondary mt-0.5">Proses review substansi etik.</p>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="flex sm:flex-col items-center gap-3 text-left sm:text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 border {{ $activeStep >= 4 ? 'bg-primary text-white border-primary' : 'bg-bg text-text-muted border-border' }}">4</div>
                    <div>
                        <p class="text-xs font-bold {{ $activeStep >= 4 ? 'text-text' : 'text-text-muted' }}">Keputusan Sidang</p>
                        <p class="text-[10px] text-text-secondary mt-0.5">Penyusunan rekomendasi akhir.</p>
                    </div>
                </div>

                {{-- Step 5 --}}
                <div class="flex sm:flex-col items-center gap-3 text-left sm:text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 border {{ $activeStep >= 5 ? 'bg-primary text-white border-primary' : 'bg-bg text-text-muted border-border' }}">5</div>
                    <div>
                        <p class="text-xs font-bold {{ $activeStep >= 5 ? 'text-text' : 'text-text-muted' }}">Selesai</p>
                        <p class="text-[10px] text-text-secondary mt-0.5">Surat Kelayakan Etik terbit.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Queue: Waiting EC Confirmation --}}
    @if(isset($waitingEcConfirmation) && $waitingEcConfirmation->isNotEmpty())
        <div class="mb-6 bg-white border border-border rounded-xl p-6 animate-fade-in">
            <h2 class="text-sm font-bold text-text uppercase tracking-wider mb-4">Menunggu Konfirmasi Ethical Clearance</h2>
            <div class="space-y-4">
                @foreach($waitingEcConfirmation as $sub)
                    <div class="border border-border rounded-lg p-4 hover:border-primary/50 transition-colors">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-text">{{ \Illuminate\Support\Str::title($sub->title) }}</h3>
                                <p class="text-xs text-text-secondary mt-1">Draft Ethical Clearance telah dikirim oleh Admin. Silakan lakukan pemeriksaan sebelum Ketua melakukan penandatanganan.</p>
                            </div>
                            <a href="{{ route('submissions.show', $sub) }}" class="inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-white text-xs font-bold px-4 py-2 rounded-lg transition-all shrink-0">
                                Lihat Ethical Clearance
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Queue: Ethical Clearance Ready --}}
    @if(isset($ecReady) && $ecReady->isNotEmpty())
        <div class="mb-6 bg-white border border-border rounded-xl p-6 animate-fade-in">
            <h2 class="text-sm font-bold text-text uppercase tracking-wider mb-4 text-success">Surat Kelayakan Etik Terbit (Ethical Clearance Ready)</h2>
            <div class="space-y-4">
                @foreach($ecReady as $sub)
                    <div class="border border-border rounded-lg p-4 hover:border-success/30 transition-colors">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-text">{{ \Illuminate\Support\Str::title($sub->title) }}</h3>
                                <p class="text-xs text-text-secondary mt-1">Nomor EC: {{ $sub->ec_number }}</p>
                            </div>
                            <a href="{{ route('submissions.certificate', $sub) }}" class="inline-flex items-center justify-center bg-success hover:bg-success/90 text-white text-xs font-bold px-4 py-2 rounded-lg transition-all shrink-0">
                                Unduh Surat Kelayakan Etik
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Recent Downloads --}}
    @if(isset($recentDownloads) && $recentDownloads->isNotEmpty())
        <div class="mb-6 bg-white border border-border rounded-xl overflow-hidden animate-fade-in">
            <div class="px-6 py-4 border-b border-border bg-slate-50/70">
                <h2 class="text-sm font-bold text-text uppercase tracking-wider text-primary">Riwayat Unduhan Terakhir (Recent Downloads)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-border">
                        @foreach($recentDownloads as $log)
                            <tr class="hover:bg-soft-surface/25 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-text-secondary w-24">
                                    {{ $log->submission ? $log->submission->code : 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-text">
                                        {{ $log->submission ? \Illuminate\Support\Str::title($log->submission->title) : 'Unknown Submission' }}
                                    </span>
                                    <span class="text-xs text-text-secondary block mt-0.5">{{ $log->description }}</span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap text-xs text-text-secondary w-40">
                                    {{ $log->created_at->timezone('Asia/Jakarta')->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Riwayat Pengajuan --}}
    <div class="bg-white border border-border rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-border flex items-center justify-between">
            <h2 class="text-sm font-bold text-text uppercase tracking-wider">Riwayat Pengajuan</h2>
            <a href="{{ route('submissions.create') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
        </div>
        @if($submissions->isEmpty())
            <div class="text-center py-16 px-6">
                <div class="w-14 h-14 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-text">Belum ada pengajuan usulan</h3>
                <p class="text-xs text-text-secondary mt-1">Silakan klik tombol di atas untuk mengajukan protokol etik baru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                            <th class="px-5 py-3 font-semibold">Kode</th>
                            <th class="px-5 py-3 font-semibold">Judul Penelitian</th>
                            <th class="px-5 py-3 font-semibold hidden sm:table-cell">Jenis Usulan</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold hidden sm:table-cell">Tanggal Diajukan</th>
                            <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($submissions as $sub)
                            <tr class="hover:bg-soft-surface/50 transition-colors">
                                <td class="px-5 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                <td class="px-5 py-3">
                                    <span class="font-semibold text-text line-clamp-1" title="{{ $sub->title }}">
                                        {{ \Illuminate\Support\Str::title($sub->title) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-text-secondary hidden sm:table-cell">{{ $sub->type }}</td>
                                <td class="px-5 py-3">
                                    <x-status-badge :status="$sub->status" />
                                </td>
                                <td class="px-5 py-3 text-xs text-text-secondary hidden sm:table-cell whitespace-nowrap">{{ $sub->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('submissions.show', $sub) }}" class="text-xs font-bold text-primary hover:underline">Detail</a>
                                        @if($sub->status === \App\Enums\SubmissionStatus::DONE)
                                            <a href="{{ route('submissions.download-ec', $sub) }}" class="text-success hover:text-green-800 font-bold transition-colors flex items-center gap-1">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Unduh
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>