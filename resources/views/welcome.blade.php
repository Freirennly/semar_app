<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SEMAR - Sistem Manajemen Etik Riset | Universitas Ultramen</title>
    <link rel="preload" href="{{ Vite::asset('resources/assets/static/PlusJakartaSans-Regular.ttf') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/assets/static/PlusJakartaSans-Bold.ttf') }}" as="font" type="font/ttf" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-bg text-text antialiased flex flex-col min-h-screen">

    {{-- HEADER --}}
    <header class="bg-white border-b border-border sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center gap-2">
                    <span class="text-xl font-bold text-primary tracking-tight">SEMAR</span>
                    <span class="hidden sm:inline-block w-px h-5 bg-border mx-2"></span>
                    <span class="hidden sm:inline-block text-sm font-medium text-text-secondary tracking-wide">Universitas Ultramen</span>
                </div>
                
                {{-- Navigation Desktop --}}
                <nav class="hidden md:flex space-x-8">
                    <a href="#" class="text-sm font-semibold text-text hover:text-primary transition-colors">Beranda</a>
                    <a href="#tentang" class="text-sm font-semibold text-text-secondary hover:text-primary transition-colors">Tentang SEMAR</a>
                    <a href="#layanan" class="text-sm font-semibold text-text-secondary hover:text-primary transition-colors">Layanan</a>
                    <a href="#alur" class="text-sm font-semibold text-text-secondary hover:text-primary transition-colors">Alur Pengajuan</a>
                    <a href="#panduan" class="text-sm font-semibold text-text-secondary hover:text-primary transition-colors">Panduan</a>
                </nav>

                {{-- Login Button --}}
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary py-2 px-4 text-sm whitespace-nowrap">Dasbor</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-primary hover:text-primary-hover px-4 py-2 border border-primary rounded-lg transition-colors whitespace-nowrap">Masuk ke Sistem</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="flex-grow">
        
        {{-- HERO SECTION --}}
        <section class="bg-white border-b border-border py-20 lg:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-text tracking-tight mb-6">
                    SEMAR<br>
                    <span class="text-primary text-2xl md:text-4xl lg:text-5xl mt-2 block">Sistem Manajemen Etik Riset</span>
                </h1>
                <p class="mt-4 max-w-3xl mx-auto text-lg text-text-secondary mb-10 leading-relaxed">
                    SEMAR merupakan platform yang memfasilitasi administrasi komite etik, mulai dari pendaftaran proposal, pemeriksaan kelengkapan dokumen, telaah etik oleh reviewer, hingga penerbitan Ethical Clearance.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('login') }}" class="btn-primary font-bold py-3 px-8 text-base">
                        Masuk ke Sistem
                    </a>
                    <a href="#panduan" class="bg-white hover:bg-soft-surface text-text font-bold py-3 px-8 rounded-lg transition-colors border border-border">
                        Dokumen Panduan
                    </a>
                </div>
            </div>
        </section>

        {{-- TENTANG SEMAR SECTION --}}
        <section id="tentang" class="py-20 bg-bg border-b border-border">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold text-text tracking-tight mb-6">Tentang SEMAR</h2>
                <p class="text-base text-text-secondary leading-relaxed mb-4">
                    Sistem Manajemen Etik Riset (SEMAR) Universitas Ultramen adalah aplikasi berbasis web yang dirancang untuk mendukung operasional Komite Etik Penelitian. Sistem ini melayani pengusul penelitian dalam mengajukan proposal yang memerlukan persetujuan etik, khususnya penelitian yang melibatkan subjek manusia atau hewan uji.
                </p>
                <p class="text-base text-text-secondary leading-relaxed">
                    Seluruh proses mulai dari penerimaan berkas, distribusi penugasan telaah, penjadwalan Sidang Fullboard, hingga penetapan putusan dan penerbitan Surat Kelayakan Etik (Ethical Clearance) dicatat secara terpusat. Hal ini bertujuan untuk menjaga tertib administrasi serta menjamin kepatuhan terhadap standar etika penelitian yang berlaku di lingkungan universitas.
                </p>
            </div>
        </section>

        {{-- LAYANAN SECTION --}}
        <section id="layanan" class="py-20 bg-white border-b border-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-text tracking-tight">Layanan</h2>
                    <p class="mt-4 text-text-secondary">Modul administrasi yang tersedia pada sistem.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    {{-- Layanan 1 --}}
                    <div class="bg-white p-6 border border-border rounded-xl">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-2 h-2 bg-text rounded-full"></span>
                            <h3 class="text-base font-bold text-text">Pengajuan Proposal</h3>
                        </div>
                        <p class="text-sm text-text-secondary">Pendaftaran usulan penelitian dan pengunggahan kelengkapan berkas oleh pengusul.</p>
                    </div>

                    {{-- Layanan 2 --}}
                    <div class="bg-white p-6 border border-border rounded-xl">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-2 h-2 bg-text rounded-full"></span>
                            <h3 class="text-base font-bold text-text">Verifikasi Dokumen</h3>
                        </div>
                        <p class="text-sm text-text-secondary">Pemeriksaan kelayakan syarat administrasi sebelum proposal ditelaah lebih lanjut.</p>
                    </div>

                    {{-- Layanan 3 --}}
                    <div class="bg-white p-6 border border-border rounded-xl">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-2 h-2 bg-text rounded-full"></span>
                            <h3 class="text-base font-bold text-text">Telaah Etik</h3>
                        </div>
                        <p class="text-sm text-text-secondary">Peninjauan aspek etika penelitian oleh reviewer yang ditunjuk komite etik.</p>
                    </div>

                    {{-- Layanan 4 --}}
                    <div class="bg-white p-6 border border-border rounded-xl">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-2 h-2 bg-text rounded-full"></span>
                            <h3 class="text-base font-bold text-text">Sidang Fullboard</h3>
                        </div>
                        <p class="text-sm text-text-secondary">Fasilitas penjadwalan dan pendataan keputusan hasil rapat komite etik.</p>
                    </div>

                    {{-- Layanan 5 --}}
                    <div class="bg-white p-6 border border-border rounded-xl">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-2 h-2 bg-text rounded-full"></span>
                            <h3 class="text-base font-bold text-text">Ethical Clearance</h3>
                        </div>
                        <p class="text-sm text-text-secondary">Pencetakan surat kelayakan etik secara sistem bagi usulan yang telah disetujui.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ALUR SECTION --}}
        <section id="alur" class="py-20 bg-bg border-b border-border">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-text tracking-tight">Alur Pengajuan</h2>
                    <p class="mt-4 text-text-secondary">Urutan proses administrasi yang berlaku.</p>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-white p-5 border border-border rounded-lg flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-text">Pengajuan Proposal</h4>
                            <p class="text-sm text-text-secondary mt-1">Pembuatan draf dan pengiriman usulan baru.</p>
                        </div>
                        <span class="text-xs font-medium text-text px-3 py-1 bg-soft-surface border border-border rounded">Mahasiswa</span>
                    </div>

                    <div class="bg-white p-5 border border-border rounded-lg flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-text">Verifikasi Administrasi</h4>
                            <p class="text-sm text-text-secondary mt-1">Pemeriksaan kelengkapan form dan dokumen lampiran.</p>
                        </div>
                        <span class="text-xs font-medium text-text px-3 py-1 bg-soft-surface border border-border rounded">Admin / Sekretariat</span>
                    </div>

                    <div class="bg-white p-5 border border-border rounded-lg flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-text">Telaah Etik</h4>
                            <p class="text-sm text-text-secondary mt-1">Penilaian substansi dan pengisian form telaah.</p>
                        </div>
                        <span class="text-xs font-medium text-text px-3 py-1 bg-soft-surface border border-border rounded">Reviewer</span>
                    </div>

                    <div class="bg-white p-5 border border-border rounded-lg flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-text">Keputusan Komite Etik</h4>
                            <p class="text-sm text-text-secondary mt-1">Penetapan putusan (Disetujui, Perbaikan, Ditolak, atau Fullboard).</p>
                        </div>
                        <span class="text-xs font-medium text-text px-3 py-1 bg-soft-surface border border-border rounded">Sekretariat</span>
                    </div>

                    <div class="bg-white p-5 border border-border rounded-lg flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-text">Sidang Fullboard (Tentatif)</h4>
                            <p class="text-sm text-text-secondary mt-1">Diskusi komite untuk usulan dengan risiko tinggi.</p>
                        </div>
                        <span class="text-xs font-medium text-text px-3 py-1 bg-soft-surface border border-border rounded">Komite Etik</span>
                    </div>

                    <div class="bg-white p-5 border border-border rounded-lg flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-text">Penerbitan Ethical Clearance</h4>
                            <p class="text-sm text-text-secondary mt-1">Pengesahan dan pemberian Surat Kelayakan Etik (Ethical Clearance).</p>
                        </div>
                        <span class="text-xs font-medium text-text px-3 py-1 bg-soft-surface border border-border rounded">Ketua</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- DOKUMEN DAN PANDUAN SECTION --}}
        <section id="panduan" class="py-20 bg-white border-b border-border">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-text tracking-tight">Dokumen & Panduan</h2>
                    <p class="mt-4 text-text-secondary">Daftar pedoman operasional baku terkait komite etik.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 border border-border rounded-xl bg-bg text-center">
                        <svg class="w-8 h-8 text-text mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        <h3 class="text-base font-bold text-text mb-2">SOP Pengajuan Proposal</h3>
                        <p class="text-xs text-text-muted mt-4">Belum tersedia.</p>
                    </div>

                    <div class="p-6 border border-border rounded-xl bg-bg text-center">
                        <svg class="w-8 h-8 text-text mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <h3 class="text-base font-bold text-text mb-2">Panduan Pengguna</h3>
                        <p class="text-xs text-text-muted mt-4">Belum tersedia.</p>
                    </div>

                    <div class="p-6 border border-border rounded-xl bg-bg text-center">
                        <svg class="w-8 h-8 text-text mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <h3 class="text-base font-bold text-text mb-2">Pedoman Telaah Etik</h3>
                        <p class="text-xs text-text-muted mt-4">Belum tersedia.</p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    {{-- FOOTER --}}
    <footer class="bg-surface border-t border-border mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <span class="text-xl font-bold text-text tracking-tight">SEMAR</span>
                    <p class="mt-4 text-sm text-text-secondary max-w-sm leading-relaxed">
                        Sistem Manajemen Etik Riset Komite Etik Penelitian Universitas Ultramen.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-text uppercase tracking-wider mb-4">Akses</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-text-secondary hover:text-primary transition-colors">Beranda</a></li>
                        <li><a href="{{ route('login') }}" class="text-sm text-text-secondary hover:text-primary transition-colors">Masuk ke Sistem</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-text uppercase tracking-wider mb-4">Kontak</h3>
                    <ul class="space-y-2 text-sm text-text-secondary">
                        <li>Komite Etik Penelitian</li>
                        <li>Universitas Ultramen</li>
                        <li class="mt-2">Email: kep@ultramen.ac.id</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-border flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-text-muted">
                    &copy; {{ date('Y') }} SEMAR - Universitas Ultramen.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
