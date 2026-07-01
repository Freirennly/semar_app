<x-layouts.app :title="'Admin Dashboard Overview'">
    {{-- 1. Header Section with Quick Actions --}}
    <div class="flex flex-col md:flex-row md:items-start justify-between mb-6 gap-4">
        <div>
            <nav class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">
                Home <span class="mx-1">/</span> Dashboard
            </nav>
            <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Overview</h1>
        </div>
        <div class="flex flex-col items-end gap-3">
            <div class="text-sm font-semibold text-text-secondary">
                {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.users.index') }}?role=reviewer" class="text-xs font-semibold text-text hover:text-primary px-3 py-1.5 border border-border rounded transition-colors bg-white hover:border-primary">
                    Kelola Reviewer
                </a>
                <a href="{{ route('admin.announcements.index') }}" class="text-xs font-semibold text-text hover:text-primary px-3 py-1.5 border border-border rounded transition-colors bg-white hover:border-primary">
                    Pengumuman
                </a>
                <a href="{{ route('admin.templates.index') }}" class="text-xs font-semibold text-text hover:text-primary px-3 py-1.5 border border-border rounded transition-colors bg-white hover:border-primary">
                    Template Dokumen
                </a>
                <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-text hover:text-primary px-3 py-1.5 border border-border rounded transition-colors bg-white hover:border-primary">
                    Laporan
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Executive Summary (4 Cards, Monochrome text) --}}
    @php
        $activeCount = $metrics['process'] + $metrics['on_review'] + $metrics['revision_required'] + $metrics['revised'];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Total Proposal</p>
            <p class="text-3xl font-bold text-text">{{ number_format($metrics['total_submissions']) }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Proposal Aktif</p>
            <p class="text-3xl font-bold text-text">{{ number_format($activeCount) }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Proposal Selesai</p>
            <p class="text-3xl font-bold text-text">{{ number_format($metrics['done']) }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Proposal Ditolak</p>
            <p class="text-3xl font-bold text-text">{{ number_format($metrics['rejected']) }}</p>
        </div>
    </div>



    {{-- Main Grid (70/30 Split) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Left: Proposal Terbaru & Aktivitas (70% or lg:col-span-2) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Proposal Terbaru --}}
            <div class="bg-white border border-border rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Proposal Terbaru</h2>
                    <a href="{{ route('admin.proposals.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                                <th class="px-5 py-3 font-semibold">Kode</th>
                                <th class="px-5 py-3 font-semibold">Judul</th>
                                <th class="px-5 py-3 font-semibold">Mahasiswa</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold">Tanggal</th>
                                <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-white">
                            @forelse($latestSubmissions as $sub)
                            <tr class="hover:bg-soft-surface/50 transition-colors">
                                <td class="px-5 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                <td class="px-5 py-3">
                                    <span class="font-semibold text-text line-clamp-1" title="{{ $sub->title }}">{{ \Illuminate\Support\Str::title($sub->title) }}</span>
                                </td>
                                <td class="px-5 py-3 text-text-secondary truncate max-w-[120px]">{{ optional($sub->student)->name ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    <x-status-badge :status="$sub->status" />
                                </td>
                                <td class="px-5 py-3 text-xs text-text-secondary whitespace-nowrap">{{ $sub->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('admin.proposals.show', $sub) }}" class="text-xs font-bold text-primary hover:underline">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-text-muted italic">Tidak ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Aktivitas Terbaru --}}
            <div class="bg-white border border-border rounded-lg overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-border">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Aktivitas Terbaru</h2>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-border">
                        @forelse($latestActivities->take(5) as $activity)
                        <li class="px-5 py-3 hover:bg-soft-surface/50 transition-colors text-xs text-text-secondary">
                            <span class="font-bold text-text">{{ optional($activity->changer)->name ?? 'Sistem' }}</span> 
                            &bull;
                            Mengubah status <span class="font-semibold text-text">{{ optional($activity->submission)->code ?? 'Proposal' }}</span> 
                            menjadi <span class="font-semibold">{{ \App\Enums\SubmissionStatus::tryFrom($activity->to_status)?->label() ?? $activity->to_status }}</span>
                            &bull;
                            <span class="text-text-muted">{{ $activity->created_at->timezone('Asia/Jakarta')->diffForHumans() }}</span>
                        </li>
                        @empty
                        <li class="px-5 py-8 text-center text-text-muted italic">Belum ada aktivitas.</li>
                        @endforelse
                    </ul>
                </div>
                @if($latestActivities->count() > 5)
                <div class="px-5 py-3 border-t border-border bg-slate-50/50 text-center">
                    <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat semua aktivitas</a>
                </div>
                @endif
            </div>
        </div>

        {{-- Kanan: Work Queue --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Work Queue --}}
            <div class="bg-white border border-border rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-border">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Work Queue</h2>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-border">
                            <tr class="hover:bg-soft-surface/50 transition-colors group">
                                <td class="px-5 py-4 font-semibold text-text-secondary">Proposal Baru</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.proposals.index') }}?status=NEW_PROPOSAL" class="font-bold text-text">
                                        {{ number_format($metrics['new_proposal']) }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-soft-surface/50 transition-colors group">
                                <td class="px-5 py-4 font-semibold text-text-secondary">Belum Ditugaskan Reviewer</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.proposals.index') }}?status=PROCESS" class="font-bold text-text">
                                        {{ number_format($metrics['process']) }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-soft-surface/50 transition-colors group">
                                <td class="px-5 py-4 font-semibold text-text-secondary">Sedang Direview</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.proposals.index') }}?status=ON_REVIEW" class="font-bold text-text">
                                        {{ number_format($metrics['on_review']) }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-soft-surface/50 transition-colors group">
                                <td class="px-5 py-4 font-semibold text-text-secondary">Menunggu Keputusan</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.proposals.index') }}?status=REVISED" class="font-bold text-text">
                                        {{ number_format($metrics['revised']) }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-soft-surface/50 transition-colors group">
                                <td class="px-5 py-4 font-semibold text-text-secondary">Menunggu Tanda Tangan</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.proposals.index') }}?status=WAITING_SIGNATURE" class="font-bold text-text">
                                        {{ number_format($metrics['waiting_signature']) }}
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Integritas Dokumen (Info) --}}
            @if(isset($integrityMetrics) && ($integrityMetrics['missing_required'] > 0 || $integrityMetrics['broken_files'] > 0))
            <div class="bg-red-50 border border-red-200 rounded-lg p-5">
                <h3 class="text-xs font-bold text-danger uppercase tracking-wider mb-2">Peringatan Integritas</h3>
                <ul class="text-sm text-danger space-y-1">
                    @if($integrityMetrics['missing_required'] > 0)
                        <li>• {{ $integrityMetrics['missing_required'] }} Dokumen hilang/tidak lengkap</li>
                    @endif
                    @if($integrityMetrics['broken_files'] > 0)
                        <li>• {{ $integrityMetrics['broken_files'] }} File corrupt/tidak valid</li>
                    @endif
                </ul>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
