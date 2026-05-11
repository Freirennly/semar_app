<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar — SEMAR (Komisi Etik Penelitian)</title>
    <meta name="description" content="Daftar akun SEMAR — Sistem Manajemen Pengajuan & Validasi Penelitian">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-[#F5F5F5] min-h-screen relative overflow-hidden flex items-center justify-center p-6">
    <!-- Dekorasi Lingkaran Latar Belakang -->
    <div class="fixed -top-32 -left-32 w-80 h-80 rounded-full bg-[#E6E6FA] opacity-70"></div>
    <div class="fixed -bottom-48 -right-48 w-96 h-96 rounded-full bg-[#B0E0E6] opacity-50"></div>

    <div class="w-full max-w-5xl mx-auto">
        <!-- Kontainer Kartu Utama -->
        <main class="grid grid-cols-1 md:grid-cols-2 rounded-2xl shadow-xl bg-white overflow-hidden relative z-10">
            
            <!-- KOLOM KIRI (Area Form Register) -->
            <div class="p-8 md:p-12 flex flex-col justify-center relative">
                
                <!-- Logo Top Left -->
                <header class="absolute top-8 left-10 flex items-center hidden sm:flex">
                    <svg class="w-7 h-7 text-[#463EE3]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h1 class="text-xl font-bold text-[#463EE3] ml-2 tracking-tight">SEMAR</h1>
                </header>

                <div class="mt-8 mb-6">
                    <!-- Tab Navigasi Login & Sign Up -->
                    <div class="flex items-center space-x-8 border-b border-gray-100 mb-8 pt-6 sm:pt-0">
                        <!-- Link ke Halaman Login -->
                        <a href="{{ route('login') }}" class="text-2xl font-semibold text-gray-400 hover:text-[#463EE3] pb-2 transition-colors">Login</a>
                        <!-- Tab Aktif (Sign up) -->
                        <h2 class="text-2xl font-bold text-[#463EE3] border-b-2 border-[#463EE3] pb-2">Sign up</h2>
                    </div>

                    <!-- Pesan Error -->
                    @if($errors->any())
                    <div class="mb-5 bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-lg text-sm" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Form Register -->
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <!-- Input Nama -->
                        <div class="relative mb-4">
                            <label for="name" class="sr-only">Nama Lengkap</label>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                class="w-full rounded-full bg-[#F5F5F5] px-12 py-3 border-none text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#87CEEB] transition-shadow"
                                placeholder="Nama Lengkap">
                        </div>

                        <!-- Input Email -->
                        <div class="relative mb-4">
                            <label for="email" class="sr-only">Email</label>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full rounded-full bg-[#F5F5F5] px-12 py-3 border-none text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#87CEEB] transition-shadow"
                                placeholder="nama@semar.ac.id">
                        </div>

                        <!-- Input Password -->
                        <div class="relative mb-4">
                            <label for="password" class="sr-only">Password</label>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-full bg-[#F5F5F5] px-12 py-3 border-none text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#87CEEB] transition-shadow"
                                placeholder="Password (Min. 8 Karakter)">
                        </div>

                        <!-- Input Konfirmasi Password -->
                        <div class="relative mb-6">
                            <label for="password_confirmation" class="sr-only">Konfirmasi Password</label>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full rounded-full bg-[#F5F5F5] px-12 py-3 border-none text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#87CEEB] transition-shadow"
                                placeholder="Konfirmasi Password">
                        </div>

                        <!-- Aksi Form -->
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="w-full sm:w-auto rounded-full px-10 py-3 bg-[#463EE3] text-white font-semibold shadow-md hover:bg-opacity-90 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-[#E6E6FA] transition-all">
                                Buat Akun
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <p class="mt-auto pt-4 text-xs text-center text-gray-400">U&copy; {{ date('Y') }} Universitas Ultramen Surakarta. All rights reserved.</p>
            </div>

            <!-- KOLOM KANAN (Ilustrasi Register - ID Card Peneliti) -->
            <div class="hidden md:flex relative bg-gradient-to-br from-[#B0E0E6] to-[#87CEEB] items-center justify-center p-12">
                
                <!-- Background Ornaments -->
                <div class="absolute inset-0 overflow-hidden">
                    <svg class="absolute top-0 right-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0,0 Q50,100 100,0 V100 H0 Z" fill="#E6E6FA"/>
                    </svg>
                </div>

                <!-- Teks Info -->
                <div class="absolute top-10 right-10 text-right z-20">
                    <h3 class="text-[#463EE3] font-bold text-lg opacity-80">Registrasi Peneliti</h3>
                    <p class="text-[#463EE3] text-sm opacity-60">Komisi Etik Penelitian</p>
                </div>
                
                <!-- Ilustrasi Registrasi: ID Card & Tanda Plus -->
                <div class="relative z-10 w-full max-w-sm drop-shadow-2xl transform hover:scale-105 transition-transform duration-500">
                    <svg viewBox="0 0 500 500" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                        
                        <!-- Latar Belakang Lingkaran (Aksen) -->
                        <circle cx="250" cy="250" r="180" fill="#F5F5F5" fill-opacity="0.2"/>
                        <circle cx="250" cy="250" r="140" fill="#E6E6FA" fill-opacity="0.4"/>

                        <!-- Kartu ID Peneliti -->
                        <rect x="150" y="110" width="200" height="280" rx="20" fill="#F5F5F5" stroke="#463EE3" stroke-width="12"/>
                        
                        <!-- Lubang Lanyard -->
                        <rect x="225" y="125" width="50" height="15" rx="7.5" fill="#B0E0E6"/>

                        <!-- Foto Profil (Placeholder) -->
                        <circle cx="250" cy="205" r="45" fill="#87CEEB"/>
                        <!-- Siluet User di dalam Foto Profil -->
                        <path d="M250 180C261.046 180 270 188.954 270 200C270 211.046 261.046 220 250 220C238.954 220 230 211.046 230 200C230 188.954 238.954 180 250 180ZM210 245C210 230 225 220 250 220C275 220 290 230 290 245V250H210V245Z" fill="#463EE3"/>

                        <!-- Garis Teks Profil -->
                        <line x1="190" y1="280" x2="310" y2="280" stroke="#B0E0E6" stroke-width="10" stroke-linecap="round"/>
                        <line x1="210" y1="315" x2="290" y2="315" stroke="#B0E0E6" stroke-width="10" stroke-linecap="round"/>
                        
                        <!-- Badge Tambah Akun (Plus) -->
                        <circle cx="340" cy="340" r="40" fill="#463EE3"/>
                        <path d="M340 315V365M315 340H365" stroke="#F5F5F5" stroke-width="12" stroke-linecap="round"/>

                        <!-- Bintang/Sparkle Kecil (Aksen) -->
                        <path d="M120 180L125 195L140 200L125 205L120 220L115 205L100 200L115 195Z" fill="#F5F5F5"/>
                        <path d="M380 120L383 130L393 133L383 136L380 146L377 136L367 133L377 130Z" fill="#F5F5F5"/>
                    </svg>
                </div>
            </div>
            
        </main>
    </div>
</body>
</html>