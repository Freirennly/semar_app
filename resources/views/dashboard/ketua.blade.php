<x-layouts.app :title="'Dashboard Ketua'">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-start justify-between mb-6 gap-4">
        <div>
            <nav class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">
                Home <span class="mx-1">/</span> Dashboard
            </nav>
            <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Dashboard Ketua</h1>
            <p class="text-sm text-text-secondary mt-1">Kelola penetapan penugasan reviewer dan pemantauan keputusan etik penelitian KEP.</p>
        </div>
        <div class="flex flex-col md:items-end gap-3">
            <div class="text-sm font-semibold text-text-secondary hidden md:block">
                {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('fullboard.index') }}" class="text-xs font-semibold text-text hover:text-primary px-3 py-1.5 border border-border rounded transition-colors bg-white hover:border-primary">
                    Jadwal Fullboard
                </a>
            </div>
        </div>
    </div>

    @php
        // Fetch decision stats and waiting decision list directly
        $totalApproved = \App\Models\Submission::whereIn('status', [
            \App\Enums\SubmissionStatus::APPROVED,
            \App\Enums\SubmissionStatus::DONE
        ])->count();

        $totalRejected = \App\Models\Submission::where('status', \App\Enums\SubmissionStatus::REJECTED)->count();

        $waitingDecision = \App\Models\Submission::where('status', \App\Enums\SubmissionStatus::ON_REVIEW)
            ->has('reviews')
            ->with('student', 'reviews')
            ->latest()
            ->limit(5)
            ->get();
    @endphp

    {{-- Ringkasan Statistik Keputusan & Tugas (Flat cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Menunggu Tanda Tangan</p>
            <p class="text-3xl font-bold text-text leading-none">{{ $waitingSignature->count() }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Total Pengajuan Aktif</p>
            <p class="text-3xl font-bold text-text leading-none">
                {{ \App\Models\Submission::whereNotIn('status', [\App\Enums\SubmissionStatus::REJECTED, \App\Enums\SubmissionStatus::DONE])->count() }}
            </p>
        </div>
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Keputusan (Disetujui / Ditolak)</p>
            <p class="text-3xl font-bold text-text leading-none">
                <span>{{ $totalApproved }}</span>
                <span class="text-text-muted mx-1">/</span>
                <span>{{ $totalRejected }}</span>
            </p>
        </div>
    </div>

    {{-- Main Grid (70/30 Split) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Tasks (70% or lg:col-span-2) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Waiting for Signature --}}
            <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-white flex items-center justify-between">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Waiting for Signature</h2>
                </div>
                @if($waitingSignature->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada pengajuan menunggu tanda tangan saat ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                                    <th class="px-5 py-3 font-semibold">Kode</th>
                                    <th class="px-5 py-3 font-semibold">Judul Usulan</th>
                                    <th class="px-5 py-3 font-semibold">Pengaju</th>
                                    <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-white">
                                @foreach($waitingSignature as $sub)
                                    <tr class="hover:bg-soft-surface/50 transition-colors">
                                        <td class="px-5 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-5 py-3 font-semibold text-text truncate max-w-[200px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-5 py-3 text-text-secondary">{{ optional($sub->student)->name ?? '-' }}</td>
                                        <td class="px-5 py-3 text-right">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('submissions.show', $sub) }}" class="text-[11px] font-bold text-text-secondary hover:text-primary transition-colors" title="Detail Pengajuan">Detail</a>
                                                <a href="{{ route('submissions.preview-final', $sub) }}" target="_blank" class="text-[11px] font-bold text-text-secondary hover:text-primary transition-colors" title="Preview Final PDF">Preview</a>
                                                <form action="{{ route('submissions.sign', $sub) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="button" class="text-[11px] font-bold px-2.5 py-1 rounded-md bg-primary/10 text-primary hover:bg-primary/20 transition-colors" onclick="event.preventDefault(); window.confirmModal('Tandatangani Surat Kelayakan Etik (Ethical Clearance)?', this.closest('form'));">
                                                        Tandatangani
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Recently Signed Certificates --}}
            <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-white flex items-center justify-between">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Surat Kelayakan Etik (Ethical Clearance) yang Baru Ditandatangani</h2>
                </div>
                @if(!isset($recentlySigned) || $recentlySigned->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Belum ada Surat Kelayakan Etik (Ethical Clearance) yang ditandatangani baru-baru ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                                    <th class="px-5 py-3 font-semibold">Kode</th>
                                    <th class="px-5 py-3 font-semibold">Judul Usulan</th>
                                    <th class="px-5 py-3 font-semibold">Peneliti</th>
                                    <th class="px-5 py-3 font-semibold text-right">Unduh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-white">
                                @foreach($recentlySigned as $sub)
                                    <tr class="hover:bg-soft-surface/50 transition-colors">
                                        <td class="px-5 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-5 py-3 font-semibold text-text truncate max-w-[200px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-5 py-3 text-text-secondary">{{ optional($sub->student)->name ?? '-' }}</td>
                                        <td class="px-5 py-3 text-right">
                                            @if($sub->ec_certificate_path)
                                                <a href="{{ route('submissions.certificate', $sub) }}" class="text-xs font-bold text-success hover:underline">
                                                    Unduh Surat Kelayakan Etik
                                                </a>
                                            @else
                                                <span class="text-xs text-text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Certificates Verified --}}
            <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-white flex items-center justify-between">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Surat Kelayakan Etik (Ethical Clearance) Terverifikasi</h2>
                </div>
                @if(!isset($verifiedLogs) || $verifiedLogs->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Belum ada riwayat verifikasi publik baru-baru ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                                    <th class="px-5 py-3 font-semibold">Kode</th>
                                    <th class="px-5 py-3 font-semibold">Judul Usulan</th>
                                    <th class="px-5 py-3 font-semibold">Detail Log Verifikasi</th>
                                    <th class="px-5 py-3 font-semibold text-right">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-white">
                                @foreach($verifiedLogs as $log)
                                    <tr class="hover:bg-soft-surface/50 transition-colors">
                                        <td class="px-5 py-3 font-mono text-xs text-text-secondary">
                                            {{ $log->submission ? $log->submission->code : 'N/A' }}
                                        </td>
                                        <td class="px-5 py-3 font-semibold text-text truncate max-w-[200px]" title="{{ $log->submission ? $log->submission->title : '' }}">
                                            {{ $log->submission ? \Illuminate\Support\Str::title($log->submission->title) : 'Unknown Submission' }}
                                        </td>
                                        <td class="px-5 py-3 text-xs text-text-secondary">
                                            {{ $log->description }}
                                        </td>
                                        <td class="px-5 py-3 text-right text-xs text-text-secondary whitespace-nowrap">
                                            {{ $log->created_at->timezone('Asia/Jakarta')->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Waiting Final Decision (30% or lg:col-span-1) --}}
        <div class="space-y-6">
            <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Menunggu Keputusan</h2>
                </div>
                
                @if($waitingDecision->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada pengajuan menunggu keputusan komite.</p>
                    </div>
                @else
                    <div class="divide-y divide-border overflow-y-auto max-h-[350px]">
                        @foreach($waitingDecision as $sub)
                            <div class="p-4 hover:bg-soft-surface/20 transition-colors">
                                <p class="text-xs font-semibold text-text line-clamp-2">{{ \Illuminate\Support\Str::title($sub->title) }}</p>
                                <div class="flex items-center justify-between mt-3 text-[10px] text-text-secondary">
                                    <span class="font-mono">{{ $sub->code }}</span>
                                    <span class="font-bold text-primary">{{ $sub->reviews->count() }} Review Masuk</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Statistik Keputusan Visual --}}
            <div class="bg-white p-5 border border-border rounded-lg flex flex-col justify-center">
                <h2 class="text-sm font-bold text-text uppercase tracking-wider mb-4 border-b border-border pb-4">Statistik Keputusan</h2>
                <div class="space-y-4">
                    @php
                        $totalAll = max($totalApproved + $totalRejected, 1);
                        $approvedPercent = ($totalApproved / $totalAll) * 100;
                        $rejectedPercent = ($totalRejected / $totalAll) * 100;
                    @endphp
                    <div class="flex items-center justify-between text-sm py-2 border-b border-border">
                        <span class="font-semibold text-text-secondary">Disetujui</span>
                        <span class="font-bold text-text">{{ $totalApproved }} ({{ round($approvedPercent) }}%)</span>
                    </div>
                    <div class="flex items-center justify-between text-sm py-2">
                        <span class="font-semibold text-text-secondary">Ditolak</span>
                        <span class="font-bold text-text">{{ $totalRejected }} ({{ round($rejectedPercent) }}%)</span>
                    </div>
                </div>
            </div>
            
            {{-- Jadwal Fullboard Widget --}}
            <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-white flex justify-between items-center">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Fullboard Mendatang</h2>
                    <a href="{{ route('fullboard.index') }}" class="text-xs text-primary hover:underline font-bold">Lihat Semua</a>
                </div>
                
                @if($upcomingFullboard->isEmpty())
                    <div class="text-center py-8 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada jadwal Fullboard.</p>
                    </div>
                @else
                    <div class="divide-y divide-border overflow-y-auto max-h-[400px]">
                        @foreach($upcomingFullboard as $fb)
                            <div class="p-4 hover:bg-soft-surface/20 transition-colors">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-xs font-semibold text-text line-clamp-1 flex-1">{{ \Illuminate\Support\Str::title($fb->submission->title) }}</p>
                                    @if($fb->meeting_type === 'OFFLINE')
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">OFFLINE</span>
                                    @elseif($fb->meeting_type === 'ONLINE')
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">ONLINE</span>
                                    @else
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">HYBRID</span>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-1 mt-2 text-[11px] text-text-secondary">
                                    <div class="flex items-center justify-between">
                                        <span>{{ $fb->scheduled_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</span>
                                        
                                        <div class="flex items-center gap-2">
                                            @if($fb->meeting_type === 'OFFLINE' || $fb->meeting_type === 'HYBRID')
                                                @if($fb->location)
                                                    <span class="text-text-secondary font-medium">{{ $fb->location }}</span>
                                                @endif
                                            @endif
                                            
                                            @if($fb->meeting_type === 'ONLINE' || $fb->meeting_type === 'HYBRID')
                                                @if($fb->meeting_url)
                                                    <a href="{{ $fb->meeting_url }}" target="_blank" class="text-primary hover:underline font-bold flex items-center gap-1">
                                                        Buka Meeting
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
