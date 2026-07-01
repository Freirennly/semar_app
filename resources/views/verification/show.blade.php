<x-layouts.landing title="Verifikasi Dokumen">
    <div class="py-20 px-6 max-w-2xl mx-auto min-h-[70vh] flex flex-col justify-center">
        @if($isValid)
            <div class="bg-white border border-success/30 rounded-2xl shadow-sm p-8 text-center space-y-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success/10 text-success mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                
                <div>
                    <h1 class="text-2xl font-bold text-success-dark">Ethical Clearance Valid</h1>
                    <p class="text-[14px] text-text-secondary mt-2">Dokumen Surat Kelayakan Etik (Ethical Clearance) ini tercatat resmi dalam sistem kami.</p>
                </div>

                <div class="bg-slate-50 border border-border rounded-xl p-6 text-left space-y-4 text-[14px]">
                    <div>
                        <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider">Nomor EC</p>
                        <p class="font-semibold text-text mt-0.5">{{ $submission->ec_number }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider">Judul Penelitian</p>
                        <p class="font-semibold text-text mt-0.5 leading-relaxed">{{ $submission->confirmed_title }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider">Nama Peneliti</p>
                        <p class="font-semibold text-text mt-0.5">{{ $submission->confirmed_researcher_name }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider">Institusi</p>
                        <p class="font-semibold text-text mt-0.5">{{ optional($submission->student)->institution ?? 'Universitas' }}</p>
                    </div>
                    <hr class="border-border">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider">Ketua Komite Etik</p>
                            <p class="font-semibold text-text mt-0.5">{{ optional($submission->signatory)->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider">Status Dokumen</p>
                            <p class="font-semibold text-success-dark mt-0.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Sah
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider">Tanggal Disahkan / Terbit</p>
                            <p class="font-semibold text-text mt-0.5">{{ $submission->signed_at ? \Carbon\Carbon::parse($submission->signed_at)->translatedFormat('d F Y') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-border mt-6 text-left flex justify-between items-center text-[11px] text-text-secondary">
                    <div>
                        <span class="block uppercase tracking-wider font-bold">Waktu Verifikasi</span>
                        <span>{{ $verificationDate }}</span>
                    </div>
                    <div class="text-right">
                        <span class="block uppercase tracking-wider font-bold">ID Verifikasi Publik</span>
                        <span class="font-mono">{{ $verificationId }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white border border-danger/30 rounded-2xl shadow-sm p-8 text-center space-y-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-danger/10 text-danger mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                
                <div>
                    <h1 class="text-2xl font-bold text-danger-dark">Dokumen Tidak Valid</h1>
                    <p class="text-[14px] text-text-secondary mt-2">{{ $message ?? 'Surat Kelayakan Etik (Ethical Clearance) tidak ditemukan atau tidak sah.' }}</p>
                </div>

                <div class="pt-6">
                    <a href="{{ route('landing') }}" class="inline-flex justify-center w-full px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-colors">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layouts.landing>
