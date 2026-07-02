<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SEMAR — Portal Manajemen Riset. Automasi pengajuan protokol penelitian dan pemantauan klirens etik secara real-time.">
    <title>{{ $title ?? 'SEMAR — Portal Manajemen Riset' }}</title>
    <link rel="preload" href="{{ Vite::asset('resources/assets/static/PlusJakartaSans-Regular.ttf') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/assets/static/PlusJakartaSans-Bold.ttf') }}" as="font" type="font/ttf" crossorigin>
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/assets/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-bg text-text antialiased selection:bg-primary selection:text-white">
    <x-landing-navbar />

    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <x-landing-footer />

    <script>
        // Simple script for mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Close mobile menu when clicking outside or on a link
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobile-menu-button');
            if (!menu.classList.contains('hidden') && !menu.contains(event.target) && !button.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
