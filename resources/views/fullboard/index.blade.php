<x-layouts.app title="Jadwal Fullboard Meeting">

<div class="mb-8">
    <div class="flex items-center gap-2 mb-1.5">
        <div class="w-4 h-[2px] bg-primary"></div>
        <p class="text-[11px] font-bold tracking-widest uppercase text-primary">Agenda Sidang</p>
    </div>
    <h1 class="text-2xl font-bold tracking-tight text-text leading-tight mb-2">
        Fullboard Schedule
    </h1>
    <p class="text-sm font-medium text-text-secondary">Daftar jadwal sidang Fullboard untuk pengajuan etik.</p>
</div>

<div class="bg-white rounded-2xl border border-border overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-soft-surface border-b border-border text-[11px] uppercase tracking-wider text-text-muted">
                    <th class="px-5 py-3.5 font-bold">Proposal</th>
                    <th class="px-5 py-3.5 font-bold whitespace-nowrap">Tanggal</th>
                    <th class="px-5 py-3.5 font-bold whitespace-nowrap">Jam</th>
                    <th class="px-5 py-3.5 font-bold">Lokasi / Link</th>
                    <th class="px-5 py-3.5 font-bold">Agenda</th>
                    <th class="px-5 py-3.5 font-bold">Jenis Rapat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/60">
                @forelse($meetings as $meeting)
                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                    <td class="px-5 py-4">
                        <div class="text-sm font-semibold text-text mb-1">
                            {{ $meeting->submission->title }}
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[10px] px-2 py-0.5 rounded-md bg-surface text-text-muted border border-border/50">
                                {{ $meeting->submission->code }}
                            </span>
                        </div>
                    </td>
                    <td class="px-5 py-4 whitespace-nowrap text-sm text-text-secondary font-medium">
                        {{ $meeting->scheduled_at->timezone('Asia/Jakarta')->format('d M Y') }}
                    </td>
                    <td class="px-5 py-4 whitespace-nowrap text-sm text-text-secondary">
                        {{ $meeting->scheduled_at->timezone('Asia/Jakarta')->format('H:i') }}
                        @if($meeting->end_at)
                            - {{ $meeting->end_at->timezone('Asia/Jakarta')->format('H:i') }}
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-text-secondary">
                        @if($meeting->meeting_type === 'OFFLINE' || $meeting->meeting_type === 'HYBRID')
                            @if($meeting->location)
                                <div class="font-medium text-text mb-0.5">{{ $meeting->location }}</div>
                            @endif
                        @endif
                        @if($meeting->meeting_type === 'ONLINE' || $meeting->meeting_type === 'HYBRID')
                            @if($meeting->meeting_url)
                                <a href="{{ $meeting->meeting_url }}" target="_blank" class="text-primary hover:underline text-xs flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Buka Meeting
                                </a>
                            @endif
                        @endif
                        @if(!$meeting->location && !$meeting->meeting_url)
                            <span class="italic text-text-muted text-xs">Belum ditentukan</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-text-secondary">
                        {{ Str::limit($meeting->agenda, 50) }}
                    </td>
                    <td class="px-5 py-4 whitespace-nowrap">
                        @if($meeting->meeting_type === 'OFFLINE')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                OFFLINE
                            </span>
                        @elseif($meeting->meeting_type === 'ONLINE')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                ONLINE
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                HYBRID
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-soft-surface text-text-muted mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-text mb-1">Belum ada Jadwal</h3>
                        <p class="text-xs text-text-muted">Tidak ada jadwal sidang Fullboard yang dijadwalkan saat ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($meetings->hasPages())
    <div class="px-5 py-4 border-t border-border">
        {{ $meetings->links() }}
    </div>
    @endif
</div>

</x-layouts.app>
