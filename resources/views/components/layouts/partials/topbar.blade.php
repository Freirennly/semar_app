<header class="h-16 bg-surface border-b border-border flex items-center justify-between px-4 sm:px-6 shrink-0">
    <div class="flex items-center gap-3">
        {{-- Mobile hamburger --}}
        <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-text-secondary hover:bg-soft-surface" aria-label="Toggle menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        </button>
        <h1 class="text-base font-semibold text-text">{{ $title ?? 'Dashboard' }}</h1>
    </div>

    <div class="flex items-center gap-3">
        {{-- Search bar --}}
        @hasanyrole('student|sekretariat|ketua|admin')
        <form method="GET" action="{{ route('submissions.index') }}" class="hidden sm:flex items-center" role="search">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pengajuan..." class="input-field pl-9 pr-3 py-1.5 w-56 text-sm" aria-label="Cari pengajuan">
            </div>
        </form>
        @endhasanyrole

        <span class="text-sm text-text-secondary hidden sm:block">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-text-muted hover:text-primary transition-colors" aria-label="Keluar dari akun">Keluar</button>
        </form>
    </div>
</header>
