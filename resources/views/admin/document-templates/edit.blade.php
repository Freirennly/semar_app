<x-layouts.app :title="'Edit Template Dokumen'">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-1.5">
            <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
            <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Administrasi</p>
        </div>
        <h1 class="text-2xl font-bold tracking-tight" style="color:#0F0E2E">Edit Template: {{ $template->name }}</h1>
        <p class="text-sm font-light mt-1" style="color:#5A587A">Perbarui informasi template dokumen.</p>
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

    {{-- Usage Warning --}}
    @if($usageCount > 0)
        <x-alert type="warning" title="Template ini sudah digunakan." class="mb-4">
            Terdapat <strong>{{ $usageCount }}</strong> dokumen pada <strong>{{ $submissionCount }}</strong> pengajuan yang menggunakan template ini. Kode template tidak dapat diubah.
        </x-alert>
    @endif

    <div class="bg-white border border-border rounded-xl p-6 max-w-2xl">
        <form action="{{ route('admin.templates.update', $template) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label class="block text-xs font-bold uppercase text-text-muted mb-2">Nama Template <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                       class="w-full px-4 py-2.5 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm">
            </div>

            {{-- Code (Immutable) --}}
            <div>
                <label class="block text-xs font-bold uppercase text-text-muted mb-2">Kode Template</label>
                <input type="text" value="{{ $template->code }}" disabled
                       class="w-full px-4 py-2.5 bg-gray-100 border border-border rounded-xl text-sm font-mono text-text-muted cursor-not-allowed">
                <p class="text-[11px] text-text-muted mt-1.5">Kode template tidak dapat diubah setelah dibuat.</p>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-bold uppercase text-text-muted mb-2">Deskripsi</label>
                <textarea name="description" rows="2"
                          class="w-full px-4 py-2.5 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm">{{ old('description', $template->description) }}</textarea>
            </div>

            {{-- File Upload --}}
            <div>
                <label class="block text-xs font-bold uppercase text-text-muted mb-2">Perbarui Berkas (Opsional)</label>
                <input type="file" name="template_file" accept=".docx,.doc,.pdf"
                       class="w-full text-sm text-text border border-border bg-soft-surface file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 rounded-xl">
                <p class="text-[11px] text-text-muted mt-1.5">Biarkan kosong jika tidak ingin mengganti file.</p>
            </div>

            {{-- Checkboxes --}}
            <div class="bg-soft-surface/50 p-4 rounded-xl border border-border space-y-3">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_required" value="1" {{ old('is_required', $template->is_required) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-border text-primary focus:ring-primary">
                    <div>
                        <span class="text-sm font-semibold text-text block leading-tight">Wajib Diunggah oleh Mahasiswa</span>
                        <span class="text-xs text-text-muted">Jika dicentang, mahasiswa harus mengunggah dokumen ini.</span>
                    </div>
                </label>
                <hr class="border-border">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_shown" value="1" {{ old('is_shown', $template->is_shown) ? 'checked' : '' }}
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</x-layouts.app>
