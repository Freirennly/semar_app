<x-layouts.app :title="'Dashboard Reviewer'">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-start justify-between mb-6 gap-4">
        <div>
            <nav class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">
                Home <span class="mx-1">/</span> Dashboard
            </nav>
            <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Dashboard Reviewer</h1>
            <p class="text-sm text-text-secondary mt-1">Kelola penugasan review etik penelitian Anda secara profesional.</p>
        </div>
        <div class="flex flex-col md:items-end gap-3">
            <div class="text-sm font-semibold text-text-secondary hidden md:block">
                {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('fullboard.index') }}" class="text-xs font-semibold text-text hover:text-primary px-3 py-1.5 border border-border rounded transition-colors bg-white hover:border-primary">
                    Jadwal Fullboard
                </a>
            </div>
        </div>
    </div>

    {{-- Statistik Review Pribadi (Flat cards, no global stats) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        @foreach($metrics as $m)
            <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
                <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">{{ $m['label'] }}</p>
                <p class="text-3xl font-bold text-text leading-none">{{ $m['value'] }}</p>
            </div>
        @endforeach
    </div>

    @php
        $activeAssignments = $assignments->where('status', '!=', 'COMPLETED');
        $completedAssignments = $assignments->where('status', '==', 'COMPLETED');
    @endphp

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Active Tasks (70% or lg:col-span-2) --}}
        <div class="lg:col-span-2 bg-white border border-border rounded-lg flex flex-col overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h2 class="text-sm font-bold text-text uppercase tracking-wider">Tugas Review Aktif</h2>
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
                            <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                                <th class="px-5 py-3 font-semibold">Judul Usulan</th>
                                <th class="px-5 py-3 font-semibold">Pengusul</th>
                                <th class="px-5 py-3 font-semibold">Batas Waktu</th>
                                <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-white">
                            @foreach($activeAssignments as $a)
                                <tr class="hover:bg-soft-surface/50 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="font-semibold text-text">{{ \Illuminate\Support\Str::title($a->submission->title) }}</div>
                                        <div class="text-xs text-text-secondary font-mono mt-0.5">{{ $a->submission->code }}</div>
                                    </td>
                                    <td class="px-5 py-3 text-text-secondary">{{ optional($a->submission->student)->name ?? '-' }}</td>
                                    <td class="px-5 py-3 text-xs text-text-secondary whitespace-nowrap">
                                        {{ $a->due_at ? $a->due_at->timezone('Asia/Jakarta')->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('reviews.show', $a->submission) }}" class="text-xs font-bold text-primary hover:underline">
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
        <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
            <div class="px-5 py-4 border-b border-border">
                <h2 class="text-sm font-bold text-text uppercase tracking-wider">Riwayat Review</h2>
            </div>
            
            @if($completedAssignments->isEmpty())
                <div class="text-center py-12 px-6">
                    <p class="text-xs text-text-muted italic">Belum ada riwayat review yang selesai.</p>
                </div>
            @else
                <div class="divide-y divide-border overflow-y-auto max-h-[400px]">
                    @foreach($completedAssignments as $a)
                        <div class="p-4 hover:bg-soft-surface/20 transition-colors">
                            <p class="text-xs font-semibold text-text line-clamp-2">{{ \Illuminate\Support\Str::title($a->submission->title) }}</p>
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
        
        {{-- Jadwal Fullboard Widget --}}
        <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex justify-between items-center">
                <h2 class="text-sm font-bold text-text uppercase tracking-wider">Fullboard Mendatang</h2>
                <a href="{{ route('fullboard.index') }}" class="text-xs text-primary hover:underline font-bold">Lihat Semua</a>
            </div>
            
            @if($upcomingFullboard->isEmpty())
                <div class="text-center py-8 px-6">
                    <p class="text-xs text-text-muted italic">Tidak ada jadwal Fullboard.</p>
                </div>
            @else
                <div class="divide-y divide-border overflow-y-auto max-h-[400px]">
                    @foreach($upcomingFullboard as $fb)
                        <div class="p-4 hover:bg-soft-surface/20 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-semibold text-text line-clamp-1 flex-1">{{ \Illuminate\Support\Str::title($fb->submission->title) }}</p>
                                @if($fb->meeting_type === 'OFFLINE')
                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">OFFLINE</span>
                                @elseif($fb->meeting_type === 'ONLINE')
                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">ONLINE</span>
                                @else
                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">HYBRID</span>
                                @endif
                            </div>
                            <div class="flex flex-col gap-1 mt-2 text-[11px] text-text-secondary">
                                <div class="flex items-center justify-between">
                                    <span>{{ $fb->scheduled_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</span>
                                    
                                    <div class="flex items-center gap-2">
                                        @if($fb->meeting_type === 'OFFLINE' || $fb->meeting_type === 'HYBRID')
                                            @if($fb->location)
                                                <span class="text-text-secondary font-medium">{{ $fb->location }}</span>
                                            @endif
                                        @endif
                                        
                                        @if($fb->meeting_type === 'ONLINE' || $fb->meeting_type === 'HYBRID')
                                            @if($fb->meeting_url)
                                                <a href="{{ $fb->meeting_url }}" target="_blank" class="text-primary hover:underline font-bold flex items-center gap-1">
                                                    Buka Meeting
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
