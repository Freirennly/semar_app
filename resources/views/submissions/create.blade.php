<x-layouts.app :title="'Buat Pengajuan'">
    <div class="mb-6">
        <nav class="text-sm text-text-muted mb-2" aria-label="Breadcrumb"><a href="{{ route('submissions.index') }}" class="hover:text-primary">Pengajuan</a> <span class="mx-1">/</span> <span class="text-text">Buat Baru</span></nav>
        <h2 class="text-xl font-bold text-text">Buat Pengajuan Baru</h2>
        <p class="text-sm text-text-secondary mt-1">Isi formulir berikut untuk mengajukan protokol penelitian baru.</p>
    </div>

    <div class="card p-6 max-w-2xl animate-fade-in">
        @if($errors->any())
        <div class="mb-4 bg-danger-bg border border-danger/20 text-danger rounded-lg px-4 py-3 text-sm" role="alert">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('submissions.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Judul Penelitian --}}
            <div class="mb-5">
                <label for="title" class="block text-sm font-medium text-text mb-1.5">Judul Penelitian <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required class="input-field" placeholder="Contoh: Penelitian Dampak Kebijakan Lingkungan">
            </div>

            {{-- Jenis Pengajuan --}}
            <div class="mb-5">
                <label for="type" class="block text-sm font-medium text-text mb-1.5">Jenis Pengajuan <span class="text-danger">*</span></label>
                <select name="type" id="type" required class="input-field">
                    <option value="">Pilih jenis...</option>
                    <option value="Penelitian" {{ old('type') == 'Penelitian' ? 'selected' : '' }}>Penelitian</option>
                    <option value="Pengabdian" {{ old('type') == 'Pengabdian' ? 'selected' : '' }}>Pengabdian</option>
                    <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            {{-- Abstrak --}}
            <div class="mb-5">
                <label for="abstract" class="block text-sm font-medium text-text mb-1.5">Abstrak / Ringkasan</label>
                <textarea name="abstract" id="abstract" rows="4" class="input-field" placeholder="Jelaskan ringkasan penelitian Anda...">{{ old('abstract') }}</textarea>
            </div>

            {{-- File Upload PDF --}}
            <div class="mb-6">
                <label for="file" class="block text-sm font-medium text-text mb-1.5">Dokumen Proposal <span class="text-xs text-text-muted font-normal">(opsional)</span></label>
                <div class="file-upload-zone" id="upload-zone">
                    <input type="file" name="file" id="file" accept=".pdf" class="sr-only" onchange="handleFileSelect(this)">
                    <div id="upload-placeholder" class="text-center">
                        <div class="w-12 h-12 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-primary/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                        </div>
                        <label for="file" class="text-sm font-medium text-primary cursor-pointer hover:text-primary-hover transition-colors">Klik untuk pilih file</label>
                        <p class="text-xs text-text-muted mt-1">atau seret file PDF ke sini</p>
                        <p class="text-xs text-text-muted mt-0.5">Format: PDF · Maks. 10MB</p>
                    </div>
                    <div id="upload-preview" class="hidden">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-danger-bg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-danger" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p id="file-name" class="text-sm font-medium text-text truncate"></p>
                                <p id="file-size" class="text-xs text-text-muted"></p>
                            </div>
                            <button type="button" onclick="clearFile()" class="text-text-muted hover:text-danger transition-colors p-1" aria-label="Hapus file">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan Draft</button>
                <a href="{{ route('submissions.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>

    <script>
        const zone = document.getElementById('upload-zone');
        const input = document.getElementById('file');

        ['dragenter', 'dragover'].forEach(e => zone.addEventListener(e, (ev) => { ev.preventDefault(); zone.classList.add('drag-over'); }));
        ['dragleave', 'drop'].forEach(e => zone.addEventListener(e, (ev) => { ev.preventDefault(); zone.classList.remove('drag-over'); }));
        zone.addEventListener('drop', (ev) => { input.files = ev.dataTransfer.files; handleFileSelect(input); });
        zone.addEventListener('click', (ev) => { if (ev.target === zone || ev.target.closest('#upload-placeholder')) input.click(); });

        function handleFileSelect(el) {
            const file = el.files[0];
            if (!file) return;
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('upload-preview').classList.remove('hidden');
        }
        function clearFile() {
            input.value = '';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('upload-preview').classList.add('hidden');
        }
    </script>
</x-layouts.app>
