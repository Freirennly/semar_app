<x-layouts.landing>
    <!-- Add custom animation for the hero illustration -->
    <style>
        @keyframes subtle-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .animate-subtle-float {
            animation: subtle-float 7s ease-in-out infinite;
        }
    </style>

    <!-- HERO SECTION -->
    <section id="beranda" class="py-24 lg:py-32 bg-white border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
                <!-- Text Content -->
                <div class="text-center lg:text-left lg:w-1/2">
                    <div class="mb-4">
                        <span class="text-sm font-bold text-text-secondary tracking-widest uppercase block">Komite Etik Penelitian</span>
                        <span class="text-sm font-bold text-text-secondary tracking-widest uppercase block">Universitas Ultramen</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-text tracking-tight mb-6 leading-tight">
                        SEMAR<br>
                        <span class="text-primary text-2xl md:text-4xl lg:text-5xl mt-2 block">Sistem Manajemen Etik Riset</span>
                    </h1>
                    <p class="mt-4 text-lg text-text-secondary leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        SEMAR merupakan sistem informasi yang digunakan oleh Komite Etik Penelitian Universitas Ultramen untuk mengelola proses pengajuan proposal penelitian, verifikasi administrasi, telaah etik, penjadwalan Sidang Fullboard, hingga penerbitan Surat Kelayakan Etik (Ethical Clearance).
                    </p>
                </div>

                <!-- Visual Asset -->
                <div class="hidden lg:flex lg:w-1/2 justify-center">
                    <div class="w-[420px] h-[420px] flex items-center justify-center animate-subtle-float">
                        <!-- Professional Flat Illustration: Document Verification & Ethical Review -->
                        <!-- Credit: Styled based on open-source unDraw / ManyPixels aesthetic -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 700 700" class="w-full h-full">
                            <defs>
                                <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%">
                                    <feDropShadow dx="0" dy="15" stdDeviation="20" flood-color="#000" flood-opacity="0.04"/>
                                </filter>
                                <filter id="shadow-sm" x="-10%" y="-10%" width="120%" height="120%">
                                    <feDropShadow dx="0" dy="4" stdDeviation="8" flood-color="#000" flood-opacity="0.06"/>
                                </filter>
                                <linearGradient id="primary-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="var(--color-primary)" stop-opacity="0.9"/>
                                    <stop offset="100%" stop-color="var(--color-primary)"/>
                                </linearGradient>
                            </defs>
                            
                            <!-- Background Abstract Elements (Depth) -->
                            <circle cx="350" cy="350" r="280" fill="#f3f4f6" opacity="0.4" />
                            <path d="M120,400 Q180,480 150,550" fill="none" stroke="#e5e7eb" stroke-width="4" stroke-dasharray="8 8" />
                            <circle cx="580" cy="180" r="12" fill="var(--color-primary)" opacity="0.2" />
                            <circle cx="150" cy="220" r="8" fill="#9ca3af" opacity="0.3" />
                            <path d="M600,450 L620,470 M620,450 L600,470" stroke="var(--color-primary)" stroke-width="4" opacity="0.3" stroke-linecap="round"/>

                            <!-- Main Clipboard / Document Board -->
                            <rect x="230" y="140" width="280" height="380" rx="20" fill="#ffffff" filter="url(#shadow)" />
                            
                            <!-- Document Clip -->
                            <path d="M310,120 L430,120 A10,10 0 0 1 440,130 L440,150 A10,10 0 0 1 430,160 L310,160 A10,10 0 0 1 300,150 L300,130 A10,10 0 0 1 310,120 Z" fill="#e5e7eb" />
                            <rect x="340" y="110" width="60" height="20" rx="10" fill="#d1d5db" />

                            <!-- Inner Document Paper -->
                            <rect x="250" y="180" width="240" height="320" rx="8" fill="#f9fafb" />
                            
                            <!-- Document Lines (Text) -->
                            <rect x="280" y="210" width="180" height="12" rx="6" fill="#e5e7eb" />
                            <rect x="280" y="240" width="140" height="12" rx="6" fill="#e5e7eb" />
                            <rect x="280" y="270" width="160" height="12" rx="6" fill="#e5e7eb" />
                            <rect x="280" y="300" width="100" height="12" rx="6" fill="#e5e7eb" />

                            <!-- Floating Verification Badge (Foreground) -->
                            <g transform="translate(420, 360)" filter="url(#shadow-sm)">
                                <!-- Shield Shape -->
                                <path d="M50,0 C50,0 90,20 90,60 C90,110 50,140 50,140 C50,140 10,110 10,60 C10,20 50,0 50,0 Z" fill="url(#primary-grad)" />
                                <!-- Checkmark -->
                                <path d="M30,70 L45,85 L70,50" fill="none" stroke="#ffffff" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" />
                            </g>

                            <!-- Progress/Chart Element (Left overlay) -->
                            <g transform="translate(140, 320)" filter="url(#shadow-sm)">
                                <rect x="0" y="0" width="140" height="100" rx="16" fill="#ffffff" />
                                <circle cx="40" cy="50" r="24" fill="none" stroke="#e5e7eb" stroke-width="8" />
                                <path d="M40,26 A24,24 0 0 1 64,50" fill="none" stroke="var(--color-primary)" stroke-width="8" stroke-linecap="round" />
                                <rect x="80" y="35" width="40" height="8" rx="4" fill="#e5e7eb" />
                                <rect x="80" y="55" width="25" height="8" rx="4" fill="#d1d5db" />
                            </g>
                            
                            <!-- Ethical Review Signature/Stamp -->
                            <circle cx="380" cy="420" r="30" fill="var(--color-primary)" opacity="0.1" />
                            <path d="M365,420 L375,430 L395,410" fill="none" stroke="var(--color-primary)" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TENTANG SEMAR -->
    <section id="tentang" class="py-20 bg-white border-b border-border">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-text tracking-tight mb-6">Tentang SEMAR</h2>
            <p class="text-base text-text-secondary leading-relaxed mb-4">
                Sistem Manajemen Etik Riset (SEMAR) Universitas Ultramen adalah aplikasi berbasis web yang dioperasikan untuk mendukung tata kelola administrasi Komite Etik Penelitian. Sistem ini diperuntukkan bagi pengusul penelitian yang memerlukan peninjauan dan persetujuan etik, khususnya riset yang melibatkan subjek manusia atau hewan.
            </p>
            <p class="text-base text-text-secondary leading-relaxed">
                Seluruh aktivitas mulai dari pendaftaran berkas usulan, penugasan telaah, penjadwalan Sidang Fullboard, hingga penetapan putusan dan pencetakan Surat Kelayakan Etik (Ethical Clearance) direkam secara terpusat untuk menjamin transparansi, ketertiban administrasi, dan kepatuhan terhadap standar etika akademik universitas.
            </p>
        </div>
    </section>

    <!-- LIVE DATA -->
    <section class="py-20 bg-white border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Stat 1 -->
                <div class="p-6 border border-border rounded-xl bg-white text-center">
                    <p class="text-sm font-bold text-text-secondary uppercase tracking-widest mb-3">Proposal Masuk</p>
                    <span class="text-5xl font-bold text-text tracking-tight">{{ number_format($totalProtokol ?? 0) }}</span>
                </div>
                <!-- Stat 2 -->
                <div class="p-6 border border-border rounded-xl bg-white text-center">
                    <p class="text-sm font-bold text-text-secondary uppercase tracking-widest mb-3">Reviewer Aktif</p>
                    <span class="text-5xl font-bold text-text tracking-tight">{{ number_format($totalReviewer ?? 0) }}</span>
                </div>
                <!-- Stat 3 -->
                <div class="p-6 border border-border rounded-xl bg-white text-center">
                    <p class="text-sm font-bold text-text-secondary uppercase tracking-widest mb-3">Hari Kerja</p>
                    <div class="flex items-baseline justify-center gap-2">
                        <span class="text-5xl font-bold text-text tracking-tight">{{ $avgWorkDays ?? 14 }}</span>
                        <span class="text-base font-medium text-text-secondary">hari</span>
                    </div>
                </div>
                <!-- Stat 4 -->
                <div class="p-6 border border-border rounded-xl bg-white text-center">
                    <p class="text-sm font-bold text-text-secondary uppercase tracking-widest mb-3">Tingkat Keakuratan</p>
                    <div class="flex items-baseline justify-center gap-2">
                        <span class="text-5xl font-bold text-text tracking-tight">{{ $accuracyRate ?? 98 }}</span>
                        <span class="text-2xl font-medium text-text-secondary">%</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JALUR TELAAH ETIK -->
    <section id="prosedur" class="py-20 bg-white border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-text tracking-tight mb-4">Klasifikasi Telaah Etik</h2>
                <p class="text-text-secondary text-base max-w-2xl mx-auto">
                    Prosedur evaluasi ditentukan berdasarkan kategori risiko dari usulan penelitian yang diajukan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Exempted -->
                <div class="p-8 border border-border rounded-xl bg-white flex flex-col items-start">
                    <span class="px-3 py-1 border border-border text-text text-xs font-bold uppercase tracking-wider mb-4 rounded">Risiko Minimal</span>
                    <h3 class="text-2xl font-bold text-text mb-4">Exempted</h3>
                    <p class="text-sm text-text-secondary leading-relaxed">
                        Kategori untuk penelitian tanpa risiko klinis, yang secara umum hanya memanfaatkan data sekunder tanpa identitas, kajian pustaka, atau kuesioner pada publik secara umum tanpa pengumpulan data privasi.
                    </p>
                </div>

                <!-- Expedited -->
                <div class="p-8 border border-border rounded-xl bg-white flex flex-col items-start">
                    <span class="px-3 py-1 border border-border text-text text-xs font-bold uppercase tracking-wider mb-4 rounded">Risiko Rendah</span>
                    <h3 class="text-2xl font-bold text-text mb-4">Expedited</h3>
                    <p class="text-sm text-text-secondary leading-relaxed">
                        Penelitian yang melibatkan subjek manusia tetapi tidak mencakup prosedur invasif, tidak melibatkan partisipan rentan, dan kerahasiaan identitas dikelola dengan metode pengamanan yang terukur.
                    </p>
                </div>

                <!-- Fullboard -->
                <div class="p-8 border border-border rounded-xl bg-white flex flex-col items-start">
                    <span class="px-3 py-1 border border-border text-text text-xs font-bold uppercase tracking-wider mb-4 rounded">Risiko Tinggi</span>
                    <h3 class="text-2xl font-bold text-text mb-4">Fullboard</h3>
                    <p class="text-sm text-text-secondary leading-relaxed">
                        Usulan penelitian yang mengandung intervensi medis, prosedur invasif, atau melibatkan kelompok rentan sehingga mewajibkan penilaian substansi melalui rapat Sidang Fullboard komite etik.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- DOKUMEN DAN PANDUAN -->
    <section id="panduan" class="py-20 bg-white border-b border-border">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-text tracking-tight mb-4">Dokumen & Panduan</h2>
            <p class="text-lg text-text-secondary mb-8 leading-relaxed">
                Akses kumpulan regulasi, standar operasional prosedur, panduan pengguna, dan format dokumen pendukung (template) untuk menunjang kelancaran proses pengajuan etik penelitian Anda.
            </p>
            <a href="{{ route('sop') }}" class="btn-outline py-3 px-8 text-base inline-flex items-center gap-2">
                Pusat Dokumentasi
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </section>
</x-layouts.landing>
