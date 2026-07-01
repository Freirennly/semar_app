<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SEMAR (Komite Etik Penelitian)</title>
    <meta name="description" content="Masuk ke SEMAR — Sistem Manajemen Etik Riset">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-bg min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-5xl mx-auto">
        <main class="grid grid-cols-1 md:grid-cols-2 rounded-2xl border border-border bg-white overflow-hidden shadow-sm">
            
            <!-- Form Section -->
            <div class="p-10 md:p-14 flex flex-col justify-center relative">
                <header class="absolute top-8 left-10 flex items-center gap-2">
                    <a href="{{ route('landing') }}" class="flex items-center gap-2">
                        <img src="{{ asset('assets/logo.png') }}" alt="SEMAR Logo" class="h-8 w-auto">
                        <span class="text-xl font-bold text-primary tracking-tight">SEMAR</span>
                    </a>
                </header>

                <div class="mt-12 mb-8">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-text mb-2">Masuk ke Sistem</h2>
                        <p class="text-sm text-text-secondary">Silakan masuk menggunakan kredensial Anda.</p>
                    </div>

                    @if($errors->any())
                        <x-alert type="error" :message="$errors->first()" class="mb-5" />
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="relative mb-5">
                            <label for="email" class="block text-sm font-semibold text-text mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                class="w-full rounded-lg bg-bg px-4 py-3 border border-border text-text focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                                placeholder="nama@semar.ac.id">
                        </div>

                        <div class="relative mb-5">
                            <label for="password" class="block text-sm font-semibold text-text mb-2">Kata Sandi</label>
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-lg bg-bg px-4 py-3 border border-border text-text focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                                placeholder="••••••••">
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-8">
                            <a href="{{ route('landing') }}" class="btn-outline px-6 py-3">
                                Kembali ke Beranda
                            </a>

                            <button type="submit" class="btn-primary px-10 py-3">
                                Masuk
                            </button>
                        </div>
                    </form>
                </div>

                <p class="mt-auto pt-8 text-xs text-center text-text-muted">&copy; {{ date('Y') }} Universitas Ultramen. Hak cipta dilindungi.</p>
            </div>

            <!-- Flat Information Section (Replaces Gradient) -->
            <div class="hidden md:flex flex-col items-start justify-center p-12 bg-surface border-l border-border">
                <div class="mb-8">
                    <div class="w-16 h-16 rounded-xl bg-white border border-border flex items-center justify-center text-primary mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-text mb-4 leading-snug">Sistem Manajemen Etik Riset</h3>
                    <p class="text-base text-text-secondary leading-relaxed">
                        Portal resmi administrasi Komite Etik Penelitian Universitas Ultramen. Fasilitas terpusat untuk pengajuan dokumen proposal, penugasan telaah, hingga penerbitan Surat Kelayakan Etik (Ethical Clearance).
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-8 w-full">
                    <div class="p-4 border border-border rounded-lg bg-white">
                        <h4 class="text-sm font-bold text-text mb-1">Pengusul</h4>
                        <p class="text-xs text-text-secondary">Akses pendaftaran usulan baru dan pantauan status.</p>
                    </div>
                    <div class="p-4 border border-border rounded-lg bg-white">
                        <h4 class="text-sm font-bold text-text mb-1">Penelaah</h4>
                        <p class="text-xs text-text-secondary">Fasilitas evaluasi dan penyampaian catatan etik.</p>
                    </div>
                </div>
            </div>
            
        </main>
    </div>
</body>
</html>