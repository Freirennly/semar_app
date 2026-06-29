<x-layouts.landing>
    <!-- HERO SECTION -->
    <section id="beranda" class="relative pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden bg-bg">
        <!-- Geometric Pattern Soft Background -->
        <div class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(var(--color-primary) 1px, transparent 1px); background-size: 32px 32px;"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                <!-- Text Content -->
                <div class="max-w-2xl">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-text tracking-tight mb-6 leading-tight">
                        Manajemen<br>
                        <span class="text-primary">Riset & Etika</span>
                    </h1>
                    <p class="text-lg text-text-secondary mb-8 leading-relaxed max-w-lg">
                        Automasi pengajuan protokol penelitian dan pemantauan klirens etik secara real-time melalui platform terpadu SEMAR.
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('login') }}" class="btn-primary py-3 px-6 text-base shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 transform hover:-translate-y-0.5 transition-all duration-300">
                            Ajukan Protokol
                        </a>
                        <a href="#tentang" class="btn-ghost py-3 px-6 text-base group">
                            Pelajari Sistem 
                            <span class="inline-block transition-transform duration-300 group-hover:translate-x-1 ml-1">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Visual / Illustration -->
                <div class="relative lg:ml-10">
                    <!-- Main Dashboard Snippet -->
                    <div class="relative rounded-2xl bg-surface border border-border shadow-2xl p-2 z-10 transform lg:rotate-[2deg] hover:rotate-0 transition-transform duration-500">
                        <div class="rounded-xl overflow-hidden border border-border bg-bg">
                            <!-- Fake Window Header -->
                            <div class="bg-surface border-b border-border px-4 py-3 flex items-center gap-2">
                                <div class="flex gap-1.5">
                                    <div class="w-3 h-3 rounded-full bg-danger/80"></div>
                                    <div class="w-3 h-3 rounded-full bg-warning/80"></div>
                                    <div class="w-3 h-3 rounded-full bg-success/80"></div>
                                </div>
                                <div class="mx-auto w-32 h-2 rounded bg-border"></div>
                            </div>
                            <!-- Fake Content -->
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="w-1/3 h-5 rounded bg-border"></div>
                                    <div class="w-1/4 h-6 rounded-full bg-primary/10"></div>
                                </div>
                                <div class="space-y-4 mb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded bg-soft-surface shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="w-full h-2 rounded bg-border"></div>
                                            <div class="w-2/3 h-2 rounded bg-border"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded bg-soft-surface shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="w-full h-2 rounded bg-border"></div>
                                            <div class="w-4/5 h-2 rounded bg-border"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="h-24 rounded-lg bg-soft-surface border border-primary/20"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Card 1 -->
                    <div class="absolute -bottom-6 -left-6 z-20 bg-surface rounded-xl p-4 shadow-xl border border-border animate-[bounce_5s_infinite] flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-success-bg text-success flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-text-secondary font-medium">Status Dokumen</p>
                            <p class="text-sm font-bold text-text">Telah Disetujui</p>
                        </div>
                    </div>

                    <!-- Floating Card 2 -->
                    <div class="absolute -top-8 -right-8 z-0 bg-surface rounded-xl p-4 shadow-lg border border-border animate-[bounce_6s_infinite_0.5s] flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-info-soft text-primary flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-text-secondary font-medium">Real-time Review</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 1: Live Sync Data Dashboard -->
    <section id="tentang" class="py-24 bg-primary relative overflow-hidden">
        <!-- Soft abstract background pattern -->
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-white/5 rounded-full blur-[100px] transform translate-x-1/3 -translate-y-1/3 pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div class="max-w-2xl">
                    <div class="text-white text-xs font-semibold mb-4">
                        Sistem Pemantauan Terpadu
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white tracking-tight mb-4">Live Sync Data</h2>
                    <p class="text-lg text-white/80 font-light">
                        Pantau status rekam akademik kampus secara real-time. Sistem SEMAR terhubung langsung dengan basis data terpusat.
                    </p>
                </div>
            </div>

            <!-- Dashboard Grid Container -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 md:p-12 shadow-2xl relative overflow-hidden">
                <!-- Subtle inner glow -->
                <div class="absolute -top-24 -left-24 w-48 h-48 bg-[#87CEEB]/20 rounded-full blur-3xl"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 md:gap-0 divide-y md:divide-y-0 md:divide-x divide-white/10 relative z-10">
                    
                    <!-- Stat 1: Protokol Masuk -->
                    <div class="flex flex-col md:pr-10">
                        <p class="text-sm font-medium text-white/60 uppercase tracking-widest mb-3">Protokol Masuk</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-6xl font-bold text-white tracking-tight">{{ number_format($totalProtokol ?? 0) }}</span>
                        </div>
                        <p class="mt-4 text-sm text-white/60 leading-relaxed border-t border-white/10 pt-4">Total pengajuan dokumen riset dalam sistem</p>
                    </div>

                    <!-- Stat 2: Reviewer Tervalidasi -->
                    <div class="flex flex-col md:px-10 pt-10 md:pt-0">
                        <p class="text-sm font-medium text-white/60 uppercase tracking-widest mb-3">Reviewer Tervalidasi</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-6xl font-bold text-[#87CEEB] tracking-tight">{{ number_format($totalReviewer ?? 0) }}</span>
                        </div>
                        <p class="mt-4 text-sm text-white/60 leading-relaxed border-t border-white/10 pt-4">Pakar aktif dalam telaah komite etik</p>
                    </div>

                    <!-- Stat 3: Hari Kerja -->
                    <div class="flex flex-col md:px-10 pt-10 md:pt-0">
                        <p class="text-sm font-medium text-white/60 uppercase tracking-widest mb-3">Hari Kerja Rata-rata</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-6xl font-bold text-white tracking-tight">{{ $avgWorkDays ?? 14 }}</span>
                            <span class="text-xl font-medium text-white/50">hari</span>
                        </div>
                        <p class="mt-4 text-sm text-white/60 leading-relaxed border-t border-white/10 pt-4">Estimasi standar waktu (SLA) penerbitan</p>
                    </div>

                    <!-- Stat 4: Keakuratan -->
                    <div class="flex flex-col md:pl-10 pt-10 md:pt-0">
                        <p class="text-sm font-medium text-white/60 uppercase tracking-widest mb-3">Tingkat Keakuratan</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-6xl font-bold text-[#87CEEB] tracking-tight">{{ $accuracyRate ?? 98 }}</span>
                            <span class="text-4xl font-light text-[#87CEEB]/80">%</span>
                        </div>
                        <p class="mt-4 text-sm text-white/60 leading-relaxed border-t border-white/10 pt-4">Keselarasan validasi dan putusan telaah</p>
                    </div>

                </div>
            </div>
            
            <div class="mt-6 flex justify-between items-center text-xs text-white/40">
                <p>Protected by Enterprise Security</p>
                <p class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Real-time sync active
                </p>
            </div>
        </div>
    </section>

    <!-- SECTION 2: Alur Kerja Digital -->
    <section class="py-20 bg-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-primary font-semibold tracking-wider uppercase text-sm mb-2">Efisiensi Tanpa Batas</h2>
                    <h3 class="text-3xl md:text-4xl font-bold text-text tracking-tight">Alur Kerja Digital</h3>
                    <p class="text-text-secondary mt-4 text-lg">
                        Sistem SEMAR memangkas waktu birokrasi melalui integrasi data yang aman dan terpusat.
                    </p>
                </div>
            </div>

            <!-- Workflow Timeline -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <!-- Connector Line (Desktop Only) -->
                <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-0.5 bg-border z-0"></div>

                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-surface border-2 border-border flex items-center justify-center mb-6 group-hover:border-primary group-hover:text-primary transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7 text-text-secondary group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-text mb-2">1. Submit</h4>
                    <p class="text-sm text-text-secondary leading-relaxed px-2">
                        Pengunggahan dokumen secara digital melalui akun SSO dengan format terstandar.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-surface border-2 border-border flex items-center justify-center mb-6 group-hover:border-primary group-hover:text-primary transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7 text-text-secondary group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-text mb-2">2. Verify</h4>
                    <p class="text-sm text-text-secondary leading-relaxed px-2">
                        Validasi administratif kelengkapan berkas oleh sekretariat KEP secara cepat.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-surface border-2 border-border flex items-center justify-center mb-6 group-hover:border-primary group-hover:text-primary transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7 text-text-secondary group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-text mb-2">3. Review</h4>
                    <p class="text-sm text-text-secondary leading-relaxed px-2">
                        Penelaahan mendalam oleh pakar dan reviewer sesuai bidang riset terkait.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-surface border-2 border-border flex items-center justify-center mb-6 group-hover:border-primary group-hover:text-primary transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7 text-text-secondary group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-text mb-2">4. Result</h4>
                    <p class="text-sm text-text-secondary leading-relaxed px-2">
                        Penerbitan klirens etik digital yang valid dengan QR verifikasi terintegrasi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: Jalur Telaah Etik -->
    <section id="prosedur" class="py-20 bg-surface border-y border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-16 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-bold text-text tracking-tight mb-4">Jalur Telaah Etik</h2>
                    <p class="text-text-secondary text-lg">
                        Setiap riset memiliki kategori risiko berbeda yang menentukan prosedur evaluasi dan durasi penelaahan di sistem SEMAR.
                    </p>
                </div>
                <div>
                    <a href="#" class="btn-outline inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh Panduan Kategori
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Exempted -->
                <div class="card overflow-hidden hover:shadow-xl hover:border-success/30 transition-all duration-300 group">
                    <div class="h-2 bg-success"></div>
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-2xl font-bold text-text">Exempted</h3>
                            <span class="text-5xl font-black text-success/10 group-hover:text-success/20 transition-colors">E</span>
                        </div>
                        <div class="inline-block px-3 py-1 rounded bg-success-bg text-success text-xs font-bold uppercase tracking-wider mb-4">Risiko Minimal</div>
                        <p class="text-text-secondary leading-relaxed">
                            Penelitian tanpa risiko bermakna, umumnya menggunakan data sekunder anonim, studi kepustakaan, atau kuesioner publik umum.
                        </p>
                    </div>
                </div>

                <!-- Expedited -->
                <div class="card overflow-hidden hover:shadow-xl hover:border-warning/30 transition-all duration-300 group">
                    <div class="h-2 bg-warning"></div>
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-2xl font-bold text-text">Expedited</h3>
                            <span class="text-5xl font-black text-warning/10 group-hover:text-warning/20 transition-colors">X</span>
                        </div>
                        <div class="inline-block px-3 py-1 rounded bg-warning-bg text-warning text-xs font-bold uppercase tracking-wider mb-4">Risiko Rendah</div>
                        <p class="text-text-secondary leading-relaxed">
                            Melibatkan subjek manusia tanpa prosedur invasif, tidak melibatkan kelompok subjek rentan, dan data privasi dijaga ketat.
                        </p>
                    </div>
                </div>

                <!-- Fullboard -->
                <div class="card overflow-hidden hover:shadow-xl hover:border-danger/30 transition-all duration-300 group">
                    <div class="h-2 bg-danger"></div>
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-2xl font-bold text-text">Fullboard</h3>
                            <span class="text-5xl font-black text-danger/10 group-hover:text-danger/20 transition-colors">F</span>
                        </div>
                        <div class="inline-block px-3 py-1 rounded bg-danger-bg text-danger text-xs font-bold uppercase tracking-wider mb-4">Risiko Tinggi</div>
                        <p class="text-text-secondary leading-relaxed">
                            Melibatkan intervensi klinis, prosedur invasif, atau melibatkan subjek rentan (anak, ibu hamil) yang membutuhkan rapat pleno.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </section>
</x-layouts.landing>
