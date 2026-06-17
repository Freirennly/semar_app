<x-layouts.app :title="'Dashboard'">
    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Selamat Datang, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-text-secondary mt-1.5">Kelola pengajuan etik penelitian dan pantau progress usulan Anda secara real-time.</p>
    </div>

    {{-- Ringkasan Pengajuan (Personal Stats, flat cards) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @foreach($metrics as $m)
            @if($m['label'] !== 'Proposal Baru') {{-- Avoid duplicate detail metrics --}}
                <div class="bg-white border border-border p-5 rounded-xl">
                    <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ $m['label'] }}</p>
                    <p class="text-[28px] font-bold text-text mt-2 leading-none">{{ $m['value'] }}</p>
                </div>
            @endif
        @endforeach
    </div>

    @php
        $latestSub = $submissions->first();
    @endphp

    @if($latestSub)
        {{-- Revision Alert --}}
        @if($latestSub->status === \App\Enums\SubmissionStatus::RESUBMISSION)
            <div class="mb-6 bg-warning-bg border border-warning/30 p-5 rounded-xl flex items-start gap-4">
                <div class="p-2 bg-white border border-warning/20 text-warning rounded-lg shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-warning">Perlu Tindakan: Revisi Proposal Diperlukan</h3>
                    <p class="text-xs text-text-secondary mt-1">Status pengajuan <strong>{{ $latestSub->code }}</strong> saat ini memerlukan perbaikan dokumen. Harap periksa catatan revisi di halaman detail dan unggah kembali dokumen yang diperbarui.</p>
                    <a href="{{ route('submissions.show', $latestSub) }}" class="inline-block mt-3 text-xs font-bold text-primary hover:underline">Lihat Catatan & Unggah Revisi →</a>
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
                } elseif (in_array($statusVal, ['APPROVED', 'APPROVED_WITH_REVISION', 'REJECTED', 'WAITING_SIGNATURE', 'RESUBMISSION', 'REVISED'])) {
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
                        <p class="text-[10px] text-text-secondary mt-0.5">Sertifikat Etik terbit.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Riwayat Pengajuan --}}
    <div class="bg-white border border-border rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h2 class="text-[20px] font-semibold text-text">Riwayat Pengajuan</h2>
            <a href="{{ route('submissions.create') }}" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-lg transition-colors">+ Buat Pengajuan Baru</a>
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
                        <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/70">
                            <th class="px-6 py-4 font-semibold">Kode</th>
                            <th class="px-6 py-4 font-semibold">Judul Penelitian</th>
                            <th class="px-6 py-4 font-semibold hidden sm:table-cell">Jenis Usulan</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold hidden sm:table-cell">Tanggal Diajukan</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($submissions as $sub)
                            <tr class="hover:bg-soft-surface/25 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-text">
                                        {{ \Illuminate\Support\Str::title($sub->title) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-text-secondary hidden sm:table-cell">{{ $sub->type }}</td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$sub->status" />
                                </td>
                                <td class="px-6 py-4 text-xs text-text-secondary hidden sm:table-cell whitespace-nowrap">{{ $sub->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('submissions.show', $sub) }}" class="text-primary hover:text-primary-hover font-bold">Lihat</a>
                                        @if($sub->status === \App\Enums\SubmissionStatus::APPROVED)
                                            <form action="{{ route('submissions.confirm', $sub) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-warning hover:text-amber-800 font-bold transition-colors" onclick="return confirm('Pastikan draf sertifikat sudah sesuai. Lanjutkan konfirmasi?')">
                                                    Konfirmasi Data
                                                </button>
                                            </form>
                                        @elseif($sub->status === \App\Enums\SubmissionStatus::DONE)
                                            <a href="{{ route('submissions.download-ec', $sub) }}" class="text-success hover:text-green-800 font-bold transition-colors flex items-center gap-1">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Sertifikat
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