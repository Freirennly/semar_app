<x-layouts.app :title="'Laporan & Statistik'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Laporan & Statistik</h2>
        <p class="text-sm text-text-secondary mt-1">Analisis data pengajuan dan kinerja sistem secara mendalam.</p>
    </div>

    <div class="card p-12 flex flex-col items-center justify-center text-center border-dashed border-2 border-border bg-bg/20">
        <div class="w-16 h-16 bg-soft-surface text-primary rounded-2xl flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-text mb-2">Statistik Sedang Dimuat</h3>
        <p class="text-sm text-text-muted max-w-sm mb-6">Modul pelaporan lanjutan sedang dalam tahap sinkronisasi data. Periksa kembali beberapa saat lagi untuk grafik mendalam.</p>
        <button disabled class="btn-primary opacity-50 cursor-not-allowed px-8">Unduh Laporan</button>
    </div>
</x-layouts.app>
