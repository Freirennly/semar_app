@php 
    $tab = $tab ?? 'details'; 
@endphp

<x-layouts.app :title="'Detail Pengajuan - ' . $proposal->code">
    {{-- Atas: Navigasi Breadcrumb & Tombol Kembali Premium --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in">
        <nav class="text-[12px] text-text-secondary" aria-label="Breadcrumb">
            <a href="{{ route('admin.proposals.index') }}" class="hover:text-primary transition-colors">Manajemen Pengajuan</a> 
            <span class="mx-1">/</span> 
            <span class="text-text font-medium">{{ $proposal->code }}</span>
        </nav>
        
        {{-- Tombol Kembali --}}
        <a href="{{ route('admin.proposals.index') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 border border-border bg-white hover:bg-slate-50 text-text-secondary hover:text-text text-[13px] font-semibold rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    {{-- Kotak Judul Utama Premium --}}
    <div class="mb-12 p-6 bg-white border border-border rounded-2xl shadow-sm animate-fade-in">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2 max-w-3xl">
                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-text-secondary uppercase tracking-wider">
                    Kategori: {{ $proposal->type }}
                </span>
                <h1 class="text-2xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3] font-serif">
                    {{ $proposal->title }}
                </h1>
                <p class="text-[14px] text-text-secondary">
                    Registrasi Protokol: <span class="font-mono font-semibold text-text">{{ $proposal->code }}</span>
                </p>
            </div>
            <div class="shrink-0">
                <x-status-badge :status="$proposal->status" />
            </div>
        </div>
    </div>

    {{-- LAYOUT UTAMA: 10-COLUMN RESPONSIVE ARCHITECTURE (70/30 SPLIT) --}}
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6 items-start mb-12">
        
        {{-- KOLOM KIRI (70% WIDTH / COL-SPAN-7): Detail Berkas & Dokumen Lampiran --}}
        <div class="lg:col-span-7 space-y-6">
            
            {{-- Kartu 1: Informasi Ringkasan / Abstrak Usulan --}}
            <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm">
                <h2 class="text-xl md:text-[24px] font-semibold text-text leading-[1.4] mb-4 border-b border-border pb-2">
                    Informasi Usulan
                </h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm pt-2">
                    <div>
                        <dt class="text-text-secondary text-xs font-semibold uppercase tracking-wide">Nama Pengaju (Mahasiswa)</dt>
                        <dd class="font-semibold text-text mt-1 block text-[15px]">{{ optional($proposal->student)->name ?? 'Unknown' }}</dd>
                    </div>
                    <div>
                        <dt class="text-text-secondary text-xs font-semibold uppercase tracking-wide">NIM / NIP</dt>
                        <dd class="font-semibold text-text mt-1 block font-mono text-[15px]">{{ optional($proposal->student)->nim_nip ?? '-' }}</dd>
                    </div>
                    <div class="col-span-1 sm:col-span-2">
                        <dt class="text-text-secondary text-xs font-semibold uppercase tracking-wide mb-2">Abstrak / Ringkasan Penelitian</dt>
                        <dd class="text-text leading-relaxed font-academic whitespace-pre-line bg-slate-50/50 p-5 rounded-xl border border-border/60 shadow-inner">
                            {{ $proposal->abstract ?? 'Tidak ada abstrak.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Kartu 2: Berkas Lampiran Persyaratan Mahasiswa --}}
            <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-4 border-b border-border pb-3 flex-wrap gap-2">
                    <h2 class="text-xl md:text-[24px] font-semibold text-text leading-[1.4]">
                        Dokumen Lampiran Persyaratan
                    </h2>
                    
                    <a href="{{ route('admin.proposals.download', $proposal) }}" class="inline-flex items-center gap-1.5 bg-primary text-white hover:bg-primary-hover px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh File Utama
                    </a>
                </div>

                <div class="divide-y divide-border/60">
                    @forelse($proposal->documents as $doc)
                        <div class="py-4 flex items-center justify-between gap-4 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-text truncate font-sans">
                                    {{ optional($doc->template)->name ?? $doc->original_name }}
                                </p>
                                <p class="text-xs text-text-muted mt-0.5 font-mono">
                                    {{ $doc->original_name }} · {{ number_format($doc->size / 1024, 0) }} KB
                                </p>
                            </div>
                            <div class="shrink-0">
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($doc->file_path) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-text border border-border rounded-lg text-xs font-semibold transition-all shadow-sm">
                                    Lihat File
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-text-muted italic">
                            Belum ada dokumen persyaratan yang diunggah oleh mahasiswa ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (30% WIDTH / COL-SPAN-3): Panel Informasi Penugasan & Aksi Alur Kerja KEP --}}
        <div class="lg:col-span-3 space-y-6">
            
            {{-- Kartu Aksi Penugasan Sekretariat --}}
            <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-4">
                <h2 class="text-xl md:text-[20px] font-semibold text-text border-b border-border pb-2 leading-[1.4]">
                    Informasi Penugasan
                </h2>
                
                <div class="text-sm space-y-4">
                    <div>
                        <span class="text-text-secondary text-[11px] font-semibold block uppercase tracking-wide mb-1">Status Alur Kerja</span>
                        <x-status-badge :status="$proposal->status" />
                    </div>

                    <div class="pt-4 border-t border-border">
                        {{-- 🟢 KONDISI 1: JALUR REGULER (Wajib Memiliki Data Penugasan Reviewer Eksis) --}}
                        @if($proposal->assignments()->exists())
                            <div class="space-y-3 animate-fade-in">
                                <span class="text-[11px] font-semibold text-text-secondary block uppercase tracking-wide">Aktor Pemeriksa Ditugaskan</span>
                                
                                <div class="p-3.5 bg-blue-50 border border-blue-200 text-blue-900 rounded-xl text-xs space-y-3 shadow-inner">
                                    {{-- Tampilkan Nama Sekretaris --}}
                                    <div>
                                        <span class="text-[10px] uppercase tracking-wider text-blue-600 block font-bold mb-0.5">Sekretariat:</span>
                                        <p class="text-text font-semibold pl-0.5">
                                            {{ $proposal->secretary_id ? optional($proposal->secretary)->name : 'Tidak Ditugaskan' }}
                                        </p>
                                    </div>

                                    {{-- Tampilkan Daftar Nama Reviewer --}}
                                    <div class="pt-2.5 border-t border-blue-200/60">
                                        <span class="text-[10px] uppercase tracking-wider text-blue-600 block font-bold mb-1">Reviewer Kelaikan Etik:</span>
                                        <ul class="space-y-1 text-text font-semibold pl-0.5">
                                            @foreach($proposal->assignments as $assignment)
                                                @if($assignment->reviewer)
                                                    <li class="flex items-center gap-1.5">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                                        <span>{{ $assignment->reviewer->name }}</span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        {{-- 🟢 KONDISI 2: JALUR CEPAT / AUTO APPROVE (Tidak Ada Reviewer Tapi Status Sudah Approved/Done) --}}
                        @elseif(in_array($proposal->status->value, ['APPROVED', 'WAITING_SIGNATURE', 'DONE']))
                            <div class="space-y-2 animate-fade-in">
                                <span class="text-[11px] font-semibold text-text-secondary block uppercase tracking-wide">Aktor Pemeriksa Ditugaskan</span>
                                <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs space-y-2 shadow-inner">
                                    <div>
                                        <span class="text-[10px] uppercase tracking-wider text-emerald-600 block font-bold mb-0.5">Sekretariat Pemroses:</span>
                                        <p class="text-text font-semibold pl-0.5">
                                            {{ $proposal->secretary_id ? optional($proposal->secretary)->name : 'Sistem (Bypass)' }}
                                        </p>
                                    </div>
                                    <div class="pt-2 border-t border-emerald-200/60 flex items-center gap-1.5 text-emerald-700 font-bold text-[11px]">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                        <span>Disetujui via Jalur Cepat (Auto Approve)</span>
                                    </div>
                                </div>
                            </div>

                        {{-- KONDISI 3: BELUM DITUGASKAN (Dropdown Terbuka untuk Alur Pengajuan Baru/Revisi Awal) --}}
                        @else
                            {{-- Formulir Update Dropdown Penugasan --}}
                            <form method="POST" action="{{ route('admin.proposals.update', $proposal) }}" class="space-y-4">
                                @csrf
                                @method('PUT')
                                
                                <input type="hidden" name="status" value="{{ $proposal->status->value }}">

                                <div class="space-y-2">
                                    <label for="secretary_id" class="text-[11px] font-semibold text-text-secondary block uppercase tracking-wide">Tugaskan Sekretariat</label>
                                    <select name="secretary_id" id="secretary_id" required class="w-full bg-slate-50 border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%234a5568%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1em_1em] bg-[right_0.75rem_center] bg-no-repeat pr-8">
                                        <option value="" disabled selected>Pilih Sekretariat...</option>
                                        @foreach($secretaries as $sec)
                                            <option value="{{ $sec->id }}">
                                                {{ $sec->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-150 shadow-sm">
                                    Simpan Penugasan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Blok Modul Manajemen Pengelolaan Dokumen Ethical Clearance (EC) --}}
            @if($proposal->status->value === 'APPROVED')
                <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-4">
                    <h2 class="text-xl md:text-[20px] font-semibold text-text border-b border-border pb-2 leading-[1.4]">
                        Draft Ethical Clearance
                    </h2>

                    @if(session('success'))
                        <div class="p-3 bg-success/10 border border-success/20 text-success rounded-lg text-xs font-semibold">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->has('ec_number') || $errors->has('signatory_id'))
                        <div class="p-3 bg-danger/10 border border-danger/20 text-danger rounded-lg text-xs font-semibold">
                            {{ $errors->first('ec_number') ?: $errors->first('signatory_id') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.proposals.store-draft', $proposal) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="ec_number" class="block text-[11px] font-semibold text-text-secondary uppercase tracking-wide mb-1">Nomor EC</label>
                            <input type="text" name="ec_number" id="ec_number" value="{{ old('ec_number', $proposal->ec_number) }}" class="w-full bg-slate-50 border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors" placeholder="Contoh: EC/2026/001" required>
                        </div>

                        <div>
                            <label for="signatory_id" class="block text-[11px] font-semibold text-text-secondary uppercase tracking-wide mb-1">Ketua Penandatangan</label>
                            <select name="signatory_id" id="signatory_id" class="w-full bg-slate-50 border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors cursor-pointer" required>
                                <option value="">Pilih Ketua KEP</option>
                                @foreach($chairmen as $chairman)
                                    <option value="{{ $chairman->id }}" {{ old('signatory_id', $proposal->signatory_id) == $chairman->id ? 'selected' : '' }}>
                                        {{ $chairman->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-150 shadow-sm">
                            Simpan Draft EC
                        </button>
                    </form>
                </div>
            @elseif($proposal->ec_number)
                <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-4">
                    <h2 class="text-xl md:text-[20px] font-semibold text-text border-b border-border pb-2 leading-[1.4]">
                        Detail Ethical Clearance
                    </h2>
                    <div class="space-y-3 text-sm pt-1">
                        <div>
                            <p class="text-text-secondary text-xs font-semibold uppercase tracking-wide">Nomor EC</p>
                            <p class="font-semibold text-text mt-0.5 font-mono">{{ $proposal->ec_number }}</p>
                        </div>
                        <div>
                            <p class="text-text-secondary text-xs font-semibold uppercase tracking-wide">Penandatangan</p>
                            <p class="font-semibold text-text mt-0.5">{{ $proposal->signatory ? $proposal->signatory->name : '-' }}</p>
                        </div>
                        @if($proposal->confirmed_title)
                            <div>
                                <p class="text-text-secondary text-xs font-semibold uppercase tracking-wide">Judul Terkonfirmasi</p>
                                <p class="font-semibold text-text mt-0.5 font-serif text-xs leading-relaxed">{{ $proposal->confirmed_title }}</p>
                            </div>
                        @endif
                        @if($proposal->confirmed_researcher_name)
                            <div>
                                <p class="text-text-secondary text-xs font-semibold uppercase tracking-wide">Peneliti Terkonfirmasi</p>
                                <p class="font-semibold text-text mt-0.5">{{ $proposal->confirmed_researcher_name }}</p>
                            </div>
                        @endif
                        @if($proposal->signed_at)
                            <div>
                                <p class="text-text-secondary text-xs font-semibold uppercase tracking-wide">Ditandatangani Pada</p>
                                <p class="font-semibold text-text mt-0.5 font-mono text-xs">{{ $proposal->signed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Info Tambahan Penanda Waktu Log Aplikasi --}}
            <div class="card p-4 bg-slate-50 border border-border/80 rounded-2xl text-xs text-text-secondary space-y-2.5 shadow-inner">
                <div class="flex justify-between items-center">
                    <span class="font-medium">Dibuat:</span>
                    <span class="font-mono text-text font-semibold">{{ $proposal->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="font-medium">Update Terakhir:</span>
                    <span class="font-mono text-text font-semibold">{{ $proposal->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

        </div>

    </div>
</x-layouts.app>