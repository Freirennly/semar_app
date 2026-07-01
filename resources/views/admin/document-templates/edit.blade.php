<x-layouts.app :title="'Edit Template Dokumen'">

    {{-- Page Header --}}
    <div class="mb-12 animate-fade-in">
        <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3]">Edit Template: {{ $template->name }}</h1>
        <p class="text-sm font-medium text-text-secondary mt-2 leading-[1.2]">Perbarui informasi template dokumen.</p>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl border text-sm"
             style="background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.2); color:#b91c1c;">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Usage Warning --}}
    @if($usageCount > 0)
        <div class="mb-4 px-4 py-3 rounded-xl border text-sm"
             style="background:rgba(234,179,8,0.08); border-color:rgba(234,179,8,0.25); color:#92400e;">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <strong>Template ini sudah digunakan.</strong>
                    <p class="mt-0.5">Terdapat <strong>{{ $usageCount }}</strong> dokumen pada <strong>{{ $submissionCount }}</strong> pengajuan yang menggunakan template ini. Kode template tidak dapat diubah.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white border border-border rounded-xl p-6 max-w-2xl mb-12">
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
