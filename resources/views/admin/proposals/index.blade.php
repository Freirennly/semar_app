<x-layouts.app :title="'Manajemen Pengajuan'">
    {{-- Header & Search/Filter Section --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-text">Manajemen Pengajuan</h2>
            <p class="text-sm text-text-secondary mt-1">Pantau dan kelola seluruh pengajuan proposal dalam sistem SEMAR.</p>
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
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', $status->value) }}
                        </option>
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

    @if(session('success'))
        <div class="mb-6 bg-success-bg border border-success/20 text-success rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="card overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted text-[10px] uppercase tracking-widest border-b border-border bg-bg/30">
                        <th class="px-6 py-4 font-bold w-24">Kode</th>
                        <th class="px-6 py-4 font-bold min-w-[200px]">Judul & Pengusul</th>
                        <th class="px-6 py-4 font-bold">Kategori</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold">Tanggal</th>
                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($proposals as $p)
                    <tr class="hover:bg-soft-surface/30 transition-colors group">
                        <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $p->code }}</td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-text group-hover:text-primary transition-colors line-clamp-2" title="{{ $p->title }}">{{ $p->title }}</p>
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
                                <a href="{{ route('admin.proposals.edit', $p) }}" class="text-text-muted hover:text-primary transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-2.83.94c-.431.144-.84-.268-.708-.708l.94-2.83a4.5 4.5 0 011.112-1.89l13.43-13.43z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.proposals.destroy', $p) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-text-muted hover:text-danger transition-colors" onclick="return confirm('Hapus pengajuan ini secara permanen?')" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </form>
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