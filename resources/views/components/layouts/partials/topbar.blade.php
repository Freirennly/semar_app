<header class="h-16 bg-surface border-b border-border flex items-center justify-between px-4 sm:px-6 shrink-0">
    {{-- Left: Mobile Toggle + Searchbar --}}
    <div class="flex items-center gap-3 flex-1">
        <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-text-secondary hover:bg-soft-surface shrink-0" aria-label="Toggle menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        </button>

        {{-- Searchbar --}}
        <div class="relative hidden sm:block w-full max-w-[400px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input type="text" placeholder="Cari pengajuan, kode, peneliti..." class="w-full h-10 pl-9 pr-4 text-sm font-medium text-text bg-bg border border-border rounded-xl placeholder:text-text-muted focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary-ring transition-all" aria-label="Pencarian global">
        </div>
    </div>

    {{-- Right: Notifications -> Divider -> User Text Stack -> Avatar --}}
    <div class="flex items-center gap-3 sm:gap-4 shrink-0">
        {{-- Notification Bell --}}
        @php
            $unreadCount = auth()->user()->unreadNotifications->count();
        @endphp
        <a href="{{ route('notifications.index') }}" class="p-2 text-text-muted hover:text-primary rounded-lg hover:bg-soft-surface transition-colors relative" aria-label="Notifikasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
            @if($unreadCount > 0)
                <span class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 bg-danger text-white text-[9px] font-extrabold px-1 rounded-full flex items-center justify-center border border-white">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>

        {{-- Vertical Divider --}}
        <div class="h-7 w-px bg-border"></div>

        {{-- User Text Stack + Circular Avatar --}}
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-[14px] font-semibold text-text leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[11px] font-normal text-text-muted mt-0.5">{{ auth()->user()->roles->first()?->name ?? 'User' }}</p>
            </div>
            @php
                $initials = collect(explode(' ', auth()->user()->name))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
            @endphp
            <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center text-[12px] font-bold tracking-tight border-2 border-primary/20 shrink-0" title="{{ auth()->user()->name }}">
                {{ $initials }}
            </div>
        </div>
    </div>
</header>
