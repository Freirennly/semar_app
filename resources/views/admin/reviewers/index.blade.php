<x-layouts.app :title="'Manajemen Reviewer'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Manajemen Reviewer</h2>
        <p class="text-sm text-text-secondary mt-1">Kelola daftar reviewer dan beban kerja peninjauan.</p>
    </div>

    <div class="card p-12 flex flex-col items-center justify-center text-center border-dashed border-2 border-border bg-bg/20">
        <div class="w-16 h-16 bg-soft-surface text-primary rounded-2xl flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-text mb-2">Data Reviewer Kosong</h3>
        <p class="text-sm text-text-muted max-w-sm mb-6">Manajemen reviewer akan tersedia segera. Saat ini Anda dapat mengelola akun reviewer melalui Manajemen User.</p>
        <button disabled class="btn-primary opacity-50 cursor-not-allowed px-8">Tambah Reviewer</button>
    </div>
</x-layouts.app>
