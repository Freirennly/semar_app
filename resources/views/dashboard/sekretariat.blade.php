<x-layouts.app :title="'Dashboard Sekretariat'">
    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Dashboard Sekretariat</h1>
        <p class="text-sm text-text-secondary mt-1.5">Kelola verifikasi dokumen persyaratan pengajuan dan rekomendasi akhir Komite Etik Penelitian.</p>
    </div>

    {{-- Ringkasan Tugas (Flat cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div class="bg-white border border-border p-5 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Perlu Cek Dokumen</p>
            <p class="text-[28px] font-bold text-warning mt-2 leading-none">{{ $submitted->count() }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Menunggu Keputusan Akhir</p>
            <p class="text-[28px] font-bold text-primary mt-2 leading-none">{{ $pendingDecision->count() }}</p>
        </div>
    </div>

    @php
        // Fetch recent validation history directly from database to avoid modifying controller
        $recentValidations = \App\Models\Submission::whereNotIn('status', [
            \App\Enums\SubmissionStatus::NEW_PROPOSAL,
            \App\Enums\SubmissionStatus::REVISED
        ])
        ->with('student')
        ->latest('updated_at')
        ->limit(6)
        ->get();
    @endphp

    {{-- Main Grid (70/30 Split) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Tasks (70% or lg:col-span-2) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Perlu Cek Dokumen --}}
            <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-[20px] font-semibold text-text">Perlu Cek Dokumen</h2>
                    <a href="{{ route('doccheck.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua →</a>
                </div>
                @if($submitted->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada dokumen baru yang perlu divalidasi saat ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/70">
                                    <th class="px-6 py-4 font-semibold">Kode</th>
                                    <th class="px-6 py-4 font-semibold">Judul Usulan</th>
                                    <th class="px-6 py-4 font-semibold">Pengusul</th>
                                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($submitted as $sub)
                                    <tr class="hover:bg-soft-surface/25 transition-colors">
                                        <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-6 py-4 font-semibold text-text truncate max-w-[200px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-6 py-4 text-text-secondary">{{ optional($sub->student)->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('doccheck.show', $sub) }}" class="px-3.5 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-lg transition-colors">
                                                Cek Dokumen
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Menunggu Keputusan --}}
            <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-[20px] font-semibold text-text">Menunggu Keputusan Akhir</h2>
                    <a href="{{ route('decisions.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua →</a>
                </div>
                @if($pendingDecision->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada pengajuan yang menunggu keputusan akhir.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/70">
                                    <th class="px-6 py-4 font-semibold">Kode</th>
                                    <th class="px-6 py-4 font-semibold">Judul Usulan</th>
                                    <th class="px-6 py-4 font-semibold">Penilaian</th>
                                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($pendingDecision as $sub)
                                    <tr class="hover:bg-soft-surface/25 transition-colors">
                                        <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-6 py-4 font-semibold text-text truncate max-w-[200px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-text-secondary">
                                            <span class="font-bold text-primary">{{ $sub->reviews->count() }}</span> Masuk
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('decisions.show', $sub) }}" class="px-3.5 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-lg transition-colors">
                                                Putuskan
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Recent Validation History (30% or lg:col-span-1) --}}
        <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-border">
                <h2 class="text-[20px] font-semibold text-text">Riwayat Validasi</h2>
            </div>
            
            @if($recentValidations->isEmpty())
                <div class="text-center py-12 px-6">
                    <p class="text-xs text-text-muted italic">Belum ada riwayat validasi.</p>
                </div>
            @else
                <div class="divide-y divide-border overflow-y-auto max-h-[500px]">
                    @foreach($recentValidations as $sub)
                        <div class="p-4 hover:bg-soft-surface/20 transition-colors">
                            <p class="text-xs font-semibold text-text line-clamp-2">{{ \Illuminate\Support\Str::title($sub->title) }}</p>
                            <p class="text-[10px] text-text-secondary mt-1">Pengusul: {{ optional($sub->student)->name ?? '-' }}</p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-[10px] text-text-muted font-mono">{{ $sub->code }}</span>
                                <x-status-badge :status="$sub->status" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>