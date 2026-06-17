<x-layouts.app :title="'Admin Dashboard Overview'">
    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Dashboard Admin</h1>
        <p class="text-sm text-text-secondary mt-1.5">Sistem Manajemen Pengajuan & Validasi Penelitian SEMAR.</p>
    </div>

    {{-- Stat Cards Grid (4 Cards Only, flat, border-based) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
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
    </div>

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
