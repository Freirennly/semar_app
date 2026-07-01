<x-layouts.app :title="'Pengajuan'">
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-6 animate-fade-in">
        <div>
            <h1 class="text-3xl md:text-[36px] font-bold text-text">Pengajuan</h1>
            <p class="text-[14px] text-text-secondary mt-1">Kelola semua pengajuan penelitian Anda.</p>
        </div>
        @role('student')
        <a href="{{ route('submissions.create') }}" class="btn-primary" aria-label="Buat pengajuan baru">+ Buat Pengajuan</a>
        @endrole
    </div>

    {{-- INTEGRASI: DOKUMEN TEMPLATE DARI DATABASE --}}
    @if(isset($documentTemplates) && $documentTemplates->isNotEmpty())
    <div class="mb-8 animate-slide-up" style="animation-delay: 50ms">
        <h2 class="text-xl md:text-[24px] font-semibold text-text mb-4">Template Dokumen Persyaratan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($documentTemplates as $template)
            <div class="bg-white p-6 rounded-2xl border border-border shadow-sm flex flex-col justify-between group hover:border-primary/50 transition-all">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-primary/5 text-primary rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base md:text-[18px] font-medium text-text truncate max-w-[150px]" title="{{ $template->name }}">{{ $template->name }}</h3>
                            @if($template->is_required)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-danger-bg text-danger border border-danger/20 uppercase tracking-tight">Wajib</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-50 text-text-secondary border border-border uppercase tracking-tight">Opsional</span>
                            @endif
                        </div>
                        <p class="text-[12px] text-text-secondary mt-2 line-clamp-2" title="{{ $template->description }}">{{ $template->description ?? 'Unduh template untuk melengkapi berkas.' }}</p>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-t border-border flex items-center justify-between text-[12px] text-text-secondary">
                    <span class="font-mono text-[11px]">{{ $template->file_size }}</span>
                    <a href="{{ route('submissions.download-template', $template) }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-primary-hover font-bold inline-flex items-center gap-1 transition-colors">
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
    <div class="card bg-white rounded-2xl border border-border shadow-sm overflow-hidden animate-slide-up" style="animation-delay: 100ms">
        @if($submissions->isEmpty())
        <div class="text-center py-16 px-6">
            <div class="w-16 h-16 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            </div>
            <h3 class="text-base md:text-[18px] font-medium text-text">Belum ada pengajuan</h3>
            <p class="text-[14px] text-text-secondary mt-1">Buat pengajuan pertama Anda untuk memulai.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-[14px]" id="submissions-table">
                <thead>
                    <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/70">
                        <th class="px-6 py-4 font-semibold">Kode</th>
                        <th class="px-6 py-4 font-semibold">Judul</th>
                        @hasanyrole('sekretariat|ketua|admin')<th class="px-6 py-4 font-semibold hidden sm:table-cell">Pengaju</th>@endhasanyrole
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold hidden sm:table-cell">Tanggal Pengajuan</th>
                        <th class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @foreach($submissions as $sub)
                    <tr class="hover:bg-soft-surface/25 transition-colors">
                        <td class="px-6 py-4 font-mono text-[12px] text-text-secondary">{{ $sub->code }}</td>
                        <td class="px-6 py-4 font-semibold text-text max-w-xs truncate">{{ $sub->title }}</td>
                        @hasanyrole('sekretariat|ketua|admin')<td class="px-6 py-4 text-text-secondary hidden sm:table-cell">{{ $sub->student->name ?? '-' }}</td>@endhasanyrole
                        <td class="px-6 py-4"><x-status-badge :status="$sub->status" /></td>
                        <td class="px-6 py-4 text-text-secondary hidden sm:table-cell">{{ $sub->created_at->timezone('Asia/Jakarta')->format('d M Y') }}</td>
                        <td class="px-6 py-4"><a href="{{ route('submissions.show', $sub) }}" class="text-primary hover:text-primary-hover font-bold transition-colors">Lihat</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-layouts.app>