<x-layouts.app :title="'Buat Pengajuan'">
    {{-- Header & Breadcrumb --}}
    <div class="mb-6 animate-fade-in">
        <nav class="text-[12px] text-text-secondary mb-2" aria-label="Breadcrumb">
            <a href="{{ route('submissions.index') }}" class="hover:text-primary transition-colors">Pengajuan</a> 
            <span class="mx-1">/</span> 
            <span class="text-text">Buat Baru</span>
        </nav>
        <h1 class="text-3xl md:text-[36px] font-bold text-text">Buat Pengajuan Baru</h1>
        <p class="text-[14px] text-text-secondary mt-1">Silakan lengkapi berkas administrasi dan unggah dokumen pendukung penelitian Anda.</p>
    </div>

    {{-- Kotak Notifikasi Validasi Error --}}
    @if($errors->any())
        <div class="mb-6 bg-danger-bg border border-danger/20 text-danger rounded-xl px-5 py-3 text-[14px] shadow-sm">
            <p class="font-bold mb-1">⚠️ Gagal menyimpan data. Mohon periksa kembali inputan Anda:</p>
            <ul class="list-disc pl-5 space-y-0.5 text-[12px]">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM UTAMA MASUKAN DATA (LAYOUT MEMANJANG PREMIUM) --}}
    <form method="POST" action="{{ route('submissions.store') }}" enctype="multipart/form-data" class="space-y-6 max-w-5xl">
        @csrf

        {{-- KARTU 1: DATA INFORMASI DASAR --}}
        <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
            <h2 class="text-xl md:text-[24px] font-semibold text-text border-b border-border pb-2">Informasi Penelitian</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Judul Penelitian --}}
                <div class="md:col-span-2 space-y-2">
                    <label for="title" class="text-[14px] font-semibold text-text-secondary block">Judul Penelitian <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="input-field" placeholder="Masukkan judul protokol penelitian...">
                </div>

                {{-- Jenis Pengajuan --}}
                <div class="space-y-2">
                    <label for="type" class="text-[14px] font-semibold text-text-secondary block">Jenis Pengajuan <span class="text-danger">*</span></label>
                    <select name="type" id="type" required class="input-field bg-white cursor-pointer">
                        <option value="">Pilih Jenis...</option>
                        <option value="Penelitian" {{ old('type') == 'Penelitian' ? 'selected' : '' }}>Penelitian</option>
                        <option value="Pengabdian" {{ old('type') == 'Pengabdian' ? 'selected' : '' }}>Pengabdian</option>
                        <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            {{-- Abstrak --}}
            <div class="space-y-2">
                <label for="abstract" class="text-[14px] font-semibold text-text-secondary block">Abstrak / Ringkasan Penelitian</label>
                <textarea name="abstract" id="abstract" rows="4" class="input-field font-academic" placeholder="Tuliskan ringkasan singkat penelitian Anda...">{{ old('abstract') }}</textarea>
            </div>
        </div>

        {{-- SEKSI 2: SUBMISSION DOCUMENTS --}}
        <div class="space-y-6">
            <div class="border-b border-border pb-1">
                <h2 class="text-xl md:text-[24px] font-semibold text-text">Submission Documents</h2>
            </div>

            <div class="space-y-6">
                @foreach($documentTemplates as $template)
                    <div class="p-6 bg-white border border-border rounded-xl shadow-sm space-y-6 relative group hover:border-primary/40 transition-all">
                        
                        {{-- Baris Atas: Nama Dokumen & Tombol Unduh Template Master --}}
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div>
                                <h3 class="text-base md:text-[18px] font-medium text-text flex items-center gap-1.5">
                                    {{ $template->name }}
                                    @if($template->is_required)
                                        <span class="text-red-500 font-bold" title="Wajib Diisi">*</span>
                                    @endif
                                </h3>
                                @if($template->description)
                                    <p class="text-[12px] text-text-secondary mt-1">{{ $template->description }}</p>
                                @endif
                            </div>

                            {{-- Tombol Download Template Di Kanan Atas --}}
                            <a href="{{ asset('storage/' . $template->file_path) }}" download class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-primary/20 bg-primary/5 hover:bg-primary/10 text-primary text-[12px] font-bold rounded-lg transition-colors shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download Template
                            </a>
                        </div>

                        {{-- Baris Bawah: Dua Input Pilihan (Upload File atau Hyperlink) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            
                            {{-- Sisi Kiri: Upload File --}}
                            <div class="space-y-2">
                                <label class="text-[14px] font-semibold text-text-secondary block">Upload File</label>
                                <input type="file" 
                                       name="files[{{ $template->id }}]" 
                                       accept=".pdf" 
                                       class="w-full text-[14px] text-text border border-border rounded-lg bg-white file:mr-3 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-[12px] file:font-semibold file:bg-slate-100 file:text-text-secondary hover:file:bg-slate-200 cursor-pointer">
                                <p class="text-[12px] text-text-secondary mt-1">Maximum file size: 10 MB (Format: PDF)</p>
                            </div>

                            {{-- Sisi Kanan: Or Hyperlink GDrive --}}
                            <div class="space-y-2">
                                <label for="link_{{ $template->id }}" class="text-[14px] font-semibold text-text-secondary block">Or Hyperlink</label>
                                <input type="url" 
                                       name="hyperlinks[{{ $template->id }}]" 
                                       id="link_{{ $template->id }}" 
                                       value="{{ old('hyperlinks.'.$template->id) }}"
                                       placeholder="https://drive.google.com/..." 
                                       class="input-field">
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PANEL TOMBOL AKSI BAWAH --}}
        <div class="flex items-center gap-4 pt-6 border-t border-border">
            <button type="submit" class="btn-primary">
                Submit Proposal
            </button>
            <a href="{{ route('submissions.index') }}" class="btn-ghost text-center">
                Batal
            </a>
        </div>
    </form>
</x-layouts.app>