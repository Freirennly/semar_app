<x-layouts.app :title="'Surat Kelayakan Etik (Ethical Clearance)'">
    {{-- Header --}}
    <div class="mb-6 animate-fade-in">
        <nav class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">
            Dashboard <span class="mx-1">/</span> Surat Kelayakan Etik (Ethical Clearance)
        </nav>
        <h2 class="text-xl font-bold text-text">Surat Kelayakan Etik (Ethical Clearance)</h2>
        <p class="text-sm text-text-secondary mt-1">Unduh Surat Kelayakan Etik (Ethical Clearance) resmi untuk penelitian Anda yang telah disetujui Komisi Etik.</p>
    </div>

    {{-- Content List --}}
    <div class="space-y-4 animate-slide-up">
        @forelse($approvedSubmissions as $sub)
            <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden p-6 hover:border-primary/30 transition-colors group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-1 flex-1 min-w-0">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-primary bg-primary/5 px-2 py-0.5 rounded border border-primary/10">{{ $sub->code }}</span>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Issued (Final)
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-text group-hover:text-primary transition-colors truncate" title="{{ $sub->title }}">
                            {{ $sub->title }}
                        </h3>
                        <p class="text-xs text-text-muted flex items-center gap-1.5 pt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Diterbitkan pada {{ $sub->updated_at->timezone('Asia/Jakarta')->format('d M Y') }}
                        </p>
                    </div>

                    {{-- Bagian Download Box --}}
                    <div class="bg-soft-surface/60 border border-border p-4 rounded-xl flex items-center justify-between gap-6 lg:w-96 shrink-0">
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-text uppercase tracking-wider">Surat Kelayakan Etik Tersedia</h4>
                            <p class="text-[11px] text-text-muted mt-0.5 truncate">Dokumen pengesahan siap digunakan.</p>
                        </div>
                        {{-- Memanggil route downloadEc yang sudah ada di SubmissionController Anda --}}
                        <a href="{{ route('submissions.download-ec', $sub) }}" class="btn-primary py-2 px-4 text-xs font-semibold rounded-xl shadow-sm flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh Surat Kelayakan Etik
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card text-center py-16 bg-white rounded-2xl border border-border shadow-sm">
                <div class="w-14 h-14 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-3 text-text-muted/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
                <p class="text-sm font-semibold text-text">Belum Ada Surat Kelayakan Etik (Ethical Clearance) Terbit</p>
                <p class="text-xs text-text-secondary mt-1 max-w-xs mx-auto">Surat Kelayakan Etik (Ethical Clearance) akan muncul di sini secara otomatis begitu pengajuan Anda dinyatakan lolos dan di-publish oleh komisi etik.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>