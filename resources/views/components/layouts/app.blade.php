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
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
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

    {{-- Global Modal Confirm --}}
    <div id="semar-confirm-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('semar-confirm-modal').classList.add('hidden')"></div>
        <div class="bg-white rounded-2xl shadow-xl transform transition-all sm:max-w-md w-full mx-4 relative z-10 border border-border overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-warning/10 rounded-full mb-4">
                    <svg class="w-6 h-6 text-warning" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-bold text-text mb-2" id="modal-title">Konfirmasi</h3>
                    <p class="text-sm text-text-secondary" id="semar-confirm-message">Apakah Anda yakin ingin melanjutkan?</p>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-center sm:justify-end gap-3 border-t border-border">
                <button type="button" id="semar-confirm-no" class="btn-outline w-full sm:w-auto" onclick="document.getElementById('semar-confirm-modal').classList.add('hidden')">Batal</button>
                <button type="button" id="semar-confirm-yes" class="btn-primary w-full sm:w-auto">Ya, Lanjutkan</button>
            </div>
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

        window.confirmModal = function(message, formElement) {
            const modal = document.getElementById('semar-confirm-modal');
            document.getElementById('semar-confirm-message').textContent = message;
            modal.classList.remove('hidden');
            
            document.getElementById('semar-confirm-yes').onclick = function() {
                modal.classList.add('hidden');
                if (formElement) formElement.submit();
            };
        };
    </script>
</body>
</html>
