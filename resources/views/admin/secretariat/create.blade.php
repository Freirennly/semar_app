<x-layouts.app :title="'Tambah Sekretaris'">
    <div class="mb-12 flex items-center gap-3 animate-fade-in">
        <a href="{{ route('admin.secretariat.index') }}" class="p-2 rounded-lg bg-white border border-border text-text-secondary hover:text-primary hover:border-primary/30 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3]">Tambah Sekretaris</h1>
            <p class="text-sm font-medium text-text-secondary mt-2 leading-[1.2]">Tambahkan akun staf sekretariat baru ke dalam sistem.</p>
        </div>
    </div>

    <div class="card p-6 border-primary/10 shadow-sm max-w-3xl mb-12">
        <form method="POST" action="{{ route('admin.secretariat.store') }}" class="space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" required class="input-field" value="{{ old('name') }}" placeholder="John Doe">
                    @error('name')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" required class="input-field" value="{{ old('email') }}" placeholder="john@example.com">
                    @error('email')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" name="password" id="password" required class="input-field" placeholder="Minimal 8 karakter">
                    @error('password')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="position" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Posisi/Jabatan</label>
                    <input type="text" name="position" id="position" class="input-field" value="{{ old('position') }}" placeholder="Contoh: Admin Dokumen">
                    @error('position')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-primary rounded border-border-strong focus:ring-primary focus:ring-offset-0">
                    <span class="text-sm font-semibold text-text">Akun Aktif</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border mt-6">
                <a href="{{ route('admin.secretariat.index') }}" class="btn-ghost">Batal</a>
                <button type="submit" class="btn-primary px-8">Simpan Staf</button>
            </div>
        </form>
    </div>
</x-layouts.app>
