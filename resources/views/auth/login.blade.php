<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — SEMAR</title>
    <meta name="description" content="Masuk ke SEMAR — Sistem Manajemen Pengajuan & Validasi Penelitian">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-bg text-text min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm mx-auto px-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary tracking-tight">SEMAR</h1>
            <p class="text-sm text-text-secondary mt-1">Sistem Manajemen Pengajuan & Validasi</p>
        </div>
        <div class="card p-8">
            <h2 class="text-lg font-semibold text-text mb-6">Masuk ke akun Anda</h2>

            @if($errors->any())
            <div class="mb-4 bg-danger-bg border border-danger/20 text-danger rounded-lg px-4 py-3 text-sm" role="alert">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-text mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="input-field" placeholder="nama@semar.ac.id">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-text mb-1.5">Password</label>
                    <input type="password" name="password" id="password" required
                        class="input-field" placeholder="••••••••">
                </div>
                <button type="submit" class="w-full btn-primary py-2.5">Masuk</button>
            </form>
        </div>
        <p class="text-center text-xs text-text-muted mt-6">Universitas Ultramen Surakarta © 2026</p>
    </div>
</body>
</html>
