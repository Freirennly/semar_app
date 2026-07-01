<x-layouts.app :title="'Dashboard Reviewer'">
    {{-- Header Section --}}
    <div class="mb-12">
        <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3]">Dashboard Reviewer</h1>
        <p class="text-sm font-medium text-text-secondary mt-2 leading-[1.2]">Kelola penugasan review etik penelitian Anda secara profesional.</p>
    </div>

    {{-- Statistik Review Pribadi (Flat cards, no global stats) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
        @foreach($metrics as $m)
            <div class="bg-white border border-border p-5 rounded-xl">
                <p class="text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">{{ $m['label'] }}</p>
                <p class="text-[28px] font-bold text-text mt-2 leading-none">{{ $m['value'] }}</p>
            </div>
        @endforeach
    </div>

    @php
        $activeAssignments = $assignments->where('status', '!=', 'COMPLETED');
        $completedAssignments = $assignments->where('status', '==', 'COMPLETED');
    @endphp

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">
        <div class="lg:col-span-7 bg-white border border-border rounded-xl flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-border bg-white">
                <h2 class="text-[24px] font-semibold text-text leading-[1.4]">Tugas Review Aktif</h2>
            </div>
            
            @if($activeAssignments->isEmpty())
                <div class="text-center py-16 px-6">
                    <div class="w-14 h-14 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-text">Semua tugas review selesai!</h3>
                    <p class="text-xs text-text-secondary mt-1">Anda tidak memiliki tugas review aktif yang tertunda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-border bg-slate-50/70">
                                <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Judul Usulan</th>
                                <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Pengusul</th>
                                <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Batas Waktu</th>
                                <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($activeAssignments as $a)
                                <tr class="hover:bg-soft-surface/25 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-serif text-text">{{ $a->submission->title }}</div>
                                        <div class="text-xs text-text-secondary font-mono mt-0.5">{{ $a->submission->code }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-text-secondary">{{ optional($a->submission->student)->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-xs text-text-secondary whitespace-nowrap">
                                        {{ $a->due_at ? $a->due_at->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('reviews.show', $a->submission) }}" class="px-3.5 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-lg transition-colors">
                                            Isi Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Right: Completed Review History (30% or lg:col-span-1) --}}
        <div class="lg:col-span-3 bg-white border border-border rounded-xl flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-border bg-white">
                <h2 class="text-[24px] font-semibold text-text leading-[1.4]">Riwayat Review</h2>
            </div>
            
            @if($completedAssignments->isEmpty())
                <div class="text-center py-12 px-6">
                    <p class="text-xs text-text-muted italic">Belum ada riwayat review yang selesai.</p>
                </div>
            @else
                <div class="divide-y divide-border overflow-y-auto max-h-[400px]">
                    @foreach($completedAssignments as $a)
                        <div class="p-4 hover:bg-soft-surface/20 transition-colors">
                            <p class="text-xs font-serif text-text line-clamp-2">{{ $a->submission->title }}</p>
                            <div class="flex items-center justify-between mt-2.5 text-[10px] text-text-secondary">
                                <span class="font-mono">{{ $a->submission->code }}</span>
                                <span class="font-bold text-success">Selesai Dinilai</span>
                            </div>
                            <div class="mt-2 text-right">
                                <a href="{{ route('reviews.show', $a->submission) }}" class="text-xs font-bold text-primary hover:underline">Lihat Detail →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
