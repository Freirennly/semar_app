<x-layouts.app :title="'Manajemen Sekretaris'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Manajemen Sekretaris</h2>
        <p class="text-sm text-text-secondary mt-1">Kelola personil sekretariat untuk validasi dokumen.</p>
    </div>

    <div class="card p-12 flex flex-col items-center justify-center text-center border-dashed border-2 border-border bg-bg/20">
        <div class="w-16 h-16 bg-soft-surface text-primary rounded-2xl flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <h3 class="text-lg font-bold text-text mb-2">Data Sekretariat Kosong</h3>
        <p class="text-sm text-text-muted max-w-sm mb-6">Modul manajemen sekretariat khusus sedang disiapkan. Kelola hak akses sekretariat melalui menu Manajemen User.</p>
        <button disabled class="btn-primary opacity-50 cursor-not-allowed px-8">Tambah Personil</button>
    </div>
</x-layouts.app>
