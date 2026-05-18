<x-layouts.app :title="'Dashboard Sekretariat'">
<div class="mb-8 flex items-start justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1.5">
            <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
            <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Sekretariat KEP</p>
        </div>
        <h1 class="text-2xl font-bold tracking-tight" style="color:#0F0E2E">Dashboard Sekretariat</h1>
        <p class="text-sm font-light mt-1" style="color:#5A587A">Kelola validasi dokumen dan keputusan akhir pengajuan.</p>
    </div>
    <div class="text-right hidden sm:block">
        <p class="text-xs font-medium" style="color:#8E8CAD">{{ now()->translatedFormat('l, d F Y') }}</p>
        <p class="text-xs mt-0.5" style="color:#b0aec8">SEMAR Academic Platform</p>
    </div>
</div>

<!--METRIC CARDS-->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach($metrics as $m)
        @php
        $colorMap = [
            'primary' => [
                'iconBg'  => '#E6E6FA',
                'iconStr' => '#463EE3',
                'dot'     => '#463EE3',
                'label'   => '#463EE3',
                'border'  => 'rgba(70,62,227,0.18)',
                'shadow'  => 'rgba(70,62,227,0.10)',
            ],
            'sky' => [
                'iconBg'  => 'rgba(135,206,235,0.22)',
                'iconStr' => '#1a7fa3',
                'dot'     => '#87CEEB',
                'label'   => '#1a7fa3',
                'border'  => 'rgba(135,206,235,0.4)',
                'shadow'  => 'rgba(135,206,235,0.15)',
            ],
            'success' => [
                'iconBg'  => 'rgba(34,197,94,0.12)',
                'iconStr' => '#15803d',
                'dot'     => '#22C55E',
                'label'   => '#15803d',
                'border'  => 'rgba(34,197,94,0.25)',
                'shadow'  => 'rgba(34,197,94,0.10)',
            ],
            'warning' => [
                'iconBg'  => 'rgba(245,158,11,0.12)',
                'iconStr' => '#b45309',
                'dot'     => '#F59E0B',
                'label'   => '#b45309',
                'border'  => 'rgba(245,158,11,0.28)',
                'shadow'  => 'rgba(245,158,11,0.10)',
            ],
            'danger' => [
                'iconBg'  => 'rgba(239,68,68,0.10)',
                'iconStr' => '#b91c1c',
                'dot'     => '#EF4444',
                'label'   => '#b91c1c',
                'border'  => 'rgba(239,68,68,0.22)',
                'shadow'  => 'rgba(239,68,68,0.10)',
            ],
        ];
        $c = $colorMap[$m['color']] ?? $colorMap['primary'];
        @endphp
        <div class="bg-white rounded-2xl border p-5 relative overflow-hidden cursor-default transition-all duration-200"
             style="border-color:{{ $c['border'] }};
                    box-shadow: 0 2px 8px {{ $c['shadow'] }};"
             onmouseover="this.style.boxShadow='0 8px 28px {{ $c['shadow'] }}'; this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='0 2px 8px {{ $c['shadow'] }}'; this.style.transform='translateY(0)'">

            {{-- Left accent bar --}}
            <div class="absolute left-0 top-3 bottom-3 w-[3px] rounded-full"
                 style="background:{{ $c['dot'] }};"></div>

            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background:{{ $c['iconBg'] }};">
                    @if($m['color'] === 'primary')
                    <svg class="w-5 h-5" fill="none" stroke="{{ $c['iconStr'] }}" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    @elseif($m['color'] === 'warning')
                    <svg class="w-5 h-5" fill="none" stroke="{{ $c['iconStr'] }}" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @elseif($m['color'] === 'success')
                    <svg class="w-5 h-5" fill="none" stroke="{{ $c['iconStr'] }}" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @elseif($m['color'] === 'danger')
                    <svg class="w-5 h-5" fill="none" stroke="{{ $c['iconStr'] }}" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    @else
                    <svg class="w-5 h-5" fill="none" stroke="{{ $c['iconStr'] }}" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    @endif
                </div>
            </div>

            <p class="text-3xl font-bold tracking-tight mb-1" style="color:#0F0E2E">{{ $m['value'] }}</p>
            <p class="text-xs font-bold" style="color:{{ $c['label'] }}">{{ $m['label'] }}</p>
        </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════
     MAIN PANELS
