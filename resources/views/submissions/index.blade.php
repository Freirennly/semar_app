{{--index pengajuan--}}
<x-layouts.app :title="'Pengajuan'">
    <div class="flex items-center justify-between mb-6 animate-fade-in">
        <div>
            <h2 class="text-xl font-bold text-text">Pengajuan</h2>
            <p class="text-sm text-text-secondary mt-1">Kelola semua pengajuan penelitian Anda.</p>
        </div>
        @role('student')
        <a href="{{ route('submissions.create') }}" class="btn-primary" aria-label="Buat pengajuan baru">+ Buat Pengajuan</a>
        @endrole
    </div>

    {{-- INTEGRASI: DOKUMEN TEMPLATE DARI DATABASE --}}
    @if(isset($documentTemplates) && $documentTemplates->isNotEmpty())
    <div class="mb-8 animate-slide-up" style="animation-delay: 50ms">
        <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted mb-3">Template Dokumen Persyaratan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($documentTemplates as $template)
            <div class="bg-white p-4 rounded-xl border border-border shadow-sm flex flex-col justify-between group hover:border-primary/50 transition-all">
                <div class="flex items-start gap-3">
                    <div class="p-2.5 bg-primary/5 text-primary rounded-lg shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-sm font-semibold text-text truncate max-w-[150px]" title="{{ $template->name }}">{{ $template->name }}</h4>
                            @if($template->is_required)
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-red-50 text-red-600 border border-red-100 uppercase tracking-tight">Wajib</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-slate-50 text-text-secondary border border-border uppercase tracking-tight">Opsional</span>
                            @endif
                        </div>
                        <p class="text-xs text-text-muted mt-1 line-clamp-2" title="{{ $template->description }}">{{ $template->description ?? 'Unduh template untuk melengkapi berkas.' }}</p>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-t border-border flex items-center justify-between text-xs text-text-muted">
                    <span class="font-mono text-[11px]">{{ $template->file_size }}</span>
                    <a href="{{ asset('storage/' . $template->file_path) }}" download class="text-primary hover:text-primary-hover font-bold inline-flex items-center gap-1 transition-colors">
                        Unduh Berkas
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Daftar Riwayat Pengajuan --}}
    <div class="card animate-slide-up" style="animation-delay: 100ms">
        @if($submissions->isEmpty())
        <div class="text-center py-16">
            <div class="w-14 h-14 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            </div>
            <p class="text-sm font-medium text-text">Belum ada pengajuan</p>
            <p class="text-sm text-text-secondary mt-1">Buat pengajuan pertama Anda untuk memulai.</p>
            @role('student')<a href="{{ route('submissions.create') }}" class="inline-block mt-3 btn-primary">+ Buat Pengajuan</a>@endrole
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="submissions-table">
                <thead>
                    <tr class="text-left text-text-muted text-xs uppercase tracking-wider border-b border-border">
                        <th class="px-6 py-3 font-medium">Kode</th>
                        <th class="px-6 py-3 font-medium">Judul</th>
                        @hasanyrole('sekretariat|ketua|admin')<th class="px-6 py-3 font-medium hidden sm:table-cell">Pengaju</th>@endhasanyrole
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium hidden sm:table-cell">Tanggal Pengajuan</th>
                        <th class="px-6 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($submissions as $sub)
                    <tr class="hover:bg-soft-surface/30 transition-colors">
                        <td class="px-6 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                        <td class="px-6 py-3 font-medium text-text max-w-xs truncate">{{ $sub->title }}</td>
                        @hasanyrole('sekretariat|ketua|admin')<td class="px-6 py-3 text-text-secondary hidden sm:table-cell">{{ $sub->student->name ?? '-' }}</td>@endhasanyrole
                        <td class="px-6 py-3"><x-status-badge :status="$sub->status" /></td>
                        <td class="px-6 py-3 text-text-secondary hidden sm:table-cell">{{ $sub->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3"><a href="{{ route('submissions.show', $sub) }}" class="text-primary hover:text-primary-hover text-sm font-medium transition-colors">Lihat</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-layouts.app>