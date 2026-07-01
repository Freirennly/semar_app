<x-layouts.app :title="'Dashboard Admin'">
    {{-- ROW 1: H1 Title + Sub-heading --}}
    <div class="mb-12">
        <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3]">Dashboard Admin</h1>
        <p class="text-sm font-medium text-text-secondary mt-2 leading-[1.2]">Ringkasan aktivitas dan statistik Sistem Manajemen Pengajuan & Validasi Penelitian SEMAR.</p>
    </div>

    {{-- ROW 2: 4-Column Stat Card Aggregator --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-12">
        {{-- Card 1: Total Pengajuan --}}
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between transition-all duration-200 hover:shadow-md group">
            <div>
                <p class="text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] leading-[1.2]">Total Pengajuan</p>
                <p class="text-[32px] font-bold text-text mt-2 leading-none group-hover:text-primary transition-colors duration-200">{{ number_format($metrics['total_submissions']) }}</p>
            </div>
            <div class="p-3 bg-soft-surface text-primary rounded-lg shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        {{-- Card 2: Menunggu Validasi --}}
        @php
            $validationCount = ($statusDistribution['NEW_PROPOSAL'] ?? 0) + ($statusDistribution['REVISED'] ?? 0);
        @endphp
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between transition-all duration-200 hover:shadow-md group">
            <div>
                <p class="text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] leading-[1.2]">Menunggu Validasi</p>
                <p class="text-[32px] font-bold text-text mt-2 leading-none group-hover:text-warning transition-colors duration-200">{{ number_format($validationCount) }}</p>
            </div>
            <div class="p-3 bg-warning-bg text-warning rounded-lg shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Card 3: Sedang Direview --}}
        @php
            $reviewCount = $statusDistribution['ON_REVIEW'] ?? 0;
        @endphp
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between transition-all duration-200 hover:shadow-md group">
            <div>
                <p class="text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] leading-[1.2]">Sedang Direview</p>
                <p class="text-[32px] font-bold text-text mt-2 leading-none group-hover:text-primary transition-colors duration-200">{{ number_format($reviewCount) }}</p>
            </div>
            <div class="p-3 bg-soft-surface text-primary rounded-lg shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
            </div>
        </div>

        {{-- Card 4: Selesai --}}
        @php
            $doneCount = $statusDistribution['DONE'] ?? 0;
        @endphp
        <div class="bg-white border border-border p-6 rounded-xl flex items-center justify-between transition-all duration-200 hover:shadow-md group">
            <div>
                <p class="text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] leading-[1.2]">Selesai</p>
                <p class="text-[32px] font-bold text-text mt-2 leading-none group-hover:text-success transition-colors duration-200">{{ number_format($doneCount) }}</p>
            </div>
            <div class="p-3 bg-success-bg text-success rounded-lg shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Document Integrity & Template Health --}}
    @if(isset($templateMetrics) && isset($integrityMetrics))
    <div class="bg-white border border-border rounded-xl p-6 mb-12">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-semibold text-text leading-[1.4]">Integritas Dokumen</h2>
                <p class="text-[12px] font-normal text-text-secondary mt-1 leading-[1.2]">Status template dan integritas data dokumen pengajuan.</p>
            </div>
            <a href="{{ route('admin.templates.index') }}" class="text-[12px] font-bold text-primary hover:underline shrink-0">
                Kelola Template
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-[0.05em]">Total</p>
                <p class="text-[22px] font-bold text-text mt-1 leading-none">{{ $templateMetrics['total'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-[0.05em]">Aktif</p>
                <p class="text-[22px] font-bold text-success mt-1 leading-none">{{ $templateMetrics['active'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-[0.05em]">Tersembunyi</p>
                <p class="text-[22px] font-bold text-text-muted mt-1 leading-none">{{ $templateMetrics['hidden'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-[0.05em]">Arsip</p>
                <p class="text-[22px] font-bold text-danger mt-1 leading-none">{{ $templateMetrics['archived'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-[0.05em]">Dok. Hilang</p>
                <p class="text-[22px] font-bold mt-1 leading-none {{ $integrityMetrics['missing_required'] > 0 ? 'text-danger' : 'text-text' }}">{{ $integrityMetrics['missing_required'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-[0.05em]">File Rusak</p>
                <p class="text-[22px] font-bold mt-1 leading-none {{ $integrityMetrics['broken_files'] > 0 ? 'text-danger' : 'text-text' }}">{{ $integrityMetrics['broken_files'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-[0.05em]">Link Invalid</p>
                <p class="text-[22px] font-bold mt-1 leading-none {{ $integrityMetrics['invalid_links'] > 0 ? 'text-warning' : 'text-text' }}">{{ $integrityMetrics['invalid_links'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-[0.05em]">Orphan</p>
                <p class="text-[22px] font-bold mt-1 leading-none {{ $integrityMetrics['orphan_records'] > 0 ? 'text-danger' : 'text-text' }}">{{ $integrityMetrics['orphan_records'] }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Queue: Draft EC Required --}}
    @if(isset($draftEcRequired) && $draftEcRequired->isNotEmpty())
        <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden mb-12 animate-fade-in">
            <div class="px-6 py-4 border-b border-border bg-slate-50/70">
                <h2 class="text-lg font-medium text-primary uppercase tracking-[0.05em] leading-[1.4]">Draf Sertifikat Diperlukan</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-border bg-slate-50/30">
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Kode</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Judul</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Peneliti</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($draftEcRequired as $sub)
                            <tr class="hover:bg-soft-surface/25 transition-colors">
                                <td class="px-6 py-4 font-mono text-[12px] text-text-secondary">{{ $sub->code }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-academic text-text line-clamp-1" title="{{ $sub->title }}">{{ $sub->title }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-secondary truncate max-w-[140px]">{{ optional($sub->student)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.proposals.show', $sub) }}" class="inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-white text-[12px] font-bold px-3 py-1.5 rounded-xl transition-all">
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
        <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden mb-12 animate-fade-in">
            <div class="px-6 py-4 border-b border-border bg-slate-50/70">
                <h2 class="text-lg font-medium text-success uppercase tracking-[0.05em] leading-[1.4]">Sertifikat Terbit</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-border bg-slate-50/30">
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Kode</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Judul</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Nomor EC</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Peneliti</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($certificatesGenerated as $sub)
                            <tr class="hover:bg-soft-surface/25 transition-colors">
                                <td class="px-6 py-4 font-mono text-[12px] text-text-secondary">{{ $sub->code }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-academic text-text line-clamp-1" title="{{ $sub->title }}">{{ $sub->title }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-secondary">{{ $sub->ec_number }}</td>
                                <td class="px-6 py-4 text-sm text-text-secondary truncate max-w-[140px]">{{ optional($sub->student)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('submissions.certificate', $sub) }}" class="inline-flex items-center justify-center bg-success hover:bg-success/90 text-white text-[12px] font-bold px-3 py-1.5 rounded-xl transition-all">
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

    {{-- ROW 3: 2-Column Layout (70/30 Split via col-span-7 / col-span-3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">
        {{-- Main Stream Box (Left, 70% / col-span-7): Pengajuan Terbaru --}}
        <div class="lg:col-span-7 bg-white border border-border rounded-xl flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-white">
                <h2 class="text-2xl font-semibold text-text leading-[1.4]">Pengajuan Terbaru</h2>
                <a href="{{ route('admin.proposals.index') }}" class="text-[12px] font-bold text-primary hover:underline shrink-0">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-border bg-slate-50/70">
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Kode</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Judul</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Peneliti</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Status</th>
                            <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-white">
                        @forelse($latestSubmissions as $sub)
                        <tr class="hover:bg-soft-surface/25 transition-colors group">
                            <td class="px-6 py-4 font-mono text-[12px] text-text-secondary">{{ $sub->code }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.proposals.show', $sub) }}" class="font-academic text-text line-clamp-1 group-hover:text-primary transition-colors" title="{{ $sub->title }}">{{ $sub->title }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-text-secondary truncate max-w-[140px]">{{ optional($sub->student)->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$sub->status" />
                            </td>
                            <td class="px-6 py-4 text-[12px] text-text-secondary whitespace-nowrap">{{ $sub->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-text-muted italic text-sm">Tidak ada data pengajuan terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Widget Stream Box (Right, 30% / col-span-3): Stacked Widgets --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Widget 1: Distribusi Status --}}
            <div class="bg-white p-6 border border-border rounded-xl">
                <h2 class="text-2xl font-semibold text-text leading-[1.4] mb-4">Distribusi Status</h2>
                <div class="space-y-4">
                    @forelse($statusDistribution as $status => $count)
                    @php
                        $enum = \App\Enums\SubmissionStatus::tryFrom($status);
                        $statusLabel = $enum ? $enum->label() : str_replace('_', ' ', $status);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-[12px] mb-1.5">
                            <span class="font-semibold text-text-secondary leading-[1.2]">{{ $statusLabel }}</span>
                            <span class="font-extrabold text-primary leading-[1.2]">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-bg rounded-full h-2 overflow-hidden border border-border">
                            @php 
                                $percent = ($metrics['total_submissions'] > 0) ? ($count / $metrics['total_submissions'] * 100) : 0; 
                            @endphp
                            <div class="bg-primary h-full rounded-full transition-all duration-700 ease-out" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-[12px] text-text-muted text-center py-4 italic">Belum ada data status.</p>
                    @endforelse
                </div>
            </div>

            {{-- Widget 2: Ringkasan Pengguna --}}
            <div class="bg-white p-6 border border-border rounded-xl">
                <h2 class="text-2xl font-semibold text-text leading-[1.4] mb-4">Ringkasan Pengguna</h2>
                <div class="grid grid-cols-2 gap-3">
                    @forelse($userSummary as $role)
                    <div class="p-3 bg-soft-surface rounded-xl border border-border flex flex-col justify-between">
                        <p class="text-[10px] font-bold text-text-secondary uppercase tracking-[0.05em] mb-1 leading-[1.2]">{{ $role->name }}</p>
                        <p class="text-xl font-bold text-primary">{{ $role->count }}</p>
                    </div>
                    @empty
                    <p class="text-[12px] text-text-muted text-center py-4 italic col-span-2">Belum ada data pengguna.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
