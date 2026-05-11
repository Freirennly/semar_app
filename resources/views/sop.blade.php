<x-layouts.landing>

{{-- ═══════════════════════════════════════════════════════
     HERO SECTION
════════════════════════════════════════════════════════ --}}
<section class="relative pt-32 pb-20 overflow-hidden" style="background:#ffffff; border-bottom:1px solid rgba(70,62,227,0.08);">

    {{-- Grid pattern --}}
    <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.025]"
         style="background-image: linear-gradient(to right, #463EE3 1px, transparent 1px),
                                  linear-gradient(to bottom, #463EE3 1px, transparent 1px);
                background-size: 28px 28px;">
    </div>
    {{-- Glow blobs --}}
    <div class="absolute top-0 right-0 pointer-events-none"
         style="width:600px;height:600px;border-radius:50%;
                background:radial-gradient(circle,rgba(230,230,250,0.7) 0%,transparent 70%);
                transform:translate(30%,-25%);">
    </div>
    <div class="absolute bottom-0 left-0 pointer-events-none"
         style="width:400px;height:400px;border-radius:50%;
                background:radial-gradient(circle,rgba(176,224,230,0.45) 0%,transparent 70%);
                transform:translate(-25%,25%);">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold mb-6 tracking-widest uppercase border"
             style="background:rgba(70,62,227,0.06); color:#463EE3; border-color:rgba(70,62,227,0.15);">
            Documentation Center
        </div>
        <h1 class="font-bold tracking-tight mb-4 leading-tight"
            style="font-size:clamp(2.5rem,5vw,3.5rem); color:#0F0E2E;">
            Pelajari <span style="color:#463EE3">SOP</span>
        </h1>
        <p class="text-lg max-w-xl mx-auto font-light leading-relaxed" style="color:#5A587A;">
            Panduan prosedur pengajuan etik penelitian melalui sistem SEMAR — dari persiapan dokumen hingga penerbitan klirens.
        </p>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     QUICK NAVIGATION
════════════════════════════════════════════════════════ --}}
<section class="py-12" style="background:#F5F5F5; border-bottom:1px solid rgba(70,62,227,0.06);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">

            @php
            $navItems = [
                ['href'=>'#timeline','label'=>'Pengajuan Protokol',
                 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                ['href'=>'#timeline','label'=>'Verifikasi Administratif',
                 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                ['href'=>'#kategori','label'=>'Proses Review',
                 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>'],
                ['href'=>'#timeline','label'=>'Klirens Etik',
                 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
                ['href'=>'#timeline','label'=>'Revisi Dokumen',
                 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>'],
                ['href'=>'#timeline','label'=>'Monitoring Status',
                 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'],
            ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ $item['href'] }}"
               class="flex flex-col items-center p-4 rounded-2xl border bg-white text-center transition-all duration-200 group"
               style="border-color:rgba(70,62,227,0.08);"
               onmouseover="this.style.borderColor='rgba(70,62,227,0.35)'; this.style.boxShadow='0 4px 16px rgba(70,62,227,0.09)'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='rgba(70,62,227,0.08)'; this.style.boxShadow='none'; this.style.transform='translateY(0)'">
                <svg class="w-6 h-6 mb-3 transition-colors duration-200" fill="none" stroke="#8E8CAD" stroke-width="1.5" viewBox="0 0 24 24"
                     onmouseover="this.style.stroke='#463EE3'"
                     onmouseout="this.style.stroke='#8E8CAD'">
                    {!! $item['icon'] !!}
                </svg>
                <span class="text-xs font-semibold leading-tight" style="color:#0F0E2E">{{ $item['label'] }}</span>
            </a>
            @endforeach

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     SOP TIMELINE
════════════════════════════════════════════════════════ --}}
<section id="timeline" class="py-24" style="background:#ffffff;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
            <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#463EE3">Alur Pengajuan</p>
            <h2 class="text-3xl font-bold" style="color:#0F0E2E">Tahapan Pengajuan</h2>
        </div>

        <div class="relative">
            {{-- Vertical line --}}
            <div class="absolute left-1/2 top-0 bottom-0 w-[2px] -translate-x-1/2 z-0 hidden md:block"
                 style="background: linear-gradient(to bottom, #E6E6FA, #87CEEB, #B0E0E6, #E6E6FA);">
            </div>
            {{-- Mobile line --}}
            <div class="absolute left-4 top-0 bottom-0 w-[2px] z-0 md:hidden"
                 style="background: linear-gradient(to bottom, #E6E6FA, #87CEEB, #B0E0E6, #E6E6FA);">
            </div>

            @php
            $steps = [
                ['num'=>'1','title'=>'Login SSO','desc'=>'Masuk menggunakan akun SSO institusi untuk memulai pengajuan baru pada sistem.','side'=>'left'],
                ['num'=>'2','title'=>'Isi Data Penelitian','desc'=>'Lengkapi formulir metadata penelitian seperti judul, tim peneliti, ringkasan, dan metodologi.','side'=>'right'],
                ['num'=>'3','title'=>'Upload Dokumen','desc'=>'Unggah protokol penelitian, CV, lembar persetujuan (informed consent), dan dokumen pendukung lainnya.','side'=>'left'],
                ['num'=>'4','title'=>'Verifikasi Administratif','desc'=>'Sekretariat melakukan pengecekan kelengkapan dokumen (1–2 hari kerja).','side'=>'right'],
                ['num'=>'5','title'=>'Proses Review Etik','desc'=>'Ketua komite menugaskan reviewer sesuai kategori (Exempted, Expedited, Fullboard).','side'=>'left'],
                ['num'=>'6','title'=>'Hasil Evaluasi','desc'=>'Pemberitahuan hasil review melalui dashboard. Jika ada catatan, peneliti dapat melakukan revisi.','side'=>'right'],
                ['num'=>'7','title'=>'Penerbitan Klirens','desc'=>'Penerbitan sertifikat klirens etik (Ethical Clearance) yang dapat diunduh langsung dari sistem.','side'=>'left'],
            ];
            @endphp

            <div class="space-y-10">
                @foreach($steps as $i => $step)
                <div class="relative flex items-start md:items-center gap-6 md:gap-0 pl-12 md:pl-0">

                    {{-- Mobile: number dot on left --}}
                    <div class="absolute left-0 top-0 md:hidden w-8 h-8 rounded-full flex items-center justify-center z-10 border-2 bg-white"
                         style="border-color:#463EE3;">
                        <span class="text-xs font-bold" style="color:#463EE3">{{ $step['num'] }}</span>
                    </div>

                    @if($step['side'] === 'left')
                        {{-- Desktop: content left --}}
                        <div class="hidden md:block w-5/12 text-right pr-12">
                            <div class="inline-block bg-white rounded-2xl border p-6 text-left transition-all duration-300 hover:-translate-y-1"
                                 style="border-color:rgba(70,62,227,0.1);"
                                 onmouseover="this.style.boxShadow='0 12px 32px rgba(70,62,227,0.09)'; this.style.borderColor='rgba(70,62,227,0.22)'"
                                 onmouseout="this.style.boxShadow='none'; this.style.borderColor='rgba(70,62,227,0.1)'">
                                <h3 class="text-base font-bold mb-1.5" style="color:#0F0E2E">{{ $step['title'] }}</h3>
                                <p class="text-sm font-light leading-relaxed" style="color:#5A587A">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                        {{-- Desktop: center dot --}}
                        <div class="hidden md:flex w-2/12 justify-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 border-2 bg-white"
                                 style="border-color:#463EE3; box-shadow:0 0 0 4px rgba(70,62,227,0.08);">
                                <span class="text-sm font-bold" style="color:#463EE3">{{ $step['num'] }}</span>
                            </div>
                        </div>
                        <div class="hidden md:block w-5/12"></div>
                    @else
                        <div class="hidden md:block w-5/12"></div>
                        {{-- Desktop: center dot --}}
                        <div class="hidden md:flex w-2/12 justify-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 border-2 bg-white"
                                 style="border-color:#463EE3; box-shadow:0 0 0 4px rgba(70,62,227,0.08);">
                                <span class="text-sm font-bold" style="color:#463EE3">{{ $step['num'] }}</span>
                            </div>
                        </div>
                        {{-- Desktop: content right --}}
                        <div class="hidden md:block w-5/12 pl-12">
                            <div class="inline-block bg-white rounded-2xl border p-6 transition-all duration-300 hover:-translate-y-1"
                                 style="border-color:rgba(70,62,227,0.1);"
                                 onmouseover="this.style.boxShadow='0 12px 32px rgba(70,62,227,0.09)'; this.style.borderColor='rgba(70,62,227,0.22)'"
                                 onmouseout="this.style.boxShadow='none'; this.style.borderColor='rgba(70,62,227,0.1)'">
                                <h3 class="text-base font-bold mb-1.5" style="color:#0F0E2E">{{ $step['title'] }}</h3>
                                <p class="text-sm font-light leading-relaxed" style="color:#5A587A">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Mobile: content --}}
                    <div class="md:hidden bg-white rounded-2xl border p-5 w-full"
                         style="border-color:rgba(70,62,227,0.1);">
                        <h3 class="text-sm font-bold mb-1" style="color:#0F0E2E">{{ $step['title'] }}</h3>
                        <p class="text-xs font-light leading-relaxed" style="color:#5A587A">{{ $step['desc'] }}</p>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     KATEGORI TELAAH
════════════════════════════════════════════════════════ --}}
<section id="kategori" class="py-24" style="background:#F5F5F5; border-top:1px solid rgba(70,62,227,0.06); border-bottom:1px solid rgba(70,62,227,0.06);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14">
            <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#463EE3">Klasifikasi Risiko</p>
            <h2 class="text-3xl font-bold" style="color:#0F0E2E">Kategori Telaah</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Exempted --}}
            <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-300 hover:-translate-y-1"
                 style="border-color:rgba(70,62,227,0.08);"
                 onmouseover="this.style.boxShadow='0 16px 40px rgba(34,197,94,0.10)'"
                 onmouseout="this.style.boxShadow='none'">
                <div class="h-1.5 w-full" style="background:#22C55E;"></div>
                <div class="p-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold" style="color:#0F0E2E">Exempted</h3>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                              style="background:rgba(34,197,94,0.1); color:#15803d;">Risiko Minimal</span>
                    </div>
                    <p class="text-sm font-light leading-relaxed mb-6" style="color:#5A587A">
                        Penelitian yang tidak melibatkan intervensi klinis atau risiko minimal. Waktu telaah lebih cepat.
                    </p>
                    <ul class="space-y-3">
                        @foreach(['Data sekunder/rekam medis anonim','Kuesioner publik umum','Studi kepustakaan'] as $item)
                        <li class="flex items-start gap-2.5 text-sm font-light" style="color:#5A587A">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="#22C55E" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <div class="mt-6 pt-5 border-t" style="border-color:rgba(0,0,0,0.06);">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="#8E8CAD" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs font-medium" style="color:#5A587A">Estimasi: <strong style="color:#0F0E2E">3–5 hari kerja</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expedited --}}
            <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-300 hover:-translate-y-1"
                 style="border-color:rgba(70,62,227,0.08);"
                 onmouseover="this.style.boxShadow='0 16px 40px rgba(230,160,0,0.10)'"
                 onmouseout="this.style.boxShadow='none'">
                <div class="h-1.5 w-full" style="background:#F59E0B;"></div>
                <div class="p-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold" style="color:#0F0E2E">Expedited</h3>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                              style="background:rgba(245,158,11,0.1); color:#b45309;">Risiko Rendah</span>
                    </div>
                    <p class="text-sm font-light leading-relaxed mb-6" style="color:#5A587A">
                        Penelitian dengan risiko rendah. Melibatkan subjek namun prosedur non-invasif.
                    </p>
                    <ul class="space-y-3">
                        @foreach(['Pengambilan darah volume kecil','Pemeriksaan fisik rutin','Rekaman aktivitas non-invasif'] as $item)
                        <li class="flex items-start gap-2.5 text-sm font-light" style="color:#5A587A">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="#F59E0B" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <div class="mt-6 pt-5 border-t" style="border-color:rgba(0,0,0,0.06);">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="#8E8CAD" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs font-medium" style="color:#5A587A">Estimasi: <strong style="color:#0F0E2E">7–10 hari kerja</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fullboard --}}
            <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-300 hover:-translate-y-1"
                 style="border-color:rgba(70,62,227,0.08);"
                 onmouseover="this.style.boxShadow='0 16px 40px rgba(239,68,68,0.10)'"
                 onmouseout="this.style.boxShadow='none'">
                <div class="h-1.5 w-full" style="background:#EF4444;"></div>
                <div class="p-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold" style="color:#0F0E2E">Fullboard</h3>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                              style="background:rgba(239,68,68,0.1); color:#b91c1c;">Risiko Tinggi</span>
                    </div>
                    <p class="text-sm font-light leading-relaxed mb-6" style="color:#5A587A">
                        Risiko tinggi. Memerlukan sidang pleno seluruh anggota komite etik.
                    </p>
                    <ul class="space-y-3">
                        @foreach(['Uji klinis obat/alat kesehatan baru','Subjek rentan (anak, lansia, dll)','Prosedur invasif berisiko'] as $item)
                        <li class="flex items-start gap-2.5 text-sm font-light" style="color:#5A587A">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="#EF4444" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <div class="mt-6 pt-5 border-t" style="border-color:rgba(0,0,0,0.06);">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="#8E8CAD" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs font-medium" style="color:#5A587A">Estimasi: <strong style="color:#0F0E2E">14–30 hari kerja</strong></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FAQ SECTION
