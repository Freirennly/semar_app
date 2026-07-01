<x-layouts.app :title="'Setting Sistem'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Setting Sistem</h2>
        <p class="text-sm text-text-secondary mt-1">Konfigurasi parameter global dan preferensi aplikasi SEMAR.</p>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Configuration Form -->
        <div class="col-span-1 lg:col-span-2 card p-6 shadow-sm border-primary/10">
            <form method="POST" action="{{ route('admin.settings.store') }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <h3 class="font-bold text-text text-sm uppercase tracking-wider mb-4 border-b border-border pb-2">Informasi Umum</h3>
                
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



                <div class="flex items-center justify-end gap-3 pt-6 border-t border-border mt-8">
                    <button type="submit" class="btn-primary px-8">Simpan Pengaturan</button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="col-span-1 space-y-6">
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
