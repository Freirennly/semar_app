<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SEMAR — Sistem Manajemen Pengajuan & Validasi Penelitian">
    <title>{{ $title ?? 'Dashboard' }} — SEMAR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-bg text-text">
    <div class="flex h-screen overflow-hidden">
        {{-- Mobile overlay --}}
        <div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 bg-black/30 z-40 lg:hidden" onclick="toggleSidebar()"></div>

        {{-- Sidebar (desktop always visible, mobile toggled) --}}
        <aside id="sidebar" class="sidebar-desktop w-64 bg-surface border-r border-border flex flex-col h-full shrink-0 z-50 fixed lg:static lg:flex">
            @include('components.layouts.partials.sidebar')
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            @include('components.layouts.partials.topbar')
            <main class="flex-1 overflow-y-auto bg-bg">
                <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                    {{-- Flash messages --}}
                    @if(session('success'))
                    <div class="mb-4 border-l-4 border-l-primary bg-soft-surface text-primary rounded-lg px-4 py-3 text-sm flex items-center justify-between" id="toast-success" role="alert">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-primary/60 hover:text-primary" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="mb-4 border-l-4 border-l-danger bg-danger-bg text-danger rounded-lg px-4 py-3 text-sm flex items-center justify-between" id="toast-error" role="alert">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-danger/60 hover:text-danger" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                    @endif
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('sidebar-mobile');
            sidebar.classList.toggle('open');
            sidebar.classList.toggle('sidebar-desktop');
            overlay.classList.toggle('open');
        }
        setTimeout(() => { document.getElementById('toast-success')?.remove(); document.getElementById('toast-error')?.remove(); }, 5000);
    </script>
</body>
</html>
