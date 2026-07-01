<x-layouts.app :title="'Edit Pengajuan'">
    {{-- Header & Breadcrumb --}}
    <div class="mb-6 animate-fade-in">
        <nav class="text-[12px] text-text-secondary mb-2" aria-label="Breadcrumb">
            <a href="{{ route('submissions.index') }}" class="hover:text-primary transition-colors">Pengajuan</a> 
            <span class="mx-1">/</span> 
            <a href="{{ route('submissions.show', $submission) }}" class="hover:text-primary transition-colors">{{ $submission->code }}</a> 
            <span class="mx-1">/</span> 
            <span class="text-text">Edit</span>
        </nav>
        <h1 class="text-3xl md:text-[36px] font-bold text-text">Edit Pengajuan</h1>
    </div>

    {{-- Kotak Notifikasi Validasi Error --}}
    @if($errors->any())
        <x-alert type="error" title="Gagal menyimpan data" class="mb-6">
            <p class="mb-1">Mohon periksa kembali inputan Anda:</p>
            <ul class="list-disc pl-5 space-y-0.5 text-[12px]">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    {{-- FORM UTAMA --}}
    <form method="POST" action="{{ route('submissions.update', $submission) }}" enctype="multipart/form-data" class="space-y-6 max-w-5xl">
        @csrf
        @method('PUT')

        {{-- KARTU 1: DATA INFORMASI DASAR --}}
        <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
            <h2 class="text-xl md:text-[24px] font-semibold text-text border-b border-border pb-2">Informasi Penelitian</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Judul Penelitian --}}
                <div class="md:col-span-2 space-y-2">
                    <label for="title" class="text-[14px] font-semibold text-text-secondary block">Judul Pengajuan <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $submission->title) }}" required class="input-field" placeholder="Masukkan judul protokol penelitian...">
                </div>

                {{-- Jenis Pengajuan --}}
                <div class="space-y-2">
                    <label for="type" class="text-[14px] font-semibold text-text-secondary block">Jenis <span class="text-danger">*</span></label>
                    <select name="type" id="type" required class="input-field bg-white cursor-pointer">
                        @foreach(['Penelitian', 'Pengabdian', 'Lainnya'] as $t)
                            <option value="{{ $t }}" {{ old('type', $submission->type) == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Abstrak --}}
            <div class="space-y-2">
                <label for="abstract" class="text-[14px] font-semibold text-text-secondary block">Abstrak / Ringkasan Penelitian</label>
                <textarea name="abstract" id="abstract" rows="4" class="input-field font-academic" placeholder="Tuliskan ringkasan singkat penelitian Anda...">{{ old('abstract', $submission->abstract) }}</textarea>
            </div>
        </div>

        {{-- SEKSI 2: SUBMISSION DOCUMENTS --}}
        <div class="space-y-6">
            <div class="border-b border-border pb-1">
                <h2 class="text-xl md:text-[24px] font-semibold text-text">Submission Documents</h2>
            </div>

            <div class="space-y-6">
                @foreach($documentTemplates as $template)
                    @php
                        $doc = $submission->documents->firstWhere('document_template_id', $template->id);
                    @endphp
                    <div class="p-5 bg-white border border-border rounded-lg mb-4 hover:border-primary transition-colors">
                        {{-- Header Template Info --}}
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 border-b border-border pb-4 mb-4">
                            <div>
                                <h3 class="text-base font-bold text-text flex items-center gap-2">
                                    {{ $template->name }}
                                    @if($template->is_required)
                                        <span class="text-[10px] uppercase font-bold text-white bg-danger px-2 py-0.5 rounded-full">Wajib</span>
                                    @else
                                        <span class="text-[10px] uppercase font-bold text-text-secondary bg-soft-surface px-2 py-0.5 rounded-full border border-border">Opsional</span>
                                    @endif
                                </h3>
                                @if($template->description)
                                    <p class="text-[12px] text-text-secondary mt-1">{{ $template->description }}</p>
                                @endif
                                
                                {{-- Hardcoded limitations info since configuration columns are not yet in DB --}}
                                <div class="flex flex-wrap items-center gap-3 mt-2">
                                    <span class="text-[11px] font-semibold text-text-muted bg-slate-50 px-2 py-1 rounded border border-slate-200">Format: PDF</span>
                                    <span class="text-[11px] font-semibold text-text-muted bg-slate-50 px-2 py-1 rounded border border-slate-200">Maks: 10 MB</span>
                                </div>
                            </div>

                            @if($template->file_path)
                            <a href="{{ route('submissions.download-template', $template) }}" class="inline-flex items-center justify-center bg-white border border-border hover:border-primary hover:text-primary text-text text-xs font-bold px-3 py-1.5 rounded transition-colors whitespace-nowrap">
                                Unduh Template
                            </a>
                            @endif
                        </div>

                        {{-- Tampilkan info dokumen saat ini jika sudah ada --}}
                        @if($doc)
                            <div class="px-4 py-3 bg-slate-50 rounded mb-4 border border-border flex items-start justify-between text-xs">
                                <div>
                                    <p class="font-bold text-text-secondary uppercase tracking-wider mb-1">Dokumen Terunggah:</p>
                                    @if($doc->type === 'file')
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-primary hover:underline font-bold">{{ $doc->original_name }}</a>
                                    @else
                                        <a href="{{ $doc->file_path }}" target="_blank" class="text-primary hover:underline font-bold">Tautan Google Drive</a>
                                    @endif
                                </div>
                                <span class="text-text-muted hidden md:block">Ganti dengan mengunggah berkas baru.</span>
                            </div>
                        @endif

                        {{-- Area Upload --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Sisi Kiri: Upload File --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-text uppercase tracking-wider">Unggah File PDF Baru</label>
                                <input type="file" 
                                       name="files[{{ $template->id }}]" 
                                       accept=".pdf" 
                                       class="w-full text-sm text-text border border-border rounded bg-white file:mr-3 file:py-2 file:px-3 file:border-0 file:border-r file:border-border file:text-xs file:font-bold file:bg-slate-50 file:text-text-secondary hover:file:bg-slate-100 cursor-pointer transition-colors"
                                       {{ ($template->is_required && !$doc) ? 'required' : '' }}>
                            </div>

                            {{-- Sisi Kanan: Atau Hyperlink --}}
                            <div class="space-y-1.5">
                                <label for="link_{{ $template->id }}" class="text-xs font-bold text-text uppercase tracking-wider">Atau Tautan GDrive Baru</label>
                                <input type="url" 
                                       name="hyperlinks[{{ $template->id }}]" 
                                       id="link_{{ $template->id }}" 
                                       value="{{ old('hyperlinks.'.$template->id, ($doc && $doc->type === 'link') ? $doc->file_path : '') }}"
                                       placeholder="https://drive.google.com/..." 
                                       class="input-field py-2 text-sm bg-slate-50 focus:bg-white"
                                       {{ ($template->is_required && !$doc) ? 'required' : '' }}>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PANEL TOMBOL AKSI BAWAH --}}
        <div class="flex items-center gap-4 pt-6 border-t border-border">
            <button type="submit" class="btn-primary">
                Simpan Perubahan
            </button>
            <a href="{{ route('submissions.show', $submission) }}" class="btn-ghost text-center">
                Batal
            </a>
        </div>
    </form>
</x-layouts.app>
