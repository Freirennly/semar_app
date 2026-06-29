<x-layouts.app :title="'Admin Dashboard Overview'">
    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Dashboard Admin</h1>
        <p class="text-sm text-text-secondary mt-1.5">Sistem Manajemen Pengajuan & Validasi Penelitian SEMAR.</p>
    </div>

    {{-- Stat Cards Grid (6 Cards, flat, border-based) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-6">
        {{-- Card 1: Total Pengajuan --}}
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Total Pengajuan</p>
                <p class="text-[32px] font-bold text-text mt-2 leading-none">{{ number_format($metrics['total_submissions']) }}</p>
            </div>
            <div class="p-3 bg-soft-surface text-primary rounded-lg">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        {{-- Card 2: Menunggu Validasi --}}
        @php
            $validationCount = ($statusDistribution['NEW_PROPOSAL'] ?? 0) + ($statusDistribution['REVISED'] ?? 0);
        @endphp
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Menunggu Validasi</p>
                <p class="text-[32px] font-bold text-text mt-2 leading-none">{{ number_format($validationCount) }}</p>
            </div>
            <div class="p-3 bg-soft-surface text-warning rounded-lg">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Card 3: Sedang Direview --}}
        @php
            $reviewCount = $statusDistribution['ON_REVIEW'] ?? 0;
        @endphp
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Sedang Direview</p>
                <p class="text-[32px] font-bold text-text mt-2 leading-none">{{ number_format($reviewCount) }}</p>
            </div>
            <div class="p-3 bg-soft-surface text-primary rounded-lg">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493"/>
                </svg>
            </div>
        </div>

        {{-- Card 4: Selesai --}}
        @php
            $doneCount = $statusDistribution['DONE'] ?? 0;
        @endphp
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Selesai</p>
                <p class="text-[32px] font-bold text-text mt-2 leading-none">{{ number_format($doneCount) }}</p>
            </div>
            <div class="p-3 bg-soft-surface text-success rounded-lg">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Card 5: Sertifikat Hilang --}}
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Sertifikat Hilang</p>
                <p class="text-[32px] font-bold text-danger mt-2 leading-none">{{ number_format($missingCertificatesCount) }}</p>
            </div>
            <div class="p-3 bg-red-50 text-danger rounded-lg">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        {{-- Card 6: Backup Terakhir --}}
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Backup Terakhir</p>
                <p class="text-[12px] font-bold text-text mt-3 leading-none truncate max-w-[120px]" title="{{ $lastBackupTime }}">{{ $lastBackupTime }}</p>
            </div>
            <div class="p-3 bg-soft-surface text-primary rounded-lg">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Document Integrity & Template Health --}}
    @if(isset($templateMetrics) && isset($integrityMetrics))
    <div class="bg-white border border-border rounded-xl p-6 mb-6">
        <div class="border-b border-border pb-3 mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-text uppercase tracking-wider">Integritas & Kesehatan Dokumen</h2>
                <p class="text-xs text-text-secondary mt-0.5">Status template dan integritas data dokumen pengajuan.</p>
            </div>
            <a href="{{ route('admin.templates.index') }}" class="text-xs font-bold text-primary hover:underline">
                Kelola Template →
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-wider">Total</p>
                <p class="text-[22px] font-bold text-text mt-1 leading-none">{{ $templateMetrics['total'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-wider">Aktif</p>
                <p class="text-[22px] font-bold mt-1 leading-none" style="color:#15803d">{{ $templateMetrics['active'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-wider">Sembunyikan</p>
                <p class="text-[22px] font-bold mt-1 leading-none" style="color:#8E8CAD">{{ $templateMetrics['hidden'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-wider">Arsip</p>
                <p class="text-[22px] font-bold mt-1 leading-none" style="color:#b91c1c">{{ $templateMetrics['archived'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-wider">Dok. Hilang</p>
                <p class="text-[22px] font-bold mt-1 leading-none {{ $integrityMetrics['missing_required'] > 0 ? 'text-danger' : 'text-text' }}">{{ $integrityMetrics['missing_required'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-wider">File Rusak</p>
                <p class="text-[22px] font-bold mt-1 leading-none {{ $integrityMetrics['broken_files'] > 0 ? 'text-danger' : 'text-text' }}">{{ $integrityMetrics['broken_files'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-wider">Link Invalid</p>
                <p class="text-[22px] font-bold mt-1 leading-none {{ $integrityMetrics['invalid_links'] > 0 ? 'text-warning' : 'text-text' }}">{{ $integrityMetrics['invalid_links'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-wider">Orphan</p>
                <p class="text-[22px] font-bold mt-1 leading-none {{ $integrityMetrics['orphan_records'] > 0 ? 'text-danger' : 'text-text' }}">{{ $integrityMetrics['orphan_records'] }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Queue: Draft EC Required --}}
    @if(isset($draftEcRequired) && $draftEcRequired->isNotEmpty())
        <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden mb-6 animate-fade-in">
            <div class="px-6 py-4 border-b border-border bg-slate-50/70">
                <h2 class="text-sm font-bold text-text uppercase tracking-wider text-primary">Draf Sertifikat Diperlukan (Draft EC Required)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/30">
                            <th class="px-6 py-4 font-semibold">Kode</th>
                            <th class="px-6 py-4 font-semibold">Judul</th>
                            <th class="px-6 py-4 font-semibold">Peneliti</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($draftEcRequired as $sub)
                            <tr class="hover:bg-soft-surface/25 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-text line-clamp-1" title="{{ $sub->title }}">
                                        {{ \Illuminate\Support\Str::title($sub->title) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-text-secondary truncate max-w-[140px]">{{ optional($sub->student)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.proposals.show', $sub) }}" class="inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-white text-xs font-bold px-3 py-1.5 rounded-xl transition-all">
                                        Buat Draft EC
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Queue: Certificates Generated --}}
    @if(isset($certificatesGenerated) && $certificatesGenerated->isNotEmpty())
        <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden mb-6 animate-fade-in">
            <div class="px-6 py-4 border-b border-border bg-slate-50/70">
                <h2 class="text-sm font-bold text-text uppercase tracking-wider text-success">Sertifikat Terbit (Certificates Generated)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/30">
                            <th class="px-6 py-4 font-semibold">Kode</th>
                            <th class="px-6 py-4 font-semibold">Judul</th>
                            <th class="px-6 py-4 font-semibold">Nomor EC</th>
                            <th class="px-6 py-4 font-semibold">Peneliti</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($certificatesGenerated as $sub)
                            <tr class="hover:bg-soft-surface/25 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-text line-clamp-1" title="{{ $sub->title }}">
                                        {{ \Illuminate\Support\Str::title($sub->title) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-text-secondary">{{ $sub->ec_number }}</td>
                                <td class="px-6 py-4 text-text-secondary truncate max-w-[140px]">{{ optional($sub->student)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('submissions.certificate', $sub) }}" class="inline-flex items-center justify-center bg-success hover:bg-success/90 text-white text-xs font-bold px-3 py-1.5 rounded-xl transition-all">
                                        Unduh
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Main Grid (70/30 Split) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Pengajuan Terbaru (70% or lg:col-span-2) --}}
        <div class="lg:col-span-2 bg-white border border-border rounded-xl flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-white">
                <h2 class="text-[24px] font-semibold text-text">Pengajuan Terbaru</h2>
                <a href="{{ route('admin.proposals.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/70">
                            <th class="px-6 py-4 font-semibold">Kode</th>
                            <th class="px-6 py-4 font-semibold">Judul</th>
                            <th class="px-6 py-4 font-semibold">Peneliti</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-white">
                        @forelse($latestSubmissions as $sub)
                        <tr class="hover:bg-soft-surface/25 transition-colors group">
                            <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.proposals.show', $sub) }}" class="font-semibold text-text line-clamp-1 group-hover:text-primary transition-colors" title="{{ $sub->title }}">
                                    {{ \Illuminate\Support\Str::title($sub->title) }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-text-secondary truncate max-w-[140px]">{{ optional($sub->student)->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$sub->status" />
                            </td>
                            <td class="px-6 py-4 text-xs text-text-secondary whitespace-nowrap">{{ $sub->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-text-muted italic">Tidak ada data pengajuan terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right: Status & Users (30% or lg:col-span-1) --}}
        <div class="space-y-6">
            {{-- Distribusi Status --}}
            <div class="bg-white p-6 border border-border rounded-xl">
                <h2 class="text-[24px] font-semibold text-text mb-4">Distribusi Status</h2>
                <div class="space-y-4">
                    @forelse($statusDistribution as $status => $count)
                    @php
                        $enum = \App\Enums\SubmissionStatus::tryFrom($status);
                        $statusLabel = $enum ? $enum->label() : str_replace('_', ' ', $status);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-text-secondary">{{ $statusLabel }}</span>
                            <span class="font-extrabold text-primary">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-bg rounded-full h-2 overflow-hidden border border-border">
                            @php 
                                $percent = ($metrics['total_submissions'] > 0) ? ($count / $metrics['total_submissions'] * 100) : 0; 
                            @endphp
                            <div class="bg-primary h-full rounded-full transition-all duration-700 ease-out" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-text-muted text-center py-4 italic">Belum ada data status.</p>
                    @endforelse
                </div>
            </div>

            {{-- Ringkasan Pengguna --}}
            <div class="bg-white p-6 border border-border rounded-xl">
                <h2 class="text-[24px] font-semibold text-text mb-4">Ringkasan Pengguna</h2>
                <div class="grid grid-cols-2 gap-3">
                    @forelse($userSummary as $role)
                    <div class="p-3 bg-soft-surface rounded-xl border border-border flex flex-col justify-between">
                        <p class="text-[10px] font-bold text-text-secondary uppercase tracking-wider mb-1">{{ $role->name }}</p>
                        <p class="text-xl font-bold text-primary">{{ $role->count }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-text-muted text-center py-4 italic col-span-2">Belum ada data pengguna.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
