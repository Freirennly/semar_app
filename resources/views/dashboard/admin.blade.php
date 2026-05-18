<x-layouts.app :title="'Admin Dashboard Overview'">
    {{-- 1. Metric Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total User --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4 group hover:border-primary transition-colors">
            <div class="w-12 h-12 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-text-muted uppercase tracking-wider">Total User</p>
                <p class="text-2xl font-bold text-text mt-0.5">{{ number_format($metrics['total_users']) }}</p>
            </div>
        </div>
        {{-- Total Pengajuan --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4 group hover:border-primary transition-colors">
            <div class="w-12 h-12 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-text-muted uppercase tracking-wider">Total Pengajuan</p>
                <p class="text-2xl font-bold text-text mt-0.5">{{ number_format($metrics['total_submissions']) }}</p>
            </div>
        </div>
        {{-- Pengajuan Aktif --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4 group hover:border-primary transition-colors">
            <div class="w-12 h-12 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-text-muted uppercase tracking-wider">Pengajuan Aktif</p>
                <p class="text-2xl font-bold text-text mt-0.5">{{ number_format($metrics['active_submissions']) }}</p>
            </div>
        </div>
        {{-- Disetujui --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4 group hover:border-primary transition-colors">
            <div class="w-12 h-12 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-text-muted uppercase tracking-wider">Disetujui</p>
                <p class="text-2xl font-bold text-text mt-0.5 text-success">{{ number_format($metrics['approved']) }}</p>
            </div>
        </div>
        {{-- Ditolak --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4 group hover:border-primary transition-colors">
            <div class="w-12 h-12 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-text-muted uppercase tracking-wider">Ditolak</p>
                <p class="text-2xl font-bold text-text mt-0.5 text-danger">{{ number_format($metrics['disapproved']) }}</p>
            </div>
        </div>
        {{-- Perlu Revisi --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4 group hover:border-primary transition-colors">
            <div class="w-12 h-12 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-text-muted uppercase tracking-wider">Perlu Revisi</p>
                <p class="text-2xl font-bold text-text mt-0.5 text-warning">{{ number_format($metrics['resubmission']) }}</p>
            </div>
        </div>
        {{-- Total Reviewer --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4 group hover:border-primary transition-colors">
            <div class="w-12 h-12 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-text-muted uppercase tracking-wider">Total Reviewer</p>
                <p class="text-2xl font-bold text-text mt-0.5">{{ number_format($metrics['total_reviewers']) }}</p>
            </div>
        </div>
        {{-- Sekretariat --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4 group hover:border-primary transition-colors">
            <div class="w-12 h-12 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-text-muted uppercase tracking-wider">Sekretariat</p>
                <p class="text-2xl font-bold text-text mt-0.5">{{ number_format($metrics['total_secretariat']) }}</p>
            </div>
        </div>
    </div>

    {{-- 2. Main Dashboard Split (Middle) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Left 2/3: Latest Submissions --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-border flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-white sticky top-0 z-10">
                <h3 class="text-base font-bold text-text">Pengajuan Terbaru</h3>
                <a href="#" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-text-muted text-[10px] uppercase tracking-widest border-b border-border bg-bg/30">
                            <th class="px-6 py-4 font-bold">Kode</th>
                            <th class="px-6 py-4 font-bold">Judul</th>
                            <th class="px-6 py-4 font-bold">Peneliti</th>
                            <th class="px-6 py-4 font-bold">Status</th>
                            <th class="px-6 py-4 font-bold">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($latestSubmissions as $sub)
                        <tr class="hover:bg-soft-surface/30 transition-colors group">
                            <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-text line-clamp-1 group-hover:text-primary transition-colors" title="{{ $sub->title }}">{{ $sub->title }}</p>
                            </td>
                            <td class="px-6 py-4 text-text-secondary truncate max-w-[140px]">{{ optional($sub->student)->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$sub->status" />
                            </td>
                            <td class="px-6 py-4 text-xs text-text-muted whitespace-nowrap">{{ $sub->created_at->format('d/m/Y') }}</td>
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

        {{-- Right 1/3: Panels --}}
        <div class="space-y-6">
            {{-- Status Distribution --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-border">
                <h3 class="text-base font-bold text-text mb-6">Distribusi Status</h3>
                <div class="space-y-5">
                    @foreach($statusDistribution as $status => $count)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-bold text-text-secondary capitalize">{{ str_replace('_', ' ', strtolower($status)) }}</span>
                            <span class="font-extrabold text-primary">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-soft-surface rounded-full h-2 overflow-hidden shadow-inner">
                            @php $percent = ($metrics['total_submissions'] > 0) ? ($count / $metrics['total_submissions'] * 100) : 0; @endphp
                            <div class="bg-primary h-full rounded-full transition-all duration-700 ease-out" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @endforeach
                    @if($statusDistribution->isEmpty())
                        <p class="text-sm text-text-muted text-center py-4 italic">Belum ada data status.</p>
                    @endif
                </div>
            </div>

            {{-- User Summary by Role --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-border">
                <h3 class="text-base font-bold text-text mb-6">Ringkasan Pengguna</h3>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($userSummary as $role)
                    <div class="p-3 bg-soft-surface rounded-xl border border-primary/10 hover:border-primary/30 transition-colors cursor-default group">
                        <p class="text-[10px] font-bold text-text-muted uppercase tracking-wider mb-0.5 group-hover:text-primary transition-colors">{{ $role->name }}</p>
                        <p class="text-xl font-extrabold text-primary">{{ $role->count }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Activity Timeline (Bottom) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border bg-white">
            <h3 class="text-base font-bold text-text">Aktivitas Sistem Terakhir</h3>
        </div>
        <div class="p-8">
            <div class="space-y-8 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-border">
                @forelse($latestActivities as $activity)
                <div class="relative pl-8 group">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="text-sm text-text-secondary leading-snug">
                            <span class="font-extrabold text-text">{{ optional($activity->user)->name ?? 'System' }}</span> 
                            <span class="text-text-muted">memperbarui status</span>
                            <span class="font-mono text-[10px] px-1.5 py-0.5 bg-bg border border-border rounded text-primary mx-1">[{{ optional($activity->submission)->code ?? '-' }}]</span>
                            <span class="text-text-muted">ke</span>
                            <span class="font-extrabold text-primary">{{ $activity->to_status ?? $activity->status }}</span>
                        </div>
                        <span class="text-[10px] font-bold text-text-muted whitespace-nowrap uppercase tracking-widest bg-soft-surface px-2 py-1 rounded">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-text-muted mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-text-muted italic">Belum ada aktivitas sistem yang tercatat.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
