<x-layouts.app :title="'Cek Dokumen'">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Cek Dokumen</h1>
            <p class="text-sm text-text-secondary mt-1.5">Validasi kelengkapan dokumen pengajuan yang masuk.</p>
        </div>
        @if(!$submissions->isEmpty())
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-border bg-white">
            <span class="text-xs font-bold text-primary">{{ $submissions->count() }} pengajuan baru</span>
        </div>
        @endif
    </div>

    <div class="bg-white border border-border rounded-xl overflow-hidden">
        @if($submissions->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-full bg-soft-surface flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-text mb-1">Tidak ada pengajuan baru</p>
                <p class="text-xs text-text-secondary">Semua dokumen sudah diperiksa.</p>
            </div>
        @else
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="text-sm font-bold text-text uppercase tracking-wider">Daftar Pengajuan Masuk</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                            <th class="px-5 py-3 font-semibold">Kode</th>
                            <th class="px-5 py-3 font-semibold">Judul Penelitian</th>
                            <th class="px-5 py-3 font-semibold hidden sm:table-cell">Pengaju</th>
                            <th class="px-5 py-3 font-semibold">Dokumen</th>
                            <th class="px-5 py-3 font-semibold hidden md:table-cell">Tanggal</th>
                            <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-white">
                        @foreach($submissions as $sub)
                        @php
                            $docCount  = $sub->documents->count();
                            $docTotal  = \App\Models\DocumentTemplate::visible()->where('is_required', true)->count();
                            $docFull   = $docCount >= $docTotal;
                        @endphp
                        <tr class="hover:bg-soft-surface/50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                            <td class="px-5 py-3 max-w-[220px]">
                                <p class="font-semibold text-text truncate" title="{{ $sub->title }}">{{ $sub->title }}</p>
                            </td>
                            <td class="px-5 py-3 hidden sm:table-cell">
                                <span class="text-sm text-text-secondary truncate block max-w-[140px]" title="{{ $sub->student->name }}">{{ $sub->student->name }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs font-bold {{ $docFull ? 'text-success' : 'text-primary' }}">
                                    {{ $docCount }}/{{ $docTotal }} Dokumen
                                </span>
                            </td>
                            <td class="px-5 py-3 hidden md:table-cell">
                                <span class="text-xs text-text-secondary whitespace-nowrap">
                                    {{ ($sub->submitted_at ?? $sub->created_at)->timezone('Asia/Jakarta')->format('d M Y') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('doccheck.show', $sub) }}" class="inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-white text-xs font-bold px-3.5 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                    Periksa
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(method_exists($submissions, 'links'))
            <div class="px-5 py-4 border-t border-border bg-bg/10">
                {{ $submissions->links() }}
            </div>
            @endif
        @endif
    </div>
</x-layouts.app>