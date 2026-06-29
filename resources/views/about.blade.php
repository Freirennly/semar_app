<x-layouts.landing>
<section class="relative min-h-screen flex items-center pt-20 pb-16 overflow-hidden bg-white">

    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-[700px] h-[700px] rounded-full"
             style="background: radial-gradient(circle, rgba(230,230,250,0.8) 0%, transparent 70%);
                    transform: translate(30%, -20%);">
        </div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] rounded-full"
             style="background: radial-gradient(circle, rgba(176,224,230,0.5) 0%, transparent 70%);
                    transform: translate(-30%, 20%);">
        </div>
        {{-- Subtle grid pattern --}}
        <div class="absolute inset-0 opacity-30"
             style="background-image: linear-gradient(rgba(70,62,227,0.05) 1px, transparent 1px),
                                      linear-gradient(90deg, rgba(70,62,227,0.05) 1px, transparent 1px);
                    background-size: 48px 48px;">
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">

            {{-- ── Text Content ─────────────────────────────── --}}
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-3 mb-8">
                    <div class="h-[2px] w-8" style="background:#463EE3"></div>
                    <span class="text-sm font-bold tracking-widest uppercase" style="color:#463EE3">
                        SEMAR Academic Platform
                    </span>
                </div>

                <h1 class="font-bold tracking-tight mb-6 leading-[1.08]"
                    style="font-size: clamp(3rem, 6vw, 5rem); color:#0F0E2E;">
                    Tentang<br>
                    <span style="color:#463EE3">SEMAR</span>
                </h1>

                <p class="text-xl leading-relaxed font-light mb-10 max-w-xl" style="color:#5A587A;">
                    Portal Manajemen Riset &amp; Etika Terintegrasi Kampus — sistem digital yang mempercepat proses review etika penelitian secara transparan dan terstruktur.
                </p>

            </div>

            {{-- ── Hero Visual Card ──────────────────────────── --}}
            <div class="relative flex items-center justify-center" style="min-height:480px;">

                {{-- Depth shadow cards (behind main card) --}}
                <div class="absolute w-full max-w-[380px] h-full rounded-2xl border"
                     style="background:rgba(230,230,250,0.5); border-color:rgba(70,62,227,0.12);
                            transform: rotate(-2deg) translate(-10px,-10px); z-index:0;">
                </div>
                <div class="absolute w-full max-w-[380px] h-full rounded-2xl border"
                     style="background:rgba(255,255,255,0.6); border-color:rgba(70,62,227,0.08);
                            transform: rotate(3deg) translate(16px,16px); z-index:1;
                            box-shadow: 0 8px 32px rgba(70,62,227,0.06);">
                </div>

                {{-- Main card --}}
                <div class="relative w-full max-w-[380px] rounded-2xl border bg-white z-10 p-7"
                     style="border-color:rgba(70,62,227,0.12);
                            box-shadow: 0 20px 60px rgba(70,62,227,0.10), 0 4px 16px rgba(0,0,0,0.04);
                            animation: semar-float 6s ease-in-out infinite;">

                    {{-- Card header --}}
                    <div class="flex justify-between items-start pb-5 mb-5"
                         style="border-bottom:1px solid rgba(70,62,227,0.08);">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background:#E6E6FA;">
                                <svg class="w-5 h-5" fill="none" stroke="#463EE3" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest mb-0.5" style="color:#463EE3">ID: PRT-2026-089</p>
                                <h4 class="text-sm font-bold" style="color:#0F0E2E">Protokol Uji Klinis</h4>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold tracking-widest uppercase px-2.5 py-1 rounded-md mt-0.5"
                              style="background:#E6E6FA; color:#463EE3; border:1px solid rgba(70,62,227,0.2);">
                            Fullboard
                        </span>
                    </div>

                    {{-- Step 1: Done --}}
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 z-10"
                                 style="background:#22C55E; box-shadow:0 2px 8px rgba(34,197,94,0.3);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="w-[2px] my-1 rounded-full flex-1" style="background:rgba(34,197,94,0.25); min-height:28px;"></div>
                        </div>
                        <div class="pt-0.5 pb-3">
                            <p class="text-sm font-bold" style="color:#0F0E2E">Verifikasi Dokumen</p>
                            <p class="text-xs mt-0.5 font-light" style="color:#8E8CAD">Selesai oleh Sekretariat KEP</p>
                        </div>
                    </div>

                    {{-- Step 2: Active --}}
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 z-10 relative"
                                 style="background:white; border:2px solid #463EE3;">
                                <div class="w-2.5 h-2.5 rounded-full" style="background:#463EE3; animation: semar-pulse 2s ease-in-out infinite;"></div>
                                <div class="absolute inset-0 rounded-full" style="border:1px solid rgba(70,62,227,0.35); animation: semar-ripple 2.5s cubic-bezier(0,0,0.2,1) infinite;"></div>
                            </div>
                            <div class="w-[2px] my-1 rounded-full flex-1" style="background:rgba(70,62,227,0.1); min-height:28px;"></div>
                        </div>
                        <div class="pt-0.5 pb-3">
                            <p class="text-sm font-bold" style="color:#463EE3">Telaah Etik Berlangsung</p>
                            <p class="text-xs mt-0.5 font-light" style="color:#8E8CAD">Evaluasi Tim Reviewer Independen</p>
                        </div>
                    </div>

                    {{-- Step 3: Pending --}}
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                                 style="background:#F5F5F5; border:1px solid rgba(0,0,0,0.08);">
                                <div class="w-2 h-2 rounded-full" style="background:#d0cfe8;"></div>
                            </div>
                        </div>
                        <div class="pt-0.5">
                            <p class="text-sm font-semibold" style="color:#8E8CAD">Penerbitan Klirens</p>
                            <p class="text-xs mt-0.5 font-light" style="color:#b0aec8">Menunggu hasil putusan sidang</p>
                        </div>
                    </div>
                </div>

                {{-- Floating badge: Keamanan (top-right) --}}
                <div class="absolute z-20 flex items-center gap-3 rounded-2xl border bg-white/95 backdrop-blur-sm px-3.5 py-2.5"
                     style="top: 48px; right: -12px;
                            border-color:rgba(70,62,227,0.1);
                            box-shadow:0 8px 28px rgba(70,62,227,0.10);
                            animation: semar-float1 5s ease-in-out infinite 0.5s;">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background:#E6E6FA;">
                        <svg class="w-4 h-4" fill="none" stroke="#463EE3" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest" style="color:#8E8CAD">Keamanan</p>
                        <p class="text-xs font-bold" style="color:#0F0E2E">Sistem Terenkripsi</p>
                    </div>
                </div>

                {{-- Floating badge: SSO (bottom-left) --}}
                <div class="absolute z-20 flex items-center gap-3 rounded-2xl border bg-white/95 backdrop-blur-sm px-3.5 py-2.5"
                     style="bottom: 72px; left: -12px;
                            border-color:rgba(70,62,227,0.1);
                            box-shadow:0 8px 28px rgba(70,62,227,0.10);
                            animation: semar-float2 6s ease-in-out infinite;">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background:rgba(176,224,230,0.3);">
                        <svg class="w-4 h-4" fill="none" stroke="#0e6e82" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest" style="color:#8E8CAD">Akses</p>
                        <p class="text-xs font-bold" style="color:#0F0E2E">SSO Terintegrasi</p>
                    </div>
                </div>

            </div>{{-- end hero visual --}}
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     PLATFORM OVERVIEW
════════════════════════════════════════════════════════ --}}
<section id="overview" class="py-24 relative overflow-hidden" style="background:#F5F5F5; border-top:1px solid rgba(70,62,227,0.06); border-bottom:1px solid rgba(70,62,227,0.06);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">

            {{-- Left: text --}}
            <div class="lg:col-span-5">
                <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#463EE3">Platform Overview</p>
                <h2 class="text-3xl md:text-4xl font-bold mb-4 leading-tight" style="color:#0F0E2E">Platform Digital Terintegrasi</h2>
                <div class="w-10 h-[3px] rounded-full mb-6" style="background:#E6E6FA;"></div>
                <p class="text-base leading-relaxed mb-4 font-light" style="color:#5A587A;">
                    SEMAR merupakan platform digital terintegrasi untuk pengajuan, pemantauan, dan pengelolaan proses etik penelitian secara modern, transparan, dan efisien.
                </p>
                <p class="text-base leading-relaxed font-light" style="color:#5A587A;">
                    Kami menjembatani para peneliti, reviewer, dan komite etik dalam satu ekosistem akademik yang aman dan terstruktur.
                </p>
            </div>

            {{-- Right: feature cards --}}
            <div class="lg:col-span-7">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 relative">
                    {{-- Cross lines --}}
                    <div class="hidden sm:block absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[85%] h-px pointer-events-none" style="background:rgba(70,62,227,0.06); z-index:0;"></div>
                    <div class="hidden sm:block absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[85%] w-px pointer-events-none" style="background:rgba(70,62,227,0.06); z-index:0;"></div>

                    {{-- Card 1 --}}
                    <div class="relative z-10 rounded-2xl p-7 border bg-white group transition-all duration-300 hover:-translate-y-1"
                         style="border-color:rgba(70,62,227,0.08);"
                         onmouseover="this.style.boxShadow='0 12px 36px rgba(70,62,227,0.10)'; this.style.borderColor='rgba(70,62,227,0.2)'"
                         onmouseout="this.style.boxShadow='none'; this.style.borderColor='rgba(70,62,227,0.08)'">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5 transition-transform duration-300 group-hover:scale-110"
                             style="background:#E6E6FA;">
                            <svg class="w-5 h-5" fill="none" stroke="#463EE3" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-bold mb-2" style="color:#0F0E2E">Proses Cepat</h4>
                        <p class="text-sm leading-relaxed font-light" style="color:#5A587A">Pemrosesan dokumen etik terakselerasi berkat alur digital yang ramping dan terpusat.</p>
                    </div>

                    {{-- Card 2 (offset down) --}}
                    <div class="relative z-10 rounded-2xl p-7 border bg-white group transition-all duration-300 hover:-translate-y-1 sm:mt-8"
                         style="border-color:rgba(70,62,227,0.08);"
                         onmouseover="this.style.boxShadow='0 12px 36px rgba(70,62,227,0.10)'; this.style.borderColor='rgba(70,62,227,0.2)'"
                         onmouseout="this.style.boxShadow='none'; this.style.borderColor='rgba(70,62,227,0.08)'">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5 transition-transform duration-300 group-hover:scale-110"
                             style="background:rgba(135,206,235,0.2);">
                            <svg class="w-5 h-5" fill="none" stroke="#3A9DC9" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-bold mb-2" style="color:#0F0E2E">Evaluasi Independen</h4>
                        <p class="text-sm leading-relaxed font-light" style="color:#5A587A">Penelaahan dilakukan oleh dewan pakar bersertifikasi dengan menjunjung asas objektivitas.</p>
                    </div>

                    {{-- Card 3 (offset up) --}}
                    <div class="relative z-10 rounded-2xl p-7 border bg-white group transition-all duration-300 hover:-translate-y-1 sm:mb-8"
                         style="border-color:rgba(70,62,227,0.08);"
                         onmouseover="this.style.boxShadow='0 12px 36px rgba(70,62,227,0.10)'; this.style.borderColor='rgba(70,62,227,0.2)'"
                         onmouseout="this.style.boxShadow='none'; this.style.borderColor='rgba(70,62,227,0.08)'">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5 transition-transform duration-300 group-hover:scale-110"
                             style="background:rgba(176,224,230,0.3);">
                            <svg class="w-5 h-5" fill="none" stroke="#0e6e82" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-bold mb-2" style="color:#0F0E2E">Akses Global</h4>
                        <p class="text-sm leading-relaxed font-light" style="color:#5A587A">Platform berbasis cloud memungkinkan akses dari mana saja dan kapan saja secara aman.</p>
                    </div>

                    {{-- Card 4 --}}
                    <div class="relative z-10 rounded-2xl p-7 border bg-white group transition-all duration-300 hover:-translate-y-1"
                         style="border-color:rgba(70,62,227,0.08);"
                         onmouseover="this.style.boxShadow='0 12px 36px rgba(70,62,227,0.10)'; this.style.borderColor='rgba(70,62,227,0.2)'"
                         onmouseout="this.style.boxShadow='none'; this.style.borderColor='rgba(70,62,227,0.08)'">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5 transition-transform duration-300 group-hover:scale-110"
                             style="background:#fff8e1;">
                            <svg class="w-5 h-5" fill="none" stroke="#E65100" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-bold mb-2" style="color:#0F0E2E">Monitoring Real-time</h4>
                        <p class="text-sm leading-relaxed font-light" style="color:#5A587A">Lacak status pengajuan dari draf hingga penerbitan sertifikat tanpa hambatan.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     VISI & MISI
