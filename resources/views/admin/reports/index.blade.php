<x-layouts.app :title="'Laporan & Statistik'">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-text">Laporan & Statistik</h2>
            <p class="text-sm text-text-secondary mt-1">Analisis kinerja sistem dan status pengajuan proposal.</p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-5 border-l-4 border-primary shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-soft-surface text-primary flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-text-muted font-medium mb-1">Total Pengajuan</p>
                    <h3 class="text-2xl font-bold text-text">{{ number_format($stats['total_proposals']) }}</h3>
                </div>
            </div>
        </div>

        <div class="card p-5 border-l-4 border-success shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-success-bg text-success flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-text-muted font-medium mb-1">Disetujui (Approved)</p>
                    <h3 class="text-2xl font-bold text-text">{{ number_format($stats['status_distribution']['APPROVED'] ?? 0) }}</h3>
                </div>
            </div>
        </div>

        <div class="card p-5 border-l-4 border-danger shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-danger-bg text-danger flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-text-muted font-medium mb-1">Ditolak (Disapproved)</p>
                    <h3 class="text-2xl font-bold text-text">{{ number_format($stats['status_distribution']['DISAPPROVED'] ?? 0) }}</h3>
                </div>
            </div>
        </div>

        <div class="card p-5 border-l-4 border-warning shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-warning-bg text-warning flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-text-muted font-medium mb-1">Dalam Proses</p>
                    @php
                        $inProcess = ($stats['status_distribution']['SUBMITTED'] ?? 0) + 
                                     ($stats['status_distribution']['DOC_CHECK'] ?? 0) + 
                                     ($stats['status_distribution']['ASSIGNED'] ?? 0) + 
                                     ($stats['status_distribution']['UNDER_REVIEW'] ?? 0) + 
                                     ($stats['status_distribution']['PENDING_DECISION'] ?? 0);
                    @endphp
                    <h3 class="text-2xl font-bold text-text">{{ number_format($inProcess) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Distribution Chart (Simplified CSS-based) -->
        <div class="card p-6 shadow-sm col-span-1">
            <h3 class="font-bold text-text mb-6">Distribusi Status Proposal</h3>
            
            <div class="space-y-4">
                @foreach([
                    'Draft' => 'DRAFT', 
                    'Doc Check' => 'DOC_CHECK',
                    'Under Review' => 'UNDER_REVIEW', 
                    'Approved' => 'APPROVED', 
                    'Disapproved' => 'DISAPPROVED'
                ] as $label => $key)
                    @php 
                        $count = $stats['status_distribution'][$key] ?? 0;
                        $total = max($stats['total_proposals'], 1);
                        $percentage = min(round(($count / $total) * 100), 100);
                        
                        $colorClass = match($key) {
                            'APPROVED' => 'bg-success',
                            'DISAPPROVED' => 'bg-danger',
                            'DRAFT' => 'bg-border-strong',
                            default => 'bg-primary'
                        };
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="text-text-secondary">{{ $label }}</span>
                            <span class="text-text">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-border rounded-full h-2">
                            <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 border-t border-border pt-4">
                <h3 class="font-bold text-text mb-4 text-sm">Statistik Pengguna</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-bg rounded-lg p-3 text-center border border-border">
                        <p class="text-[10px] uppercase text-text-muted font-bold tracking-wider mb-1">Reviewer Aktif</p>
                        <p class="text-xl font-bold text-primary">{{ number_format($stats['total_reviewers']) }}</p>
                    </div>
                    <div class="bg-bg rounded-lg p-3 text-center border border-border">
                        <p class="text-[10px] uppercase text-text-muted font-bold tracking-wider mb-1">Staf Sekretariat</p>
                        <p class="text-xl font-bold text-primary">{{ number_format($stats['total_secretariat']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Submissions Table -->
        <div class="card shadow-sm col-span-1 lg:col-span-2 flex flex-col">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <h3 class="font-bold text-text">Pengajuan Terbaru</h3>
                <a href="{{ route('admin.proposals.index') }}" class="text-xs text-primary font-semibold hover:underline">Lihat Semua &rarr;</a>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-text-muted text-[10px] uppercase tracking-widest bg-bg/30">
                            <th class="px-6 py-3 font-bold">Judul & Pengusul</th>
                            <th class="px-6 py-3 font-bold">Status</th>
                            <th class="px-6 py-3 font-bold text-right">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($latestSubmissions as $submission)
                        <tr class="hover:bg-soft-surface/30 transition-colors">
                            <td class="px-6 py-3">
                                <p class="font-semibold text-text line-clamp-1" title="{{ $submission->title }}">{{ $submission->title }}</p>
                                <p class="text-xs text-text-muted mt-0.5">{{ optional($submission->student)->name ?? 'Unknown' }}</p>
                            </td>
                            <td class="px-6 py-3">
                                <x-status-badge :status="$submission->status" />
                            </td>
                            <td class="px-6 py-3 text-right text-xs text-text-secondary whitespace-nowrap">
                                {{ $submission->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-text-muted italic">
                                Belum ada pengajuan masuk.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
