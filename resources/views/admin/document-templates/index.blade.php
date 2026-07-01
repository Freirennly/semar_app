<x-layouts.app :title="'Manajemen Template Dokumen'">

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
                <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Administrasi</p>
            </div>
            <h1 class="text-2xl font-bold tracking-tight" style="color:#0F0E2E">Manajemen Template Dokumen</h1>
            <p class="text-sm font-light mt-1" style="color:#5A587A">Kelola template, ketentuan wajib, visibilitas, dan arsip dokumen.</p>
        </div>
        <a href="{{ route('admin.templates.create') }}"
           class="bg-primary hover:bg-primary-hover text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-all inline-flex items-center gap-2 self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Template
        </a>
    </div>

    {{-- Info Card --}}
    <div class="mb-6 bg-white border border-border rounded-xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-bold text-text mb-1">Template Dokumen Bawaan</h2>
            <p class="text-xs text-text-secondary leading-relaxed max-w-3xl">Sistem menyediakan template dokumen standar yang digunakan pada proses pengajuan penelitian. Gunakan fitur ini apabila database baru digunakan atau template bawaan belum tersedia.</p>
            <a href="https://drive.google.com/file/d/137_W1se6SvV6DVS_hTMCiP1reVaF0RHr/view?usp=sharing" target="_blank" class="text-xs font-bold text-primary hover:underline mt-2 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat Panduan
            </a>
        </div>
        <form action="{{ route('admin.templates.restore-default') }}" method="POST" class="shrink-0">
            @csrf
            <button type="button" onclick="event.preventDefault(); window.confirmModal('Pulihkan Template Dokumen Bawaan? Sistem akan otomatis membuat template standar jika belum ada.', this.closest('form'));" class="bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 px-4 py-2.5 rounded-xl text-[12px] font-bold transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Pulihkan Template Bawaan
            </button>
        </form>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background:rgba(34,197,94,0.08); border-color:rgba(34,197,94,0.2); color:#15803d;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.2); color:#b91c1c;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-border p-5 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Total Template</p>
            <p class="text-[28px] font-bold text-text mt-2 leading-none">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Aktif</p>
            <p class="text-[28px] font-bold mt-2 leading-none" style="color:#15803d">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Tersembunyi</p>
            <p class="text-[28px] font-bold mt-2 leading-none" style="color:#8E8CAD">{{ $stats['hidden'] }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Diarsipkan</p>
            <p class="text-[28px] font-bold mt-2 leading-none" style="color:#b91c1c">{{ $stats['archived'] }}</p>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="bg-white border border-border rounded-xl p-4 mb-4 flex flex-col sm:flex-row gap-3">
        <form method="GET" action="{{ route('admin.templates.index') }}" class="flex-1 flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kode template..."
                   class="flex-1 px-4 py-2 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm">
            <select name="filter" class="px-3 py-2 bg-soft-surface border border-border rounded-xl text-sm focus:outline-none focus:border-primary">
                <option value="">Semua</option>
                <option value="active" {{ request('filter') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="hidden" {{ request('filter') === 'hidden' ? 'selected' : '' }}>Tersembunyi</option>
                <option value="archived" {{ request('filter') === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                <option value="required" {{ request('filter') === 'required' ? 'selected' : '' }}>Wajib</option>
            </select>
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-sm font-bold px-4 py-2 rounded-xl transition-all">
                Cari
            </button>
            @if(request('q') || request('filter'))
                <a href="{{ route('admin.templates.index') }}" class="px-3 py-2 text-sm font-medium text-text-secondary hover:text-primary border border-border rounded-xl transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-soft-surface text-[10px] font-bold text-text-muted uppercase tracking-wider border-b border-border">
                        <th class="px-5 py-3.5">Kode</th>
                        <th class="px-5 py-3.5">Nama Template</th>
                        <th class="px-5 py-3.5 hidden md:table-cell">Ukuran</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 hidden lg:table-cell">Penggunaan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-sm">
                    @forelse($templates as $item)
                        <tr class="hover:bg-soft-surface/30 transition-colors {{ $item->is_archived ? 'opacity-60' : '' }}">
                            {{-- Code --}}
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-[11px] px-2 py-1 rounded-lg"
                                      style="background:#F5F5F5; color:#5A587A; border:1px solid rgba(0,0,0,0.06);">
                                    {{ $item->code }}
                                </span>
                            </td>

                            {{-- Name --}}
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-text">{{ $item->name }}</div>
                                @if($item->description)
                                    <div class="text-xs text-text-muted mt-0.5 max-w-sm truncate">{{ $item->description }}</div>
                                @endif
                            </td>

                            {{-- File Size --}}
                            <td class="px-5 py-3.5 hidden md:table-cell font-mono text-xs text-text-secondary">
                                {{ $item->file_size }}
                            </td>

                            {{-- Status Badges --}}
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap gap-1">
                                    @if($item->is_archived)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">Diarsipkan</span>
                                    @else
                                        @if($item->is_required)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">Wajib</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-50 text-gray-600 border border-gray-200">Opsional</span>
                                        @endif

                                        @if($item->is_shown)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Tampil</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-50 text-gray-500 border border-gray-200">Sembunyikan</span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            {{-- Usage --}}
                            <td class="px-5 py-3.5 hidden lg:table-cell">
                                @php
                                    $docCount = $item->documents()->count();
                                    $subCount = $item->submissionCount();
                                @endphp
                                <div class="text-xs text-text-secondary">
                                    <span class="font-semibold text-text">{{ $docCount }}</span> dokumen
                                    ·
                                    <span class="font-semibold text-text">{{ $subCount }}</span> pengajuan
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if(!$item->is_archived)
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.templates.edit', $item) }}"
                                           class="p-1.5 text-text-secondary hover:text-primary rounded-lg hover:bg-soft-surface transition-colors"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        {{-- Toggle Required --}}
                                        <form action="{{ route('admin.templates.toggle-required', $item) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1.5 rounded-lg transition-colors {{ $item->is_required ? 'text-red-600 hover:bg-red-50' : 'text-text-muted hover:text-amber-600 hover:bg-amber-50' }}"
                                                    title="{{ $item->is_required ? 'Jadikan Opsional' : 'Jadikan Wajib' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            </button>
                                        </form>

                                        {{-- Toggle Shown --}}
                                        <form action="{{ route('admin.templates.toggle-shown', $item) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1.5 rounded-lg transition-colors {{ $item->is_shown ? 'text-emerald-600 hover:bg-emerald-50' : 'text-text-muted hover:text-emerald-600 hover:bg-emerald-50' }}"
                                                    title="{{ $item->is_shown ? 'Sembunyikan' : 'Tampilkan' }}">
                                                @if($item->is_shown)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                                @endif
                                            </button>
                                        </form>

                                        {{-- Archive --}}
                                        <form action="{{ route('admin.templates.archive', $item) }}" method="POST" class="inline"
                                              onsubmit="event.preventDefault(); window.confirmModal('Arsipkan template ini? Template yang diarsipkan tidak akan tampil di form pengajuan.', this);">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1.5 text-text-muted hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                    title="Arsipkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Restore --}}
                                        <form action="{{ route('admin.templates.restore', $item) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                                    title="Pulihkan dari Arsip">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-text-muted italic">
                                Belum ada data template yang tersimpan di sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($templates->hasPages())
            <div class="px-5 py-4 border-t border-border">
                {{ $templates->links() }}
            </div>
        @endif
    </div>

</x-layouts.app>
