<x-layouts.app :title="'Setting Sistem'">
    <div class="mb-12 animate-fade-in">
        <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3]">Setting Sistem</h1>
        <p class="text-sm font-medium text-text-secondary mt-2 leading-[1.2]">Konfigurasi parameter global dan preferensi aplikasi SEMAR.</p>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">
        <!-- Configuration Form -->
        <div class="col-span-1 lg:col-span-7 card p-6 shadow-sm border-primary/10">
            <form method="POST" action="{{ route('admin.settings.store') }}" class="space-y-6">
                @csrf
                
                <h2 class="text-[24px] font-semibold text-text leading-[1.4] mb-4 border-b border-border pb-2">Informasi Umum</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="application_name" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Aplikasi</label>
                        <input type="text" name="application_name" id="application_name" required class="input-field" value="{{ old('application_name', $settings['application_name']) }}">
                        @error('application_name')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="institution_name" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Institusi</label>
                        <input type="text" name="institution_name" id="institution_name" required class="input-field" value="{{ old('institution_name', $settings['institution_name']) }}">
                        @error('institution_name')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="contact_email" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Email Kontak Dukungan</label>
                    <input type="email" name="contact_email" id="contact_email" required class="input-field" value="{{ old('contact_email', $settings['contact_email']) }}">
                    <p class="text-[10px] text-text-muted mt-1">Email ini akan ditampilkan jika user mengalami masalah.</p>
                    @error('contact_email')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <h2 class="text-[24px] font-semibold text-text leading-[1.4] mb-4 border-b border-border pb-2 pt-4 mt-8">Keamanan & Pemeliharaan</h2>
                
                <div class="p-4 rounded-xl border border-warning/30 bg-warning-bg/50">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-warning/20 text-warning flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="flex-1">
                            <label class="flex items-center gap-3 cursor-pointer mb-1">
                                <input type="checkbox" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings['maintenance_mode']) === '1' ? 'checked' : '' }} class="w-5 h-5 text-warning rounded border-border-strong focus:ring-warning focus:ring-offset-0">
                                <span class="text-sm font-bold text-warning-dark uppercase tracking-wide">Mode Pemeliharaan (Maintenance Mode)</span>
                            </label>
                            <p class="text-xs text-text-secondary mt-1 leading-relaxed">
                                Jika diaktifkan, semua pengguna selain Administrator tidak akan bisa mengakses sistem. Gunakan hanya saat melakukan update atau perbaikan krusial.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-border mt-8">
                    <button type="submit" class="btn-primary px-8">Simpan Pengaturan</button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="col-span-1 lg:col-span-3 space-y-6">
            <div class="card p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4 text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="font-bold text-text text-sm uppercase tracking-wider">Informasi Sistem</h3>
                </div>
                <ul class="space-y-3 text-sm">
                    <li class="flex justify-between items-center py-2 border-b border-border">
                        <span class="text-text-secondary">Versi Aplikasi</span>
                        <span class="font-mono font-semibold text-text">v1.0.0</span>
                    </li>
                    <li class="flex justify-between items-center py-2 border-b border-border">
                        <span class="text-text-secondary">Framework</span>
                        <span class="font-mono font-semibold text-text">Laravel 12.0</span>
                    </li>
                    <li class="flex justify-between items-center py-2 border-b border-border">
                        <span class="text-text-secondary">PHP Version</span>
                        <span class="font-mono font-semibold text-text">{{ PHP_VERSION }}</span>
                    </li>
                    <li class="flex justify-between items-center py-2">
                        <span class="text-text-secondary">Timezone</span>
                        <span class="font-mono font-semibold text-text">{{ config('app.timezone') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-layouts.app>