════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14">
            <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#463EE3">Fondasi Platform</p>
            <h2 class="text-3xl md:text-4xl font-bold" style="color:#0F0E2E">Visi &amp; Misi</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Visi --}}
            <div class="relative rounded-3xl p-10 md:p-12 overflow-hidden border transition-all duration-300 hover:-translate-y-1"
                 style="background:#F5F5F5; border-color:rgba(70,62,227,0.08);"
                 onmouseover="this.style.boxShadow='0 16px 48px rgba(70,62,227,0.09)'"
                 onmouseout="this.style.boxShadow='none'">
                {{-- Deco blob --}}
                <div class="absolute top-0 right-0 w-48 h-48 rounded-bl-full pointer-events-none"
                     style="background: radial-gradient(circle at top right, #E6E6FA 0%, transparent 70%);">
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-5 h-[2px]" style="background:#463EE3;"></div>
                        <p class="text-xs font-bold tracking-widest uppercase" style="color:#463EE3">Visi</p>
                    </div>
                    <p class="text-xl leading-relaxed font-light mb-8" style="color:#0F0E2E;">
                        Menjadi pusat keunggulan dalam penjaminan mutu dan etika penelitian yang berstandar internasional, mendorong integritas keilmuan, serta memfasilitasi inovasi riset untuk kemajuan institusi dan masyarakat.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full" style="background:#E6E6FA; color:#463EE3">Standar Internasional</span>
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full" style="background:rgba(135,206,235,0.25); color:#1a7fa3">Integritas Keilmuan</span>
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full" style="background:rgba(176,224,230,0.35); color:#0e6e82">Inovasi Riset</span>
                    </div>
                </div>
            </div>

            {{-- Misi --}}
            <div class="relative rounded-3xl p-10 md:p-12 overflow-hidden transition-all duration-300 hover:-translate-y-1"
                 style="background:#463EE3;"
                 onmouseover="this.style.boxShadow='0 16px 48px rgba(70,62,227,0.35)'"
                 onmouseout="this.style.boxShadow='none'">
                {{-- Deco blobs --}}
                <div class="absolute bottom-0 left-0 w-48 h-48 rounded-tr-full pointer-events-none"
                     style="background:rgba(255,255,255,0.06);"></div>
                <div class="absolute top-0 right-0 w-36 h-36 rounded-full pointer-events-none"
                     style="background:rgba(135,206,235,0.12); transform:translate(30%,-30%);"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-5 h-[2px]" style="background:rgba(230,230,250,0.6);"></div>
                        <p class="text-xs font-bold tracking-widest uppercase" style="color:rgba(230,230,250,0.8)">Misi</p>
                    </div>
                    <ul class="space-y-5">
                        <li class="flex items-start gap-4">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                                 style="background:rgba(255,255,255,0.15);">
                                <span class="w-2 h-2 rounded-full bg-white block"></span>
                            </div>
                            <p class="text-base leading-relaxed font-light" style="color:rgba(255,255,255,0.88)">
                                Meningkatkan kualitas pengawasan dan telaah etika penelitian secara profesional.
                            </p>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                                 style="background:rgba(255,255,255,0.15);">
                                <span class="w-2 h-2 rounded-full bg-white block"></span>
                            </div>
                            <p class="text-base leading-relaxed font-light" style="color:rgba(255,255,255,0.88)">
                                Membangun ekosistem riset yang aman, independen, transparan, dan terstruktur.
                            </p>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                                 style="background:rgba(255,255,255,0.15);">
                                <span class="w-2 h-2 rounded-full bg-white block"></span>
                            </div>
                            <p class="text-base leading-relaxed font-light" style="color:rgba(255,255,255,0.88)">
                                Mendukung digitalisasi administrasi akademik yang responsif terhadap teknologi.
                            </p>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     NILAI UTAMA
