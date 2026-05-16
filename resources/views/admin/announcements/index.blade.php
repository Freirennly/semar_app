<x-layouts.app :title="'Manajemen Pengumuman'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Manajemen Pengumuman</h2>
        <p class="text-sm text-text-secondary mt-1">Kelola informasi dan pengumuman publik di halaman landing.</p>
    </div>

    <div class="card p-12 flex flex-col items-center justify-center text-center border-dashed border-2 border-border bg-bg/20">
        <div class="w-16 h-16 bg-soft-surface text-primary rounded-2xl flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.297A1.705 1.705 0 019.336 21H4.104a.71.71 0 01-.7-.718V4.731a.71.71 0 01.7-.717h5.232c.9 0 1.631.733 1.631 1.631zM11 5.882c0-.9.731-1.631 1.631-1.631h5.232c.386 0 .7.314.7.717v15.849a.71.71 0 01-.701.718h-5.232a1.705 1.705 0 01-1.664-1.703V5.882zM7 10h2m-2 3h2m7-3h2m-2 3h2"/></svg>
        </div>
        <h3 class="text-lg font-bold text-text mb-2">Belum Ada Pengumuman</h3>
        <p class="text-sm text-text-muted max-w-sm mb-6">Anda dapat membuat pengumuman baru untuk ditampilkan pada landing page setelah modul ini diaktifkan.</p>
        <button disabled class="btn-primary opacity-50 cursor-not-allowed px-8">Buat Pengumuman</button>
    </div>
</x-layouts.app>
