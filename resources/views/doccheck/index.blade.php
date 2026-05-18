<x-layouts.app :title="'Cek Dokumen'">

{{-- ═══════════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════════════ --}}
<div class="mb-8 flex items-start justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1.5">
            <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
            <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Sekretariat KEP</p>
        </div>
        <h1 class="text-2xl font-bold tracking-tight" style="color:#0F0E2E">Cek Dokumen</h1>
        <p class="text-sm font-light mt-1" style="color:#5A587A">Validasi kelengkapan dokumen pengajuan yang masuk.</p>
    </div>
    @if(!$submissions->isEmpty())
    <div class="hidden sm:flex items-center gap-2 px-3.5 py-2 rounded-xl border"
         style="background:#E6E6FA; border-color:rgba(70,62,227,0.2);">
        <span class="text-xs font-bold" style="color:#463EE3">{{ $submissions->count() }} pengajuan</span>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════
     CONTENT
════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border overflow-hidden"
     style="border-color:rgba(70,62,227,0.14);
            box-shadow: 0 2px 16px rgba(70,62,227,0.07);">

    @if($submissions->isEmpty())

        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4"
                 style="background:#F5F5F5; border:1.5px dashed rgba(70,62,227,0.18);">
                <svg class="w-7 h-7" fill="none" stroke="#c4c2e0" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold mb-1" style="color:#5A587A">Tidak ada pengajuan baru</p>
            <p class="text-xs font-light" style="color:#b0aec8">Semua dokumen sudah diperiksa</p>
        </div>

    @else

        {{-- Table header --}}
        <div class="px-5 py-4 border-b flex items-center gap-2.5"
             style="border-color:rgba(70,62,227,0.08); background:rgba(70,62,227,0.025);">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                 style="background:#E6E6FA; box-shadow:0 1px 4px rgba(70,62,227,0.15);">
                <svg class="w-4 h-4" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold" style="color:#0F0E2E">Daftar Pengajuan Masuk</h3>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ml-1"
                  style="background:#463EE3; color:white;">{{ $submissions->count() }}</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid rgba(70,62,227,0.07);">
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase"
                            style="color:#8E8CAD">Kode</th>
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase"
                            style="color:#8E8CAD">Judul Penelitian</th>
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase hidden sm:table-cell"
                            style="color:#8E8CAD">Pengaju</th>
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase"
                            style="color:#8E8CAD">Dokumen</th>
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase hidden md:table-cell"
                            style="color:#8E8CAD">Tanggal</th>
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase"
                            style="color:#8E8CAD">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $sub)
                    @php
                        $docCount  = $sub->documents->count();
                        $docTotal  = 3;
                        $docFull   = $docCount >= $docTotal;
                        $docPct    = min(100, round($docCount / $docTotal * 100));
                    @endphp
                    <tr class="group transition-colors duration-150"
                        style="border-bottom:1px solid rgba(70,62,227,0.05);"
                        onmouseover="this.style.background='rgba(70,62,227,0.025)'"
                        onmouseout="this.style.background='transparent'">

                        {{-- Kode --}}
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-[11px] px-2 py-1 rounded-lg"
                                  style="background:#F5F5F5; color:#5A587A; border:1px solid rgba(0,0,0,0.06);">
                                {{ $sub->code }}
                            </span>
                        </td>

                        {{-- Judul --}}
                        <td class="px-5 py-3.5 max-w-[220px]">
                            <p class="font-semibold truncate text-sm" style="color:#0F0E2E">{{ $sub->title }}</p>
                        </td>

                        {{-- Pengaju --}}
                        <td class="px-5 py-3.5 hidden sm:table-cell">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0"
                                     style="background:#F5F5F5; color:#5A587A; border:1px solid rgba(0,0,0,0.06);">
                                    {{ strtoupper(substr($sub->student->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-light truncate max-w-[140px]" style="color:#5A587A">
                                    {{ $sub->student->name }}
                                </span>
                            </div>
                        </td>

                        {{-- Dokumen progress --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                {{-- Mini progress bar --}}
                                <div class="w-16 h-1.5 rounded-full overflow-hidden"
                                     style="background:rgba(70,62,227,0.1);">
                                    <div class="h-full rounded-full transition-all"
                                         style="width:{{ $docPct }}%;
                                                background:{{ $docFull ? '#22C55E' : '#463EE3' }};">
                                    </div>
                                </div>
                                <span class="text-xs font-semibold"
                                      style="color:{{ $docFull ? '#15803d' : '#463EE3' }}">
                                    {{ $docCount }}/{{ $docTotal }}
                                </span>
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-5 py-3.5 hidden md:table-cell">
                            <span class="text-xs font-light" style="color:#8E8CAD">
                                {{ ($sub->submitted_at ?? $sub->created_at)->format('d M Y') }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-3.5">
                            <a href="{{ route('doccheck.show', $sub) }}"
                               class="text-xs font-bold px-3.5 py-1.5 rounded-lg border inline-flex items-center gap-1 transition-all duration-150"
                               style="color:#463EE3; border-color:rgba(70,62,227,0.22); background:white;
                                      box-shadow:0 1px 4px rgba(70,62,227,0.08);"
                               onmouseover="this.style.background='#463EE3'; this.style.color='white'; this.style.borderColor='#463EE3'; this.style.boxShadow='0 3px 10px rgba(70,62,227,0.25)'"
                               onmouseout="this.style.background='white'; this.style.color='#463EE3'; this.style.borderColor='rgba(70,62,227,0.22)'; this.style.boxShadow='0 1px 4px rgba(70,62,227,0.08)'">
                                Periksa
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination (if paginated) --}}
        @if(method_exists($submissions, 'links'))
        <div class="px-5 py-4 border-t" style="border-color:rgba(70,62,227,0.07);">
            {{ $submissions->links() }}
        </div>
        @endif

    @endif
</div>

</x-layouts.app>