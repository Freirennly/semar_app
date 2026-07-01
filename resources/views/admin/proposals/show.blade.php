@php 
    // Mengamankan variabel tab untuk layout global sidebar/navbar
    $tab = $tab ?? 'details'; 
@endphp

<x-layouts.app :title="'Detail Pengajuan - ' . $proposal->code">
    {{-- Breadcrumb & Kembali --}}
    <div class="mb-6 animate-fade-in">
        <nav class="text-xs text-text-muted font-medium mb-2" aria-label="Breadcrumb">
            <a href="{{ route('admin.proposals.index') }}" class="hover:text-primary transition-colors">Manajemen Pengajuan</a> 
            <span class="mx-1">/</span> 
            <span class="text-text">{{ $proposal->code }}</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-text">{{ $proposal->title }}</h2>
                <p class="text-xs text-text-secondary mt-1 font-mono">
                    {{ $proposal->code }} · Kategori: {{ $proposal->type }}
                </p>
            </div>
            <div class="shrink-0">
                <x-status-badge :status="$proposal->status" />
            </div>
        </div>
    </div>



    {{-- LAYOUT UTAMA: 2 KOLOM PREMIUM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        {{-- KOLOM KIRI (2/3): Detail Berkas & Berkas Lampiran --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Kartu 1: Informasi Ringkasan / Abstrak Usulan --}}
            <div class="card p-6 bg-white border border-border rounded-2xl space-y-4">
                <h3 class="text-sm font-bold text-text uppercase tracking-wider border-b border-border pb-2">Informasi Usulan</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-text-secondary text-xs font-medium">Nama Pengaju (Mahasiswa)</dt>
                        <dd class="font-semibold text-text mt-0.5">{{ optional($proposal->student)->name ?? 'Unknown' }}</dd>
                    </div>
                    <div>
                        <dt class="text-text-secondary text-xs font-medium">NIM / NIP</dt>
                        <dd class="font-semibold text-text mt-0.5 font-mono">{{ optional($proposal->student)->nim_nip ?? '-' }}</dd>
                    </div>
                    <div class="col-span-1 sm:col-span-2">
                        <dt class="text-text-secondary text-xs font-medium">Abstrak / Ringkasan Penelitian</dt>
                        <dd class="text-text leading-relaxed mt-1 whitespace-pre-line bg-slate-50/50 p-4 rounded-xl border border-border/60">{{ $proposal->abstract ?? 'Tidak ada abstrak.' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Kartu 2: Berkas Lampiran Persyaratan Mahasiswa --}}
            <div class="card p-6 bg-white border border-border rounded-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3 flex-wrap gap-2">
                    <h3 class="text-sm font-bold text-text uppercase tracking-wider">Dokumen Lampiran Persyaratan</h3>
                    
                    {{-- Tombol Utama Unduh Berkas Proposal Asli (Menuju Fungsi downloadProposal Baru) --}}
                    <a href="{{ route('admin.proposals.download', $proposal) }}" class="inline-flex items-center gap-1.5 bg-primary text-white hover:bg-primary-hover px-3 py-1.5 rounded-xl text-xs font-bold transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh File Utama
                    </a>
                </div>

                {{-- Daftar Berkas Lampiran Hasil Loop Template Database --}}
                <div class="divide-y divide-border/60">
                    @forelse($proposal->documents as $doc)
                        <div class="py-3.5 flex items-center justify-between gap-4 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-text truncate">
                                    {{ optional($doc->template)->name ?? $doc->original_name }}
                                </p>
                                <p class="text-xs text-text-muted mt-0.5 font-mono">
                                    {{ $doc->original_name }} · {{ number_format($doc->size / 1024, 0) }} KB
                                </p>
                            </div>
                            <div class="shrink-0">
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($doc->file_path) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-text border border-border rounded-lg text-xs font-semibold transition-all">
                                    Lihat File
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-text-muted italic">
                            Belum ada dokumen persyaratan yang diunggah oleh mahasiswa ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (1/3): Panel Aksi Cepat Alur Kerja KEP --}}
        <div class="space-y-6">

            @if($proposal->status->value === 'NEW_PROPOSAL')
                <div class="card p-5 bg-white border border-border rounded-2xl space-y-4">
                    <h4 class="text-xs font-bold text-text uppercase tracking-wider border-b border-border pb-2">Penugasan Sekretariat</h4>
                    <form action="{{ route('admin.proposals.assign-secretary', $proposal) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="secretary_id" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Pilih Sekretariat</label>
                            <select name="secretary_id" id="secretary_id" class="w-full bg-slate-50 border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors" required>
                                <option value="">-- Pilih Sekretariat --</option>
                                @foreach($secretaries as $sec)
                                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-150">
                            Tetapkan Sekretariat
                        </button>
                    </form>
                </div>
            @endif
            
            <div class="card p-5 bg-white border border-border rounded-2xl space-y-4">
                <h4 class="text-xs font-bold text-text uppercase tracking-wider border-b border-border pb-2">Informasi Penugasan</h4>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-text-secondary text-xs font-medium">Sekretariat</p>
                        <p class="font-semibold text-text mt-0.5">
                            {{ $proposal->secretary ? $proposal->secretary->name : 'Belum Ditugaskan' }}
                        </p>
                    </div>
                    @if($proposal->secretary)
                    <div>
                        <p class="text-text-secondary text-xs font-medium">Status</p>
                        <p class="font-semibold text-success mt-0.5">Sudah Ditetapkan</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-text-secondary text-xs font-medium">Status Alur Kerja</p>
                        <div class="mt-1">
                            <x-status-badge :status="$proposal->status" />
                        </div>
                    </div>
                </div>
            </div>

            @if(in_array($proposal->status->value, ['APPROVED', 'WAITING_STUDENT_CONFIRMATION']))
                <div class="card p-5 bg-white border border-border rounded-2xl space-y-4">
                    <h4 class="text-xs font-bold text-text uppercase tracking-wider border-b border-border pb-2">Draft Ethical Clearance</h4>

                    @if(session('success'))
                        <div class="p-3 bg-success/10 border border-success/20 text-success rounded-lg text-xs font-semibold">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->has('ec_number') || $errors->has('signatory_id'))
                        <x-alert type="error" :message="$errors->first('ec_number') ?: $errors->first('signatory_id')" />
                    @endif

                    <form action="{{ route('admin.proposals.store-draft', $proposal) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="ec_number" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Nomor EC</label>
                            <input type="text" name="ec_number" id="ec_number" value="{{ old('ec_number', $proposal->ec_number) }}" class="w-full bg-slate-50 border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors {{ $isDraftReadonly ? 'opacity-70 cursor-not-allowed' : '' }}" placeholder="Contoh: EC/2026/001" {{ $isDraftReadonly ? 'readonly' : 'required' }}>
                        </div>

                        <div>
                            <label for="signatory_id" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Ketua Penandatangan</label>
                            <select name="signatory_id" id="signatory_id" class="w-full bg-slate-50 border border-border rounded-xl px-3 py-2 text-xs font-medium text-text focus:outline-none focus:border-primary transition-colors {{ $isDraftReadonly ? 'opacity-70 cursor-not-allowed' : '' }}" {{ $isDraftReadonly ? 'disabled' : 'required' }}>
                                <option value="">-- Pilih Ketua KEP --</option>
                                @foreach($chairmen as $chairman)
                                    <option value="{{ $chairman->id }}" {{ old('signatory_id', $proposal->signatory_id) == $chairman->id ? 'selected' : '' }}>
                                        {{ $chairman->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($isDraftReadonly && $proposal->signatory_id)
                                <input type="hidden" name="signatory_id" value="{{ $proposal->signatory_id }}">
                            @endif
                        </div>

                        @if(!$isDraftReadonly)
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" formaction="{{ route('admin.proposals.store-draft', $proposal) }}" class="w-full bg-slate-100 hover:bg-slate-200 text-text border border-border text-xs font-bold py-2.5 rounded-xl transition-all duration-150">
                                Simpan Draft
                            </button>
                            <button type="submit" formaction="{{ route('admin.proposals.send-draft', $proposal) }}" class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-150">
                                Kirim Draft ke Mahasiswa
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            @elseif($proposal->ec_number)
                <div class="card p-5 bg-white border border-border rounded-2xl space-y-4">
                    <h4 class="text-xs font-bold text-text uppercase tracking-wider border-b border-border pb-2">Detail Ethical Clearance</h4>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-text-secondary text-xs font-medium">Nomor EC</p>
                            <p class="font-semibold text-text mt-0.5">{{ $proposal->ec_number }}</p>
                        </div>
                        <div>
                            <p class="text-text-secondary text-xs font-medium">Penandatangan</p>
                            <p class="font-semibold text-text mt-0.5">{{ $proposal->signatory ? $proposal->signatory->name : '-' }}</p>
                        </div>
                        @if($proposal->confirmed_title)
                            <div>
                                <p class="text-text-secondary text-xs font-medium">Judul Terkonfirmasi</p>
                                <p class="font-semibold text-text mt-0.5">{{ $proposal->confirmed_title }}</p>
                            </div>
                        @endif
                        @if($proposal->confirmed_researcher_name)
                            <div>
                                <p class="text-text-secondary text-xs font-medium">Peneliti Terkonfirmasi</p>
                                <p class="font-semibold text-text mt-0.5">{{ $proposal->confirmed_researcher_name }}</p>
                            </div>
                        @endif
                        @if($proposal->signed_at)
                            <div>
                                <p class="text-text-secondary text-xs font-medium">Ditandatangani Pada</p>
                                <p class="font-semibold text-text mt-0.5">{{ $proposal->signed_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Info Tambahan Penanda Waktu Log --}}
            <div class="card p-4 bg-slate-50 border border-border/80 rounded-2xl text-xs text-text-secondary space-y-2">
                <div class="flex justify-between"><span>Dibuat:</span><span class="font-mono text-text font-medium">{{ $proposal->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span></div>
                <div class="flex justify-between"><span>Update Terakhir:</span><span class="font-mono text-text font-medium">{{ $proposal->updated_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span></div>
            </div>

        </div>

    </div>
</x-layouts.app>