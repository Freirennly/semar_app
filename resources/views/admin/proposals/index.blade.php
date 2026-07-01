<x-layouts.app :title="'Manajemen Pengajuan'">
    {{-- Header & Search/Filter Section --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-12">
        <div>
            <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3]">Manajemen Pengajuan</h1>
            <p class="text-sm font-medium text-text-secondary mt-2 leading-[1.2]">Pantau dan kelola seluruh pengajuan proposal dalam sistem SEMAR.</p>
        </div>
        
        {{-- Unified Form for Search and Status Filter --}}
        <form method="GET" action="{{ route('admin.proposals.index') }}" class="flex flex-col sm:flex-row items-center gap-2 w-full xl:w-auto">
            {{-- Input Pencarian --}}
            <div class="relative w-full sm:w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau pengusul..." class="input-field pl-9 pr-3 py-2 w-full text-sm">
            </div>

            {{-- Dropdown Filter Status --}}
            <div class="w-full sm:w-48">
                <select name="status" onchange="this.form.submit()" class="input-field py-2 px-3 w-full text-sm bg-white cursor-pointer appearance-none">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\SubmissionStatus::cases() as $status)
                        {{-- 🟢 FIX: Sembunyikan status DRAFT dari dropdown filter pencarian admin --}}
                        @if($status !== \App\Enums\SubmissionStatus::DRAFT)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', $status->value) }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="btn-primary py-2 px-4 whitespace-nowrap flex-1 sm:flex-none">Cari</button>
                @if(request('q') || request('status'))
                    <a href="{{ route('admin.proposals.index') }}" class="px-4 py-2 bg-soft-surface text-text-secondary text-sm font-semibold rounded-xl hover:bg-border transition-colors whitespace-nowrap" title="Clear Filters">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-border bg-bg/30">
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] w-24">Kode</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] min-w-[200px]">Judul & Pengusul</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Kategori</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Status</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Tanggal</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($proposals as $p)
                    <tr class="hover:bg-soft-surface/30 transition-colors group">
                        <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $p->code }}</td>
                        <td class="px-6 py-4">
                            <p class="font-serif text-text group-hover:text-primary transition-colors line-clamp-2" title="{{ $p->title }}">
                                {{ $p->title }}
                            </p>
                            <p class="text-xs text-text-muted mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ optional($p->student)->name ?? 'Unknown' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-bg text-text-secondary border border-border uppercase tracking-tighter">{{ $p->type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$p->status" />
                        </td>
                        <td class="px-6 py-4 text-xs text-text-secondary whitespace-nowrap">
                            {{ $p->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.proposals.show', $p) }}" class="text-text-muted hover:text-primary transition-colors" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-text-muted mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm text-text-muted italic">Tidak ada data pengajuan yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($proposals->hasPages())
        <div class="px-6 py-4 border-t border-border bg-bg/10">
            {{ $proposals->links() }}
        </div>
        @endif
    </div>
</x-layouts.app>