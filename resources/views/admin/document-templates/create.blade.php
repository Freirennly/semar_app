<x-layouts.app :title="'Tambah Template Dokumen'">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-1.5">
            <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
            <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Administrasi</p>
        </div>
        <h1 class="text-2xl font-bold tracking-tight" style="color:#0F0E2E">Tambah Template Baru</h1>
        <p class="text-sm font-light mt-1" style="color:#5A587A">Buat template dokumen baru untuk digunakan oleh mahasiswa pada pengajuan etik.</p>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <x-alert type="error" class="mb-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <div class="bg-white border border-border rounded-xl p-6 max-w-2xl">
        <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label class="block text-xs font-bold uppercase text-text-muted mb-2">Nama Template <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm"
                       placeholder="Contoh: Informed Consent Form">
            </div>

            {{-- Code --}}
            <div>
                <label class="block text-xs font-bold uppercase text-text-muted mb-2">Kode Template <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" required
                       class="w-full px-4 py-2.5 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm font-mono uppercase"
                       placeholder="Contoh: ICF">
                <p class="text-[11px] text-text-muted mt-1.5">Kode unik sebagai identifikasi bisnis. Akan dinormalisasi ke UPPERCASE_SNAKE_CASE. <strong>Tidak dapat diubah setelah dibuat.</strong></p>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-bold uppercase text-text-muted mb-2">Deskripsi</label>
                <textarea name="description" rows="2"
                          class="w-full px-4 py-2.5 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm"
                          placeholder="Catatan opsional perihal template...">{{ old('description') }}</textarea>
            </div>

            {{-- File Upload --}}
            <div>
                <label class="block text-xs font-bold uppercase text-text-muted mb-2">Berkas Master (.docx / .pdf) <span class="text-red-500">*</span></label>
                <input type="file" name="template_file" required accept=".docx,.doc,.pdf"
                       class="w-full text-sm text-text border border-border bg-soft-surface file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 rounded-xl">
            </div>

            {{-- Checkboxes --}}
            <div class="bg-soft-surface/50 p-4 rounded-xl border border-border space-y-3">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_required" value="1" {{ old('is_required', true) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-border text-primary focus:ring-primary">
                    <div>
                        <span class="text-sm font-semibold text-text block leading-tight">Wajib Diunggah oleh Mahasiswa</span>
                        <span class="text-xs text-text-muted">Jika dicentang, mahasiswa harus mengunggah dokumen ini sebelum pengajuan.</span>
                    </div>
                </label>
                <hr class="border-border">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_shown" value="1" {{ old('is_shown', true) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-border text-primary focus:ring-primary">
                    <div>
                        <span class="text-sm font-semibold text-text block leading-tight">Tampilkan Template</span>
                        <span class="text-xs text-text-muted">Tampilkan template di halaman pengajuan mahasiswa.</span>
                    </div>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-border">
                <a href="{{ route('admin.templates.index') }}"
                   class="px-4 py-2 text-sm font-semibold text-text-secondary border border-border rounded-xl hover:bg-soft-surface transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="bg-primary hover:bg-primary-hover text-white px-5 py-2 text-sm font-bold rounded-xl transition-all">
                    Simpan Template
                </button>
            </div>
        </form>
    </div>

</x-layouts.app>
