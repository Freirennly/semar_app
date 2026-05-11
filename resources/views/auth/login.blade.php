<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SEMAR (Komisi Etik Penelitian)</title>
    <meta name="description" content="Masuk ke SEMAR — Sistem Manajemen Pengajuan & Validasi Penelitian">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-[#F5F5F5] min-h-screen relative overflow-hidden flex items-center justify-center p-6">
    <!-- Dekorasi Lingkaran Latar Belakang (Menggunakan Pale Lilac dan Powder Blue) -->
    <div class="fixed -top-32 -left-32 w-80 h-80 rounded-full bg-[#E6E6FA] opacity-70"></div>
    <div class="fixed -bottom-48 -right-48 w-96 h-96 rounded-full bg-[#B0E0E6] opacity-50"></div>

    <div class="w-full max-w-5xl mx-auto">
        <!-- Kontainer Kartu Utama -->
        <main class="grid grid-cols-1 md:grid-cols-2 rounded-2xl shadow-xl bg-white overflow-hidden relative z-10">
            
            <!-- KOLOM KIRI (Area Form) -->
            <div class="p-10 md:p-14 flex flex-col justify-center relative">
                
                <!-- Logo Top Left -->
                <header class="absolute top-8 left-10 flex items-center">
                    <svg class="w-7 h-7 text-[#463EE3]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h1 class="text-xl font-bold text-[#463EE3] ml-2 tracking-tight">SEMAR</h1>
                </header>

                <div class="mt-12 mb-8">
                    <!-- Tab Navigasi Login & Sign Up -->
                    <div class="flex items-center space-x-8 border-b border-gray-100 mb-8">
                        <h2 class="text-2xl font-bold text-[#463EE3] border-b-2 border-[#463EE3] pb-2">Login</h2>
                        <!-- Link ke Halaman Register -->
                        <a href="{{ route('register') }}" class="text-2xl font-semibold text-gray-400 hover:text-[#463EE3] pb-2 transition-colors">Sign up</a>
                    </div>

                    <!-- Pesan Error -->
                    @if($errors->any())
                    <div class="mb-5 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg text-sm" role="alert">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    <!-- Form Login -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Input Email/Telepon -->
                        <div class="relative mb-5">
                            <label for="email" class="sr-only">Email</label>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                class="w-full rounded-full bg-[#F5F5F5] px-12 py-3.5 border-none text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#87CEEB] transition-shadow"
                                placeholder="nama@semar.ac.id">
                        </div>

                        <!-- Input Password -->
                        <div class="relative mb-5">
                            <label for="password" class="sr-only">Password</label>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-full bg-[#F5F5F5] px-12 py-3.5 border-none text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#87CEEB] transition-shadow"
                                placeholder="••••••••">
                        </div>

                        <!-- Aksi Form -->
                        <div class="flex items-center justify-between mt-8">
                            <button type="submit" class="rounded-full px-10 py-3 bg-[#463EE3] text-white font-semibold shadow-md hover:bg-opacity-90 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-[#E6E6FA] transition-all">
                                Login
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <p class="mt-auto pt-8 text-xs text-center text-gray-400">&copy; {{ date('Y') }} Universitas Ultramen Surakarta. All rights reserved.</p>
            </div>

            <!-- KOLOM KANAN (Ilustrasi KEP - Komisi Etik Penelitian) -->
            <div class="hidden md:flex relative bg-gradient-to-br from-[#B0E0E6] to-[#87CEEB] items-center justify-center p-12">
                
                <!-- Background Ornaments -->
                <div class="absolute inset-0 overflow-hidden">
                    <svg class="absolute top-0 right-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0,0 Q50,100 100,0 V100 H0 Z" fill="#E6E6FA"/>
                    </svg>
                </div>

                <!-- Teks Info -->
                <div class="absolute top-10 right-10 text-right z-20">
                    <h3 class="text-[#463EE3] font-bold text-lg opacity-80">Komisi Etik Penelitian</h3>
                    <p class="text-[#463EE3] text-sm opacity-60">Portal Pengajuan & Validasi</p>
                </div>
                
                <!-- Ilustrasi KEP: Dokumen & Perisai Validasi -->
                <div class="relative z-10 w-full max-w-sm drop-shadow-2xl transform hover:scale-105 transition-transform duration-500">
                    <svg viewBox="0 0 500 500" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                        
                        <!-- Latar Belakang Lingkaran (Aksen) -->
                        <circle cx="250" cy="250" r="180" fill="#F5F5F5" fill-opacity="0.2"/>
                        <circle cx="250" cy="250" r="140" fill="#E6E6FA" fill-opacity="0.4"/>

                        <!-- Papan Jalan (Clipboard) -->
                        <rect x="140" y="100" width="200" height="280" rx="15" fill="#F5F5F5" stroke="#463EE3" stroke-width="12"/>
                        
                        <!-- Klip Dokumen -->
                        <rect x="210" y="80" width="60" height="30" rx="8" fill="#87CEEB" stroke="#463EE3" stroke-width="12"/>
                        <path d="M225 80V65C225 60 230 55 235 55H245C250 55 255 60 255 65V80" stroke="#463EE3" stroke-width="10" stroke-linecap="round"/>

                        <!-- Garis Teks pada Dokumen -->
                        <line x1="180" y1="160" x2="300" y2="160" stroke="#B0E0E6" stroke-width="10" stroke-linecap="round"/>
                        <line x1="180" y1="200" x2="280" y2="200" stroke="#B0E0E6" stroke-width="10" stroke-linecap="round"/>
                        <line x1="180" y1="240" x2="260" y2="240" stroke="#B0E0E6" stroke-width="10" stroke-linecap="round"/>

                        <!-- Perisai (Shield) - Melambangkan Etika & Perlindungan -->
                        <path d="M270 200L360 230V290C360 340 320 380 270 410C220 380 180 340 180 290V230L270 200Z" fill="#463EE3"/>
                        <path d="M270 215L345 240V290C345 330 310 365 270 390V215Z" fill="#3B34BA"/> <!-- Bayangan Perisai -->

                        <!-- Tanda Centang (Validasi) di dalam Perisai -->
                        <path d="M230 300L260 330L310 270" stroke="#87CEEB" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                        
                        <!-- Bintang/Sparkle Kecil (Aksen) -->
                        <path d="M380 150L385 165L400 170L385 175L380 190L375 175L360 170L375 165L380 150Z" fill="#F5F5F5"/>
                        <path d="M120 280L123 290L133 293L123 296L120 306L117 296L107 293L117 290L120 280Z" fill="#F5F5F5"/>
                    </svg>
                </div>
            </div>
            
        </main>
    </div>
</body>
</html>