════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- ── Perlu Cek Dokumen ──────────────────────── --}}
    <div class="bg-white rounded-2xl border overflow-hidden"
         style="border-color:rgba(70,62,227,0.14);
                box-shadow: 0 2px 16px rgba(70,62,227,0.07);">

        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom:1.5px solid rgba(70,62,227,0.08);
                    background:rgba(70,62,227,0.025);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                     style="background:#E6E6FA; box-shadow:0 1px 4px rgba(70,62,227,0.15);">
                    <svg class="w-4 h-4" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold" style="color:#0F0E2E">Perlu Cek Dokumen</h3>
                @if($submitted->count())
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                      style="background:#463EE3; color:white;">{{ $submitted->count() }}</span>
                @endif
            </div>
            <a href="{{ route('doccheck.index') }}"
               class="text-xs font-semibold flex items-center gap-1 px-3 py-1.5 rounded-lg border transition-all duration-150"
               style="color:#463EE3; border-color:rgba(70,62,227,0.22); background:white;"
               onmouseover="this.style.background='#E6E6FA'; this.style.borderColor='rgba(70,62,227,0.4)'"
               onmouseout="this.style.background='white'; this.style.borderColor='rgba(70,62,227,0.22)'">
                Semua <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @forelse($submitted as $sub)
        <div class="flex items-center gap-3.5 px-5 py-3.5 border-b last:border-b-0 transition-colors duration-150"
             style="border-color:rgba(70,62,227,0.05);"
             onmouseover="this.style.background='rgba(70,62,227,0.025)'"
             onmouseout="this.style.background='transparent'">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0"
                 style="background:#F5F5F5; color:#5A587A; border:1px solid rgba(0,0,0,0.07);">
                {{ strtoupper(substr($sub->student->name ?? 'U', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold truncate" style="color:#0F0E2E">{{ $sub->title }}</p>
                <p class="text-xs font-light mt-0.5" style="color:#8E8CAD">
                    {{ $sub->student->name ?? '-' }}
                    <span style="color:#d0cfe8; margin:0 3px">·</span>
                    {{ $sub->created_at->format('d M Y') }}
                </p>
            </div>
            <a href="{{ route('doccheck.show', $sub) }}"
               class="text-xs font-bold px-3.5 py-1.5 rounded-lg flex-shrink-0 border transition-all duration-150"
               style="color:#463EE3; border-color:rgba(70,62,227,0.22); background:white; box-shadow:0 1px 4px rgba(70,62,227,0.08);"
               onmouseover="this.style.background='#463EE3'; this.style.color='white'; this.style.borderColor='#463EE3'; this.style.boxShadow='0 3px 10px rgba(70,62,227,0.25)'"
               onmouseout="this.style.background='white'; this.style.color='#463EE3'; this.style.borderColor='rgba(70,62,227,0.22)'; this.style.boxShadow='0 1px 4px rgba(70,62,227,0.08)'">
                Cek →
            </a>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
                 style="background:#F5F5F5; border:1.5px dashed rgba(70,62,227,0.18);">
                <svg class="w-6 h-6" fill="none" stroke="#c4c2e0" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold" style="color:#8E8CAD">Tidak ada pengajuan baru</p>
            <p class="text-xs font-light mt-1" style="color:#b0aec8">Semua dokumen sudah dicek</p>
        </div>
        @endforelse
    </div>

    {{-- ── Menunggu Keputusan ──────────────────────── --}}
    <div class="bg-white rounded-2xl border overflow-hidden"
         style="border-color:rgba(245,158,11,0.22);
                box-shadow: 0 2px 16px rgba(245,158,11,0.07);">

        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom:1.5px solid rgba(245,158,11,0.1);
                    background:rgba(245,158,11,0.03);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                     style="background:rgba(245,158,11,0.14); box-shadow:0 1px 4px rgba(245,158,11,0.2);">
                    <svg class="w-4 h-4" fill="none" stroke="#b45309" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold" style="color:#0F0E2E">Menunggu Keputusan</h3>
                @if($pendingDecision->count())
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                      style="background:#F59E0B; color:white;">{{ $pendingDecision->count() }}</span>
                @endif
            </div>
            <a href="{{ route('decisions.index') }}"
               class="text-xs font-semibold flex items-center gap-1 px-3 py-1.5 rounded-lg border transition-all duration-150"
               style="color:#b45309; border-color:rgba(245,158,11,0.3); background:white;"
               onmouseover="this.style.background='rgba(245,158,11,0.1)'; this.style.borderColor='rgba(245,158,11,0.5)'"
               onmouseout="this.style.background='white'; this.style.borderColor='rgba(245,158,11,0.3)'">
                Semua <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @forelse($pendingDecision as $sub)
        <div class="flex items-center gap-3.5 px-5 py-3.5 border-b last:border-b-0 transition-colors duration-150"
             style="border-color:rgba(245,158,11,0.07);"
             onmouseover="this.style.background='rgba(245,158,11,0.025)'"
             onmouseout="this.style.background='transparent'">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0"
                 style="background:rgba(245,158,11,0.1); color:#b45309; border:1px solid rgba(245,158,11,0.22);">
                {{ $sub->reviews->count() }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold truncate" style="color:#0F0E2E">{{ $sub->title }}</p>
                <p class="text-xs font-light mt-0.5" style="color:#8E8CAD">{{ $sub->reviews->count() }} review masuk</p>
            </div>
            <a href="{{ route('decisions.show', $sub) }}"
               class="text-xs font-bold px-3.5 py-1.5 rounded-lg flex-shrink-0 transition-all duration-150"
               style="color:white; background:#463EE3; box-shadow:0 2px 8px rgba(70,62,227,0.28);"
               onmouseover="this.style.background='#332DB8'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 5px 16px rgba(70,62,227,0.35)'"
               onmouseout="this.style.background='#463EE3'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(70,62,227,0.28)'">
                Putuskan →
            </a>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
                 style="background:#F5F5F5; border:1.5px dashed rgba(245,158,11,0.28);">
                <svg class="w-6 h-6" fill="none" stroke="#f5d08b" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold" style="color:#8E8CAD">Tidak ada yang menunggu keputusan</p>
            <p class="text-xs font-light mt-1" style="color:#b0aec8">Semua pengajuan sudah diproses</p>
        </div>
        @endforelse
    </div>

</div>

</x-layouts.app>