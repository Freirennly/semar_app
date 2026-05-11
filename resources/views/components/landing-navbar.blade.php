<header class="sticky top-0 z-50 bg-primary border-b border-white/10 transition-all duration-300 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Brand -->
            <div class="flex-shrink-0 flex items-center gap-3">
                <a href="{{ route('landing') }}" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-white/10 text-white rounded-xl flex items-center justify-center font-bold text-xl group-hover:bg-white group-hover:text-primary transition-colors duration-300">
                        S
                    </div>
                    <div>
                        <h1 class="font-bold text-xl leading-none text-white transition-colors">SEMAR</h1>
                        <p class="text-[0.65rem] text-white/70 font-medium tracking-wider uppercase mt-0.5">Portal Manajemen Riset</p>
                    </div>
                </a>
            </div>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('landing') }}" class="text-sm font-medium {{ request()->routeIs('landing') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }} px-3 py-2 rounded-lg transition-colors duration-200">Beranda</a>
                <a href="{{ route('about') }}" class="text-sm font-medium {{ request()->routeIs('about') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }} px-3 py-2 rounded-lg transition-colors duration-200">Tentang Kami</a>
                <a href="{{ route('sop') }}" class="text-sm font-medium {{ request()->routeIs('sop') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }} px-3 py-2 rounded-lg transition-colors duration-200">Pelajari SOP</a>
                
                <div class="h-6 w-px bg-white/20 mx-2"></div>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-primary hover:bg-gray-50 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 flex items-center gap-2 shadow-sm">
                        <span>Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-white text-primary hover:bg-gray-50 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 shadow-sm">Login SSO</a>
                @endauth
            </nav>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button type="button" id="mobile-menu-button" onclick="toggleMobileMenu()" class="p-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-white/50" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Buka menu utama</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Panel -->
    <div id="mobile-menu" class="hidden md:hidden bg-primary border-b border-white/10 shadow-xl absolute w-full left-0 origin-top">
        <div class="px-4 pt-2 pb-6 space-y-1">
            <a href="{{ route('landing') }}" class="block px-4 py-3 rounded-lg text-base font-medium {{ request()->routeIs('landing') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-colors">Beranda</a>
            <a href="{{ route('about') }}" class="block px-4 py-3 rounded-lg text-base font-medium {{ request()->routeIs('about') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-colors">Tentang Kami</a>
            <a href="{{ route('sop') }}" class="block px-4 py-3 rounded-lg text-base font-medium {{ request()->routeIs('sop') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-colors">Pelajari SOP</a>
            
            <div class="pt-4 pb-2 border-t border-white/10 mt-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="flex justify-center w-full bg-white text-primary hover:bg-gray-50 rounded-lg py-3 font-semibold transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="flex justify-center w-full bg-white text-primary hover:bg-gray-50 rounded-lg py-3 font-semibold transition-colors shadow-sm">Login SSO</a>
                @endauth
            </div>
        </div>
    </div>
</header>