════════════════════════════════════════════════════════ --}}
<section class="py-24" style="background:#F5F5F5; border-top:1px solid rgba(70,62,227,0.06); border-bottom:1px solid rgba(70,62,227,0.06);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14">
            <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#463EE3">Fondasi Institusi</p>
            <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color:#0F0E2E">Nilai Utama</h2>
            <p class="text-base max-w-xl mx-auto font-light leading-relaxed" style="color:#5A587A">
                Prinsip dasar yang menjadi landasan operasional platform SEMAR dalam menjaga standar akademik.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

            @php
            $nilai = [
                [
                    'title' => 'Transparansi',
                    'desc'  => 'Keterbukaan informasi dan kejelasan proses pada setiap tahapan review etika.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                ],
                [
                    'title' => 'Integritas',
                    'desc'  => 'Menjunjung tinggi etika moral, objektivitas, dan standar keilmuan tanpa kompromi.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                ],
                [
                    'title' => 'Efisiensi',
                    'desc'  => 'Penggunaan waktu dan sumber daya secara optimal guna mempercepat pelayanan.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                ],
                [
                    'title' => 'Keamanan Data',
                    'desc'  => 'Perlindungan privasi peneliti dan subjek melalui enkripsi sistem yang ketat.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
                ],
            ];
            @endphp

            @foreach($nilai as $item)
            <div class="bg-white rounded-2xl p-8 border relative overflow-hidden group transition-all duration-300 hover:-translate-y-1"
                 style="border-color:rgba(70,62,227,0.08);"
                 onmouseover="this.style.boxShadow='0 12px 32px rgba(70,62,227,0.10)'; this.style.borderColor='rgba(70,62,227,0.2)'"
                 onmouseout="this.style.boxShadow='none'; this.style.borderColor='rgba(70,62,227,0.08)'">
                {{-- Bottom accent line --}}
                <div class="absolute bottom-0 left-0 w-full h-[3px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 rounded-b-2xl"
                     style="background: linear-gradient(90deg, #463EE3, #87CEEB);"></div>
                <div class="w-11 h-11 rounded-xl border flex items-center justify-center mb-5 transition-all duration-300"
                     style="border-color:rgba(70,62,227,0.12);"
                     data-hover-bg="E6E6FA">
                    <svg class="w-5 h-5 transition-all duration-300" fill="none" stroke="#8E8CAD" stroke-width="1.6" viewBox="0 0 24 24"
                         data-hover-stroke="463EE3">
                        {!! $item['icon'] !!}
                    </svg>
                </div>
                <h4 class="text-base font-bold mb-2" style="color:#0F0E2E">{{ $item['title'] }}</h4>
                <p class="text-sm leading-relaxed font-light" style="color:#5A587A">{{ $item['desc'] }}</p>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     WORKFLOW
════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
            <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#463EE3">Alur Kolaborasi</p>
            <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color:#0F0E2E">Workflow Ekosistem</h2>
            <p class="text-base max-w-lg mx-auto font-light leading-relaxed" style="color:#5A587A">
                Integrasi mulus antara seluruh aktor utama dalam proses riset, mempercepat pengambilan keputusan etik dari pengajuan hingga sertifikasi.
            </p>
        </div>

        {{-- Steps --}}
        <div class="relative max-w-5xl mx-auto">
            {{-- Connector line --}}
            <div class="hidden md:block absolute z-0 h-[2px]"
                 style="top:32px; left:12.5%; right:12.5%;
                        background: linear-gradient(90deg, #E6E6FA, #87CEEB, #B0E0E6, #E6E6FA);">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 relative z-10">

                @php
                $steps = [
                    ['num'=>'1','actor'=>'Peneliti','action'=>'Mengajukan Protokol',
                     'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                     'bg'=>'linear-gradient(135deg,#E6E6FA,white)'],
                    ['num'=>'2','actor'=>'Reviewer','action'=>'Menelaah Dokumen',
                     'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
                     'bg'=>'linear-gradient(135deg,rgba(135,206,235,0.25),white)'],
                    ['num'=>'3','actor'=>'Komite Etik','action'=>'Sidang & Validasi',
                     'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                     'bg'=>'linear-gradient(135deg,rgba(176,224,230,0.3),white)'],
                    ['num'=>'4','actor'=>'Klirens Etik','action'=>'Penerbitan Sertifikat',
                     'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>',
                     'bg'=>'linear-gradient(135deg,#E6E6FA,rgba(176,224,230,0.3))'],
                ];
                @endphp

                @foreach($steps as $step)
                <div class="flex flex-col items-center text-center group">
                    <div class="relative w-16 h-16 rounded-2xl flex items-center justify-center mb-5 border transition-all duration-300 group-hover:-translate-y-1"
                         style="background:{{ $step['bg'] }}; border-color:rgba(70,62,227,0.15);
                                box-shadow:0 4px 20px rgba(70,62,227,0.10);"
                         onmouseover="this.style.boxShadow='0 12px 32px rgba(70,62,227,0.18)'"
                         onmouseout="this.style.boxShadow='0 4px 20px rgba(70,62,227,0.10)'">
                        <span class="text-xl font-bold" style="color:#463EE3; font-family:serif;">{{ $step['num'] }}</span>
                        {{-- Mini icon badge --}}
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 rounded-full flex items-center justify-center"
                             style="background:#463EE3; box-shadow:0 2px 8px rgba(70,62,227,0.3);">
                            <svg class="w-3 h-3" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                {!! $step['icon'] !!}
                            </svg>
                        </div>
                    </div>
                    <h4 class="text-base font-bold mb-1" style="color:#0F0E2E">{{ $step['actor'] }}</h4>
                    <span class="text-xs font-medium px-3 py-1 rounded-full mt-1"
                          style="background:#F5F5F5; color:#5A587A">{{ $step['action'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Detail cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 max-w-5xl mx-auto mt-10">
            @php
            $details = [
                ['pill'=>'Tahap 1','color'=>'background:#E6E6FA;color:#463EE3','text'=>'Upload protokol, informed consent, CV peneliti, dan dokumen pendukung via portal.'],
                ['pill'=>'Tahap 2','color'=>'background:rgba(135,206,235,0.25);color:#1a7fa3','text'=>'Reviewer independen menilai kelayakan etik menggunakan checklist terstandar.'],
                ['pill'=>'Tahap 3','color'=>'background:rgba(176,224,230,0.35);color:#0e6e82','text'=>'Sidang pleno komite etik memutuskan: disetujui, revisi minor, atau revisi mayor.'],
                ['pill'=>'Tahap 4','color'=>'background:rgba(70,62,227,0.1);color:#463EE3','text'=>'Sertifikat klirens etik diterbitkan dan dapat diunduh langsung oleh peneliti.'],
            ];
            @endphp
            @foreach($details as $d)
            <div class="rounded-2xl border p-5 text-center" style="background:#F5F5F5; border-color:rgba(70,62,227,0.07);">
                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-3"
                      style="{{ $d['color'] }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $d['pill'] }}
                </span>
                <p class="text-xs leading-relaxed font-light" style="color:#5A587A">{{ $d['text'] }}</p>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     INLINE STYLES & ANIMATIONS
     (Tambahkan ini ke app.css / layout jika memungkinkan)
════════════════════════════════════════════════════════ --}}
@push('styles')
<style>
    @keyframes semar-float {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-12px); }
    }
    @keyframes semar-float1 {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-8px); }
    }
    @keyframes semar-float2 {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(8px); }
    }
    @keyframes semar-pulse {
        0%, 100% { opacity:1; transform:scale(1); }
        50%       { opacity:0.6; transform:scale(0.82); }
    }
    @keyframes semar-ripple {
        0%   { transform:scale(1); opacity:0.5; }
        100% { transform:scale(1.75); opacity:0; }
    }
</style>
@endpush

</x-layouts.landing>