<x-layouts.app :title="'Tambah Pengumuman'">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.announcements.index') }}" class="p-2 rounded-lg bg-white border border-border text-text-secondary hover:text-primary hover:border-primary/30 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-text">Tambah Pengumuman</h2>
            <p class="text-sm text-text-secondary mt-1">Buat pengumuman baru untuk ditampilkan di sistem.</p>
        </div>
    </div>

    <div class="card p-6 border-primary/10 shadow-sm max-w-4xl">
        <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-5">
            @csrf
            
            <div>
                <label for="title" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Judul Pengumuman</label>
                <input type="text" name="title" id="title" required class="input-field" value="{{ old('title') }}" placeholder="Contoh: Jadwal Penerimaan Proposal Diperpanjang">
                @error('title')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="content" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Isi Pengumuman</label>
                <textarea name="content" id="content" rows="6" required class="input-field" placeholder="Tuliskan isi pengumuman di sini...">{{ old('content') }}</textarea>
                @error('content')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="status" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Status Publish</label>
                    <select name="status" id="status" class="input-field">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Simpan sementara)</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (Tampilkan ke publik)</option>
                    </select>
                    @error('status')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="publish_date" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Tanggal Publish (Opsional)</label>
                    <input type="datetime-local" name="publish_date" id="publish_date" class="input-field" value="{{ old('publish_date') }}">
                    <p class="text-[10px] text-text-muted mt-1">Biarkan kosong untuk menggunakan waktu saat ini jika status Published.</p>
                    @error('publish_date')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border mt-6">
                <a href="{{ route('admin.announcements.index') }}" class="btn-ghost">Batal</a>
                <button type="submit" class="btn-primary px-8">Simpan Pengumuman</button>
            </div>
        </form>
    </div>
</x-layouts.app>