════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-white" x-data="{ activeAccordion: 1 }">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14">
            <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#463EE3">Bantuan</p>
            <h2 class="text-3xl font-bold" style="color:#0F0E2E">Frequently Asked Questions</h2>
        </div>

        @php
        $faqs = [
            ['id'=>1,'q'=>'Berapa lama proses review?',
             'a'=>'Waktu review bergantung pada kategori telaah. Exempted biasanya 3–5 hari kerja, Expedited 7–10 hari kerja, sedangkan Fullboard menyesuaikan jadwal sidang pleno komite (biasanya 14–30 hari kerja).'],
            ['id'=>2,'q'=>'Dokumen apa saja yang dibutuhkan?',
             'a'=>'Dokumen esensial meliputi: Protokol Penelitian lengkap, Form Persetujuan Setelah Penjelasan (Informed Consent), Kuesioner/Pedoman Wawancara (jika ada), CV Peneliti Utama, dan Surat Pengantar Institusi. Detail format dapat diunduh di bagian Unduhan.'],
            ['id'=>3,'q'=>'Bagaimana revisi dilakukan?',
             'a'=>'Jika hasil telaah memerlukan perbaikan, sistem akan memberikan notifikasi. Anda dapat melihat catatan reviewer langsung di dashboard, mengunggah kembali dokumen yang telah direvisi, dan mengirimkannya kembali ke komite untuk dievaluasi ulang.'],
            ['id'=>4,'q'=>'Bagaimana melihat status pengajuan?',
             'a'=>'Status pengajuan bersifat real-time dan dapat dipantau di halaman Dashboard akun Anda. Status mencakup: Draft, Submitted, Under Review, Revision Required, hingga Approved.'],
        ];
        @endphp

        <div class="space-y-3">
            @foreach($faqs as $faq)
            <div class="rounded-2xl border overflow-hidden bg-white transition-all duration-200"
                 style="border-color:rgba(70,62,227,0.1);"
                 x-bind:style="activeAccordion === {{ $faq['id'] }} ? 'border-color:rgba(70,62,227,0.25);box-shadow:0 4px 20px rgba(70,62,227,0.07)' : 'border-color:rgba(70,62,227,0.1)'">
                <button @click="activeAccordion = activeAccordion === {{ $faq['id'] }} ? null : {{ $faq['id'] }}"
                        class="w-full px-6 py-4 flex justify-between items-center text-left transition-colors duration-200 focus:outline-none"
                        x-bind:style="activeAccordion === {{ $faq['id'] }} ? 'background:rgba(70,62,227,0.03)' : 'background:white'">
                    <span class="font-semibold text-sm" style="color:#0F0E2E">{{ $faq['q'] }}</span>
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 ml-4 transition-all duration-200"
                         x-bind:style="activeAccordion === {{ $faq['id'] }} ? 'background:#E6E6FA' : 'background:#F5F5F5'">
                        <svg class="w-4 h-4 transition-transform duration-200"
                             x-bind:class="{ 'rotate-180': activeAccordion === {{ $faq['id'] }} }"
                             fill="none" viewBox="0 0 24 24"
                             x-bind:style="activeAccordion === {{ $faq['id'] }} ? 'stroke:#463EE3' : 'stroke:#8E8CAD'"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>
                <div x-show="activeAccordion === {{ $faq['id'] }}" x-collapse>
                    <div class="px-6 pb-5 pt-0 text-sm font-light leading-relaxed border-t"
                         style="color:#5A587A; border-color:rgba(70,62,227,0.08);">
                        <div class="pt-4">{{ $faq['a'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     DOWNLOAD SECTION
════════════════════════════════════════════════════════ --}}
<section class="py-24" style="background:#F5F5F5; border-top:1px solid rgba(70,62,227,0.06);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-3xl border p-8 md:p-12" style="border-color:rgba(70,62,227,0.08);">

            <div class="text-center mb-10">
                <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#463EE3">Sumber Daya</p>
                <h2 class="text-2xl font-bold mb-3" style="color:#0F0E2E">Pusat Unduhan</h2>
                <p class="text-sm font-light max-w-xl mx-auto leading-relaxed" style="color:#5A587A">
                    Akses seluruh format dokumen standar, panduan sistem, dan regulasi etik penelitian institusi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                {{-- PDF --}}
                <a href="#"
                   class="flex items-center gap-4 p-5 rounded-2xl border group transition-all duration-200"
                   style="border-color:rgba(70,62,227,0.08);"
                   onmouseover="this.style.borderColor='rgba(70,62,227,0.3)'; this.style.background='rgba(70,62,227,0.03)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(70,62,227,0.08)'"
                   onmouseout="this.style.borderColor='rgba(70,62,227,0.08)'; this.style.background='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-200 group-hover:scale-105"
                         style="background:rgba(239,68,68,0.08);">
                        <svg class="w-6 h-6" fill="none" stroke="#EF4444" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm mb-0.5" style="color:#0F0E2E">SOP PDF Lengkap</h4>
                        <p class="text-xs font-light" style="color:#8E8CAD">Versi cetak regulasi — 2.4 MB</p>
                    </div>
                    <svg class="w-4 h-4 ml-auto opacity-40 transition-opacity group-hover:opacity-80" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </a>

                {{-- Template --}}
                <a href="#"
                   class="flex items-center gap-4 p-5 rounded-2xl border group transition-all duration-200"
                   style="border-color:rgba(70,62,227,0.08);"
                   onmouseover="this.style.borderColor='rgba(70,62,227,0.3)'; this.style.background='rgba(70,62,227,0.03)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(70,62,227,0.08)'"
                   onmouseout="this.style.borderColor='rgba(70,62,227,0.08)'; this.style.background='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-200 group-hover:scale-105"
                         style="background:#E6E6FA;">
                        <svg class="w-6 h-6" fill="none" stroke="#463EE3" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm mb-0.5" style="color:#0F0E2E">Template Dokumen</h4>
                        <p class="text-xs font-light" style="color:#8E8CAD">Protokol &amp; Informed Consent — ZIP</p>
                    </div>
                    <svg class="w-4 h-4 ml-auto opacity-40 transition-opacity group-hover:opacity-80" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </a>

                {{-- Panduan --}}
                <a href="#"
                   class="flex items-center gap-4 p-5 rounded-2xl border group transition-all duration-200"
                   style="border-color:rgba(70,62,227,0.08);"
                   onmouseover="this.style.borderColor='rgba(70,62,227,0.3)'; this.style.background='rgba(70,62,227,0.03)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(70,62,227,0.08)'"
                   onmouseout="this.style.borderColor='rgba(70,62,227,0.08)'; this.style.background='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-200 group-hover:scale-105"
                         style="background:rgba(245,158,11,0.1);">
                        <svg class="w-6 h-6" fill="none" stroke="#F59E0B" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm mb-0.5" style="color:#0F0E2E">Panduan Pengguna</h4>
                        <p class="text-xs font-light" style="color:#8E8CAD">Buku manual sistem SSO — 1.8 MB</p>
                    </div>
                    <svg class="w-4 h-4 ml-auto opacity-40 transition-opacity group-hover:opacity-80" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </a>

            </div>
        </div>
    </div>
</section>

</x-layouts.landing>