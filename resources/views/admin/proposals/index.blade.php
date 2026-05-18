<x-layouts.app :title="'Manajemen Pengajuan'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Manajemen Pengajuan</h2>
        <p class="text-sm text-text-secondary mt-1">Pantau dan kelola seluruh pengajuan proposal dalam sistem SEMAR.</p>
    </div>

    <div class="card p-12 flex flex-col items-center justify-center text-center border-dashed border-2 border-border bg-bg/20">
        <div class="w-16 h-16 bg-soft-surface text-primary rounded-2xl flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-text mb-2">Belum Ada Data Pengajuan</h3>
        <p class="text-sm text-text-muted max-w-sm mb-6">Fitur pemantauan pengajuan terpusat sedang dalam tahap finalisasi. Gunakan Dashboard Overview untuk melihat ringkasan terbaru.</p>
        <button disabled class="btn-primary opacity-50 cursor-not-allowed px-8">Refresh Data</button>
    </div>
</x-layouts.app>
