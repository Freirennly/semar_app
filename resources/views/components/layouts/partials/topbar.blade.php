<header class="h-16 bg-surface border-b border-border flex items-center justify-between px-4 sm:px-6 shrink-0">
    {{-- Left: Mobile Toggle only (no search bar) --}}
    <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-text-secondary hover:bg-soft-surface shrink-0" aria-label="Toggle menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        </button>
    </div>

    {{-- Right: Actions (Notifications, Divider, User Info, Avatar) --}}
    <div class="flex items-center gap-2 sm:gap-4 justify-end">
        {{-- Notifications --}}
        @php
            $unreadCount = auth()->user()->unreadNotifications->count();
        @endphp
        <a href="{{ route('notifications.index') }}" class="p-1.5 text-text-muted hover:text-primary rounded-lg hover:bg-soft-surface transition-colors relative" aria-label="Notifikasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
            @if($unreadCount > 0)
                <span class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 bg-danger text-white text-[9px] font-extrabold px-1 rounded-full flex items-center justify-center border border-white">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>

        <div class="h-6 w-px bg-border"></div>

        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-bold text-text leading-none">{{ auth()->user()->name }}</p>
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-tighter mt-1">{{ auth()->user()->roles->first()?->name ?? 'User' }}</p>
            </div>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=463EE3&color=fff" class="w-8 h-8 rounded-lg border border-border" alt="Profile">
        </div>
    </div>
</header>
