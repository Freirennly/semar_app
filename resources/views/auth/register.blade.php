<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - SEMAR (Komite Etik Penelitian)</title>
    <meta name="description" content="Daftar akun SEMAR — Sistem Manajemen Etik Riset">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-bg min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-5xl mx-auto">
        <main class="grid grid-cols-1 md:grid-cols-2 rounded-2xl border border-border bg-white overflow-hidden shadow-sm">
            
            <!-- Form Section -->
            <div class="p-10 md:p-12 flex flex-col justify-center relative">
                <header class="absolute top-8 left-10 flex items-center gap-2">
                    <span class="text-xl font-bold text-primary tracking-tight">SEMAR</span>
                </header>

                <div class="mt-12 mb-8">
                    <div class="flex items-center space-x-8 mb-8">
                        <a href="{{ route('login') }}" class="text-2xl font-semibold text-text-muted hover:text-primary pb-2 transition-colors">Masuk</a>
                        <h2 class="text-2xl font-bold text-text border-b-2 border-primary pb-2">Daftar</h2>
                    </div>

                    @if($errors->any())
                        <x-alert type="error" class="mb-5">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="relative mb-5">
                            <label for="name" class="block text-sm font-semibold text-text mb-2">Nama Lengkap</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                class="w-full rounded-lg bg-bg px-4 py-3 border border-border text-text focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                                placeholder="Nama Lengkap dengan Gelar">
                        </div>

                        <div class="relative mb-5">
                            <label for="email" class="block text-sm font-semibold text-text mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full rounded-lg bg-bg px-4 py-3 border border-border text-text focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                                placeholder="nama@semar.ac.id">
                        </div>

                        <div class="relative mb-5">
                            <label for="password" class="block text-sm font-semibold text-text mb-2">Kata Sandi</label>
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-lg bg-bg px-4 py-3 border border-border text-text focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                                placeholder="Minimal 8 Karakter">
                        </div>

                        <div class="relative mb-8">
                            <label for="password_confirmation" class="block text-sm font-semibold text-text mb-2">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full rounded-lg bg-bg px-4 py-3 border border-border text-text focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                                placeholder="Ketik ulang kata sandi">
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-4">
                            <a href="{{ route('landing') }}" class="btn-outline px-6 py-3">
                                Kembali ke Beranda
                            </a>

                            <button type="submit" class="btn-primary px-10 py-3">
                                Buat Akun
                            </button>
                        </div>
                    </form>
                </div>

                <p class="mt-auto pt-4 text-xs text-center text-text-muted">&copy; {{ date('Y') }} Universitas Ultramen. Hak cipta dilindungi.</p>
            </div>

            <!-- Flat Information Section (Replaces Gradient) -->
            <div class="hidden md:flex flex-col items-start justify-center p-12 bg-surface border-l border-border">
                <div class="mb-8">
                    <div class="w-16 h-16 rounded-xl bg-white border border-border flex items-center justify-center text-primary mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-text mb-4 leading-snug">Pendaftaran Pengguna Sistem</h3>
                    <p class="text-base text-text-secondary leading-relaxed">
                        Bagi pengusul dan peneliti yang belum memiliki kredensial Single Sign-On (SSO) institusi, Anda dapat mendaftarkan akun secara mandiri untuk menggunakan layanan pendaftaran kelayakan etik.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-8 w-full">
                    <div class="p-4 border border-border rounded-lg bg-white">
                        <h4 class="text-sm font-bold text-text mb-1">Verifikasi Email</h4>
                        <p class="text-xs text-text-secondary">Pastikan email aktif untuk notifikasi.</p>
                    </div>
                    <div class="p-4 border border-border rounded-lg bg-white">
                        <h4 class="text-sm font-bold text-text mb-1">Keamanan Data</h4>
                        <p class="text-xs text-text-secondary">Data peneliti disimpan dalam server aman.</p>
                    </div>
                </div>
            </div>
            
        </main>
    </div>
</body>
</html>