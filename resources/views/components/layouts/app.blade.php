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
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                        <x-alert type="success" :message="session('success')" class="mb-6" />
                    @endif
                    @if(session('error'))
                        <x-alert type="error" :message="session('error')" class="mb-6" />
                    @endif
                    @if(session('warning'))
                        <x-alert type="warning" :message="session('warning')" class="mb-6" />
                    @endif
                    @if(session('info'))
                        <x-alert type="info" :message="session('info')" class="mb-6" />
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
        setTimeout(() => { document.querySelectorAll('[role="alert"]').forEach(el => el.remove()); }, 5000);
    </script>
</body>
</html>
