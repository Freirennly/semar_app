<x-layouts.app :title="'Manajemen Pengumuman'">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-text">Manajemen Pengumuman</h2>
            <p class="text-sm text-text-secondary mt-1">Kelola informasi publik dan pengumuman sistem.</p>
        </div>
        
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <form method="GET" action="{{ route('admin.announcements.index') }}" class="flex items-center gap-2 flex-1 sm:flex-initial">
                <div class="relative w-full sm:w-56">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul..." class="input-field pl-9 pr-3 py-2 w-full text-sm">
                </div>
            </form>
            <a href="{{ route('admin.announcements.create') }}" class="btn-primary py-2 px-4 whitespace-nowrap flex items-center gap-2">
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
                        <th class="px-6 py-4 font-bold min-w-[200px]">Judul Pengumuman</th>
                        <th class="px-6 py-4 font-bold">Tanggal Publish</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-soft-surface/30 transition-colors group">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-text group-hover:text-primary transition-colors line-clamp-2" title="{{ $announcement->title }}">{{ $announcement->title }}</p>
                            <p class="text-xs text-text-muted mt-1 truncate max-w-sm">{{ Str::limit($announcement->content, 50) }}</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-text-secondary whitespace-nowrap">
                            {{ $announcement->publish_date ? $announcement->publish_date->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($announcement->status === 'published')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-success-bg text-success border border-success/20 uppercase tracking-tighter">Published</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-bg text-text-secondary border border-border uppercase tracking-tighter">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-text-muted hover:text-primary transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-2.83.94c-.431.144-.84-.268-.708-.708l.94-2.83a4.5 4.5 0 011.112-1.89l13.43-13.43z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="text-text-muted hover:text-danger transition-colors" onclick="event.preventDefault(); window.confirmModal('Hapus pengumuman ini secara permanen?', this.closest('form'));" title="Hapus">
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
                                <svg class="w-12 h-12 text-text-muted mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.297A1.705 1.705 0 019.336 21H4.104a.71.71 0 01-.7-.718V4.731a.71.71 0 01.7-.717h5.232c.9 0 1.631.733 1.631 1.631zM11 5.882c0-.9.731-1.631 1.631-1.631h5.232c.386 0 .7.314.7.717v15.849a.71.71 0 01-.701.718h-5.232a1.705 1.705 0 01-1.664-1.703V5.882zM7 10h2m-2 3h2m7-3h2m-2 3h2"/></svg>
                                <p class="text-sm text-text-muted italic">Tidak ada pengumuman yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
        <div class="px-6 py-4 border-t border-border bg-bg/10">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</x-layouts.app>
