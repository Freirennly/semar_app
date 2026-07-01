<x-layouts.landing>

<!-- HERO SECTION -->
<section class="pt-32 pb-20 bg-white border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="font-bold text-primary mb-4 tracking-widest uppercase text-sm">
            Pusat Dokumentasi
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-text tracking-tight mb-6">
            Pelajari <span class="text-primary">SOP</span>
        </h1>
        <p class="text-lg text-text-secondary max-w-2xl mx-auto leading-relaxed">
            Panduan prosedur pengajuan etik penelitian melalui sistem SEMAR — dari persiapan dokumen hingga penerbitan klirens etik.
        </p>
    </div>
</section>

<!-- QUICK NAVIGATION -->
<section class="py-12 bg-white border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
            $navItems = [
                ['href'=>'#timeline','label'=>'Pengajuan Protokol', 'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['href'=>'#timeline','label'=>'Verifikasi Administratif', 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['href'=>'#kategori','label'=>'Proses Review', 'icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                ['href'=>'#timeline','label'=>'Klirens Etik', 'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['href'=>'#timeline','label'=>'Revisi Dokumen', 'icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                ['href'=>'#timeline','label'=>'Monitoring Status', 'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ $item['href'] }}" class="flex flex-col items-center p-5 rounded-xl border border-border bg-white text-center hover:border-primary hover:bg-bg transition-colors group">
                <svg class="w-6 h-6 mb-3 text-text-muted group-hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                </svg>
                <span class="text-xs font-bold text-text">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- DOWNLOAD SECTION (Dipindah ke atas agar mudah diakses) -->
<section class="py-20 bg-white border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-border p-8 md:p-12">
            <div class="text-center mb-10">
                <p class="text-xs font-bold tracking-widest uppercase mb-2 text-primary">Sumber Daya</p>
                <h2 class="text-3xl font-bold text-text mb-4">Pusat Unduhan</h2>
                <p class="text-base text-text-secondary max-w-xl mx-auto">
                    Akses format dokumen standar, panduan sistem, dan regulasi etik penelitian institusi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- PDF -->
                <a href="https://drive.google.com/file/d/1XhmnYUmTOE0QKaf_LB---B5Y3Z9BMBLR/view?usp=drive_link" target="_blank"
                   class="flex items-center gap-4 p-5 rounded-xl border border-border hover:border-primary hover:bg-bg transition-colors group">
                    <div class="w-12 h-12 rounded-lg bg-surface border border-border flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-text-secondary group-hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-text mb-1">SOP PDF Lengkap</h4>
                        <p class="text-xs text-text-muted">Versi cetak regulasi — 2.4 MB</p>
                    </div>
                </a>

                <!-- Template -->
                <a href="https://drive.google.com/file/d/137_W1se6SvV6DVS_hTMCiP1reVaF0RHr/view?usp=drive_link" target="_blank"
                   class="flex items-center gap-4 p-5 rounded-xl border border-border hover:border-primary hover:bg-bg transition-colors group">
                    <div class="w-12 h-12 rounded-lg bg-surface border border-border flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-text-secondary group-hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-text mb-1">Template Dokumen</h4>
                        <p class="text-xs text-text-muted">Protokol &amp; Informed Consent — ZIP</p>
                    </div>
                </a>

                <!-- Panduan -->
                <a href="https://drive.google.com/file/d/1oibjkEkmZ7E1y5pXF_2uP9uA65HP0okG/view?usp=sharing" target="_blank"
                   class="flex items-center gap-4 p-5 rounded-xl border border-border hover:border-primary hover:bg-bg transition-colors group">
                    <div class="w-12 h-12 rounded-lg bg-surface border border-border flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-text-secondary group-hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-text mb-1">Panduan Pengguna</h4>
                        <p class="text-xs text-text-muted">Buku manual sistem — 1.8 MB</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- SOP TIMELINE -->
<section id="timeline" class="py-20 bg-white border-b border-border">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-xs font-bold tracking-widest uppercase mb-2 text-primary">Alur Pengajuan</p>
            <h2 class="text-3xl font-bold text-text">Tahapan Pengajuan</h2>
        </div>

        <div class="relative">
            <!-- Vertical line -->
            <div class="absolute left-6 md:left-1/2 top-0 bottom-0 w-px bg-border md:-translate-x-1/2"></div>

            @php
            $steps = [
                ['num'=>'1','title'=>'Login SSO','desc'=>'Masuk menggunakan akun SSO institusi untuk memulai pengajuan baru pada sistem.','side'=>'left'],
                ['num'=>'2','title'=>'Isi Data Penelitian','desc'=>'Lengkapi formulir metadata penelitian seperti judul, tim peneliti, ringkasan, dan metodologi.','side'=>'right'],
                ['num'=>'3','title'=>'Upload Dokumen','desc'=>'Unggah protokol penelitian, CV, lembar persetujuan (informed consent), dan dokumen pendukung lainnya.','side'=>'left'],
                ['num'=>'4','title'=>'Verifikasi Administratif','desc'=>'Sekretariat melakukan pengecekan kelengkapan dokumen (1–2 hari kerja).','side'=>'right'],
                ['num'=>'5','title'=>'Proses Review Etik','desc'=>'Ketua komite menugaskan reviewer sesuai kategori (Exempted, Expedited, Fullboard).','side'=>'left'],
                ['num'=>'6','title'=>'Hasil Evaluasi','desc'=>'Pemberitahuan hasil review melalui dashboard. Jika ada catatan, peneliti dapat melakukan revisi.','side'=>'right'],
                ['num'=>'7','title'=>'Penerbitan Klirens','desc'=>'Penerbitan Surat Kelayakan Etik (Ethical Clearance) yang dapat diunduh langsung dari sistem.','side'=>'left'],
            ];
            @endphp

            <div class="space-y-8">
                @foreach($steps as $step)
                <div class="relative flex flex-col md:flex-row items-start md:items-center w-full">
                    
                    @if($step['side'] === 'left')
                        <div class="hidden md:block w-1/2 pr-12 text-right">
                            <div class="bg-bg border border-border p-6 rounded-xl inline-block text-left">
                                <h3 class="text-base font-bold text-text mb-2">{{ $step['title'] }}</h3>
                                <p class="text-sm text-text-secondary leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @else
                        <div class="hidden md:block w-1/2"></div>
                    @endif

                    <!-- Center dot (Desktop) -->
                    <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white border-2 border-primary items-center justify-center z-10">
                        <span class="text-sm font-bold text-primary">{{ $step['num'] }}</span>
                    </div>

                    <!-- Dot (Mobile) -->
                    <div class="md:hidden absolute left-0 top-6 w-12 h-12 rounded-full bg-white border-2 border-primary flex items-center justify-center z-10">
                        <span class="text-base font-bold text-primary">{{ $step['num'] }}</span>
                    </div>

                    @if($step['side'] === 'right')
                        <div class="hidden md:block w-1/2 pl-12">
                            <div class="bg-bg border border-border p-6 rounded-xl inline-block">
                                <h3 class="text-base font-bold text-text mb-2">{{ $step['title'] }}</h3>
                                <p class="text-sm text-text-secondary leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @else
                        <div class="hidden md:block w-1/2"></div>
                    @endif

                    <!-- Mobile Content -->
                    <div class="md:hidden w-full pl-16 py-4">
                        <div class="bg-bg border border-border p-5 rounded-xl w-full">
                            <h3 class="text-sm font-bold text-text mb-2">{{ $step['title'] }}</h3>
                            <p class="text-xs text-text-secondary leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- KATEGORI TELAAH -->
<section id="kategori" class="py-20 bg-white border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-xs font-bold tracking-widest uppercase mb-2 text-primary">Klasifikasi Risiko</p>
            <h2 class="text-3xl font-bold text-text">Kategori Telaah</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Exempted -->
            <div class="bg-white rounded-xl border border-border p-8 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-text">Exempted</h3>
                    <span class="text-xs font-bold px-3 py-1 rounded border border-border bg-surface text-text-secondary uppercase">Risiko Minimal</span>
                </div>
                <p class="text-sm text-text-secondary leading-relaxed mb-6">
                    Penelitian yang tidak melibatkan intervensi klinis atau risiko minimal. Waktu telaah lebih cepat.
                </p>
                <ul class="space-y-3 mb-6 flex-1">
                    @foreach(['Data sekunder/rekam medis anonim','Kuesioner publik umum','Studi kepustakaan'] as $item)
                    <li class="flex items-start gap-3 text-sm text-text-secondary">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <div class="pt-5 border-t border-border">
                    <p class="text-sm text-text-secondary">Estimasi: <strong class="text-text">3–5 hari kerja</strong></p>
                </div>
            </div>

            <!-- Expedited -->
            <div class="bg-white rounded-xl border border-border p-8 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-text">Expedited</h3>
                    <span class="text-xs font-bold px-3 py-1 rounded border border-border bg-surface text-text-secondary uppercase">Risiko Rendah</span>
                </div>
                <p class="text-sm text-text-secondary leading-relaxed mb-6">
                    Penelitian dengan risiko rendah. Melibatkan subjek namun prosedur non-invasif.
                </p>
                <ul class="space-y-3 mb-6 flex-1">
                    @foreach(['Pengambilan darah volume kecil','Pemeriksaan fisik rutin','Rekaman aktivitas non-invasif'] as $item)
                    <li class="flex items-start gap-3 text-sm text-text-secondary">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <div class="pt-5 border-t border-border">
                    <p class="text-sm text-text-secondary">Estimasi: <strong class="text-text">7–10 hari kerja</strong></p>
                </div>
            </div>

            <!-- Fullboard -->
            <div class="bg-white rounded-xl border border-border p-8 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-text">Fullboard</h3>
                    <span class="text-xs font-bold px-3 py-1 rounded border border-border bg-surface text-text-secondary uppercase">Risiko Tinggi</span>
                </div>
                <p class="text-sm text-text-secondary leading-relaxed mb-6">
                    Risiko tinggi. Memerlukan sidang pleno seluruh anggota komite etik.
                </p>
                <ul class="space-y-3 mb-6 flex-1">
                    @foreach(['Uji klinis obat/alat kesehatan baru','Subjek rentan (anak, lansia, dll)','Prosedur invasif berisiko'] as $item)
                    <li class="flex items-start gap-3 text-sm text-text-secondary">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <div class="pt-5 border-t border-border">
                    <p class="text-sm text-text-secondary">Estimasi: <strong class="text-text">14–30 hari kerja</strong></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ SECTION -->
<section class="py-20 bg-white border-b border-border">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-xs font-bold tracking-widest uppercase mb-2 text-primary">Bantuan</p>
            <h2 class="text-3xl font-bold text-text">Tanya Jawab</h2>
        </div>

        @php
        $faqs = [
            ['id'=>1,'q'=>'Berapa lama proses review?','a'=>'Waktu review bergantung pada kategori telaah. Exempted biasanya 3–5 hari kerja, Expedited 7–10 hari kerja, sedangkan Fullboard menyesuaikan jadwal sidang pleno komite (biasanya 14–30 hari kerja).'],
            ['id'=>2,'q'=>'Dokumen apa saja yang dibutuhkan?','a'=>'Dokumen esensial meliputi: Protokol Penelitian lengkap, Form Persetujuan Setelah Penjelasan (Informed Consent), Kuesioner/Pedoman Wawancara (jika ada), CV Peneliti Utama, dan Surat Pengantar Institusi. Detail format dapat diunduh di bagian Unduhan.'],
            ['id'=>3,'q'=>'Bagaimana revisi dilakukan?','a'=>'Jika hasil telaah memerlukan perbaikan, sistem akan memberikan notifikasi. Anda dapat melihat catatan reviewer langsung di dashboard, mengunggah kembali dokumen yang telah direvisi, dan mengirimkannya kembali ke komite untuk dievaluasi ulang.'],
            ['id'=>4,'q'=>'Bagaimana melihat status pengajuan?','a'=>'Status pengajuan bersifat real-time dan dapat dipantau di halaman Dashboard akun Anda. Status mencakup: Proposal Baru, Diproses, Sedang Direview, Perlu Revisi, hingga Selesai.'],
        ];
        @endphp

        <div class="space-y-4">
            @foreach($faqs as $faq)
            <div class="faq-item border border-border rounded-xl bg-white overflow-hidden">
                <button onclick="toggleFaq(this)" class="w-full px-6 py-5 flex justify-between items-center text-left hover:bg-bg transition-colors focus:outline-none">
                    <span class="font-bold text-sm text-text">{{ $faq['q'] }}</span>
                    <svg class="faq-icon w-5 h-5 text-text-muted transition-transform duration-300"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="px-6 pb-5 text-sm text-text-secondary leading-relaxed border-t border-border pt-4">
                        {{ $faq['a'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
function toggleFaq(button) {
    const item = button.parentElement;
    const content = item.querySelector('.faq-content');
    const icon = item.querySelector('.faq-icon');
    
    document.querySelectorAll('.faq-item').forEach(otherItem => {
        if (otherItem !== item) {
            const otherContent = otherItem.querySelector('.faq-content');
            const otherIcon = otherItem.querySelector('.faq-icon');
            otherContent.style.maxHeight = '0px';
            otherIcon.classList.remove('rotate-180');
        }
    });

    if (content.style.maxHeight === '0px' || !content.style.maxHeight) {
        content.style.maxHeight = content.scrollHeight + 'px';
        icon.classList.add('rotate-180');
    } else {
        content.style.maxHeight = '0px';
        icon.classList.remove('rotate-180');
    }
}
</script>

</x-layouts.landing>