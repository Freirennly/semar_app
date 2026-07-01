<x-layouts.app :title="'Manajemen Sekretaris'">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-text">Manajemen Sekretaris</h2>
            <p class="text-sm text-text-secondary mt-1">Kelola staf sekretariat untuk validasi dan administrasi dokumen.</p>
        </div>
        
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <form method="GET" action="{{ route('admin.secretariat.index') }}" class="flex items-center gap-2 flex-1 sm:flex-initial">
                <div class="relative w-full sm:w-56">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/posisi..." class="input-field pl-9 pr-3 py-2 w-full text-sm">
                </div>
            </form>
            <a href="{{ route('admin.secretariat.create') }}" class="btn-primary py-2 px-4 whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">Tambah</span>
            </a>
        </div>
    </div>



    <div class="card overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted text-[10px] uppercase tracking-widest border-b border-border bg-bg/30">
                        <th class="px-6 py-4 font-bold min-w-[200px]">Staf Sekretariat</th>
                        <th class="px-6 py-4 font-bold">Posisi/Jabatan</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($secretariat as $staff)
                    <tr class="hover:bg-soft-surface/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-soft-surface flex items-center justify-center text-primary font-bold text-xs shrink-0">
                                    {{ $staff->initials }}
                                </div>
                                <div>
                                    <p class="font-semibold text-text group-hover:text-primary transition-colors">{{ $staff->name }}</p>
                                    <p class="text-xs text-text-secondary">{{ $staff->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-text-secondary">
                            {{ $staff->position ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($staff->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-success-bg text-success border border-success/20 uppercase tracking-tighter">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-danger-bg text-danger border border-danger/20 uppercase tracking-tighter">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.secretariat.edit', $staff) }}" class="text-text-muted hover:text-primary transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-2.83.94c-.431.144-.84-.268-.708-.708l.94-2.83a4.5 4.5 0 011.112-1.89l13.43-13.43z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.secretariat.destroy', $staff) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="text-text-muted hover:text-danger transition-colors" onclick="event.preventDefault(); window.confirmModal('Hapus anggota sekretariat {{ $staff->name }} secara permanen?', this.closest('form'));" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-text-muted mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <p class="text-sm text-text-muted italic">Tidak ada data staf sekretariat yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($secretariat->hasPages())
        <div class="px-6 py-4 border-t border-border bg-bg/10">
            {{ $secretariat->links() }}
        </div>
        @endif
    </div>
</x-layouts.app>
