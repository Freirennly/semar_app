<x-layouts.app :title="'Keputusan'">

{{-- ═══════════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════════════ --}}
<div class="mb-8 flex items-start justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1.5">
            <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
            <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Sekretariat KEP</p>
        </div>
        <h1 class="text-2xl font-bold tracking-tight" style="color:#0F0E2E">Keputusan</h1>
        <p class="text-sm font-light mt-1" style="color:#5A587A">Pengajuan yang menunggu keputusan akhir setelah review selesai.</p>
    </div>
    @if(!$submissions->isEmpty())
    <div class="hidden sm:flex items-center gap-2 px-3.5 py-2 rounded-xl border"
         style="background:rgba(245,158,11,0.10); border-color:rgba(245,158,11,0.28);">
        <span class="text-xs font-bold" style="color:#b45309">{{ $submissions->count() }} menunggu</span>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════
     CONTENT
════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border overflow-hidden"
     style="border-color:rgba(245,158,11,0.22);
            box-shadow: 0 2px 16px rgba(245,158,11,0.07);">

    @if($submissions->isEmpty())

        <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4"
                 style="background:#F5F5F5; border:1.5px dashed rgba(245,158,11,0.28);">
                <svg class="w-7 h-7" fill="none" stroke="#f5d08b" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold mb-1" style="color:#5A587A">Tidak ada yang menunggu keputusan</p>
            <p class="text-xs font-light" style="color:#b0aec8">Semua pengajuan sudah diproses</p>
        </div>

    @else

        {{-- Panel header --}}
        <div class="px-5 py-4 border-b flex items-center gap-2.5"
             style="border-color:rgba(245,158,11,0.1); background:rgba(245,158,11,0.03);">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                 style="background:rgba(245,158,11,0.14); box-shadow:0 1px 4px rgba(245,158,11,0.2);">
                <svg class="w-4 h-4" fill="none" stroke="#b45309" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold" style="color:#0F0E2E">Daftar Menunggu Keputusan</h3>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ml-1"
                  style="background:#F59E0B; color:white;">{{ $submissions->count() }}</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid rgba(245,158,11,0.08);">
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase"
                            style="color:#8E8CAD">Kode</th>
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase"
                            style="color:#8E8CAD">Judul Penelitian</th>
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase hidden sm:table-cell"
                            style="color:#8E8CAD">Pengaju</th>
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase"
                            style="color:#8E8CAD">Review</th>
                        <th class="px-5 py-3 text-left font-bold text-[10px] tracking-widest uppercase"
                            style="color:#8E8CAD">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $sub)
                    <tr class="transition-colors duration-150"
                        style="border-bottom:1px solid rgba(245,158,11,0.06);"
                        onmouseover="this.style.background='rgba(245,158,11,0.025)'"
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
                            <p class="font-semibold truncate" style="color:#0F0E2E">{{ $sub->title }}</p>
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

                        {{-- Review count --}}
                        <td class="px-5 py-3.5">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg"
                                 style="background:rgba(245,158,11,0.10); border:1px solid rgba(245,158,11,0.2);">
                                <svg class="w-3 h-3" fill="none" stroke="#b45309" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-xs font-bold" style="color:#b45309">
                                    {{ $sub->reviews->count() }} review
                                </span>
                            </div>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-3.5">
                            <a href="{{ route('decisions.show', $sub) }}"
                               class="text-xs font-bold px-3.5 py-1.5 rounded-lg inline-flex items-center gap-1 transition-all duration-150"
                               style="color:white; background:#463EE3; box-shadow:0 2px 8px rgba(70,62,227,0.25);"
                               onmouseover="this.style.background='#332DB8'; this.style.boxShadow='0 5px 16px rgba(70,62,227,0.35)'; this.style.transform='translateY(-1px)'"
                               onmouseout="this.style.background='#463EE3'; this.style.boxShadow='0 2px 8px rgba(70,62,227,0.25)'; this.style.transform='translateY(0)'">
                                Putuskan
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

        @if(method_exists($submissions, 'links'))
        <div class="px-5 py-4 border-t" style="border-color:rgba(245,158,11,0.08);">
            {{ $submissions->links() }}
        </div>
        @endif

    @endif
</div>

</x-layouts.app>