<x-layouts.app :title="'Dashboard Sekretariat'">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-start justify-between mb-6 gap-4">
        <div>
            <nav class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">
                Home <span class="mx-1">/</span> Dashboard
            </nav>
            <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Dashboard Sekretariat</h1>
            <p class="text-sm text-text-secondary mt-1">Kelola verifikasi dokumen persyaratan pengajuan dan rekomendasi akhir Komite Etik Penelitian.</p>
        </div>
        <div class="flex flex-col md:items-end gap-3">
            <div class="text-sm font-semibold text-text-secondary hidden md:block">
                {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('doccheck.index') }}" class="text-xs font-semibold text-text hover:text-primary px-3 py-1.5 border border-border rounded transition-colors bg-white hover:border-primary">
                    Cek Dokumen
                </a>
                <a href="{{ route('assignments.index') }}" class="text-xs font-semibold text-text hover:text-primary px-3 py-1.5 border border-border rounded transition-colors bg-white hover:border-primary">
                    Assign Reviewer
                </a>
                <a href="{{ route('decisions.index') }}" class="text-xs font-semibold text-text hover:text-primary px-3 py-1.5 border border-border rounded transition-colors bg-white hover:border-primary">
                    Putusan Akhir
                </a>
            </div>
        </div>
    </div>

    {{-- Ringkasan Tugas (Flat cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Perlu Cek Dokumen</p>
            <p class="text-3xl font-bold text-text leading-none">{{ $submitted->count() }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Perlu Assign Reviewer</p>
            <p class="text-3xl font-bold text-text leading-none">{{ $needAssign->count() }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-lg flex flex-col justify-center hover:border-primary/50 transition-colors duration-200">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-1">Menunggu Keputusan Akhir</p>
            <p class="text-3xl font-bold text-text leading-none">{{ $pendingDecision->count() }}</p>
        </div>
    </div>

    @php
        // Fetch recent validation history directly from database to avoid modifying controller
        $recentValidations = \App\Models\Submission::whereNotIn('status', [
            \App\Enums\SubmissionStatus::NEW_PROPOSAL,
            \App\Enums\SubmissionStatus::REVISED
        ])
        ->with('student')
        ->latest('updated_at')
        ->limit(6)
        ->get();
    @endphp

    {{-- Main Grid (70/30 Split) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Tasks (70% or lg:col-span-2) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Perlu Cek Dokumen --}}
            <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Perlu Cek Dokumen</h2>
                    <a href="{{ route('doccheck.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
                </div>
                @if($submitted->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada dokumen baru yang perlu divalidasi saat ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                                    <th class="px-5 py-3 font-semibold">Kode</th>
                                    <th class="px-5 py-3 font-semibold">Judul Usulan</th>
                                    <th class="px-5 py-3 font-semibold">Pengusul</th>
                                    <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-white">
                                @foreach($submitted as $sub)
                                    <tr class="hover:bg-soft-surface/50 transition-colors">
                                        <td class="px-5 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-5 py-3 font-semibold text-text truncate max-w-[200px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-5 py-3 text-text-secondary">{{ optional($sub->student)->name ?? '-' }}</td>
                                        <td class="px-5 py-3 text-right">
                                            <a href="{{ route('doccheck.show', $sub) }}" class="text-xs font-bold text-primary hover:underline">
                                                Cek Dokumen
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Perlu Assign Reviewer --}}
            <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Perlu Assign Reviewer</h2>
                    <a href="{{ route('assignments.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
                </div>
                @if($needAssign->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada pengajuan baru yang perlu di-assign reviewer.</p>
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
                                @foreach($needAssign as $sub)
                                    <tr class="hover:bg-soft-surface/50 transition-colors">
                                        <td class="px-5 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-5 py-3 font-semibold text-text truncate max-w-[200px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-5 py-3 text-text-secondary">{{ optional($sub->student)->name ?? '-' }}</td>
                                        <td class="px-5 py-3 text-right">
                                            <a href="{{ route('assignments.index') }}?focus={{ $sub->id }}" class="text-xs font-bold text-primary hover:underline">
                                                Assign
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Proposal Dalam Review --}}
            <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Proposal Dalam Review</h2>
                    <a href="{{ route('assignments.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
                </div>
                @if($assigned->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada proposal yang sedang aktif direview.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                                    <th class="px-5 py-3 font-semibold">Kode</th>
                                    <th class="px-5 py-3 font-semibold">Judul Usulan</th>
                                    <th class="px-5 py-3 font-semibold">Reviewer Ditugaskan</th>
                                    <th class="px-5 py-3 font-semibold text-right">Progress</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-white">
                                @foreach($assigned as $sub)
                                    <tr class="hover:bg-soft-surface/50 transition-colors">
                                        <td class="px-5 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-5 py-3 font-semibold text-text truncate max-w-[180px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-5 py-3 text-xs text-text-secondary">
                                            <div class="space-y-1">
                                                @foreach($sub->assignments as $asg)
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="truncate max-w-[110px]" title="{{ $asg->reviewer->name }}">{{ $asg->reviewer->name }}</span>
                                                        <span class="font-bold shrink-0 {{ $asg->status === 'COMPLETED' ? 'text-success' : 'text-warning' }}">
                                                            {{ $asg->status === 'COMPLETED' ? 'Selesai' : 'Belum' }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-right whitespace-nowrap">
                                            @php
                                                $totalAsg = $sub->assignments->count();
                                                $doneAsg = $sub->assignments->where('status', 'COMPLETED')->count();
                                            @endphp
                                            <span class="text-xs font-bold text-text">{{ $doneAsg }} / {{ $totalAsg }} Selesai</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Menunggu Keputusan --}}
            <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-bold text-text uppercase tracking-wider">Menunggu Keputusan Akhir</h2>
                    <a href="{{ route('decisions.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
                </div>
                @if($pendingDecision->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada pengajuan yang menunggu keputusan akhir.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[11px] uppercase tracking-wider border-b border-border bg-slate-50/50">
                                    <th class="px-5 py-3 font-semibold">Kode</th>
                                    <th class="px-5 py-3 font-semibold">Judul Usulan</th>
                                    <th class="px-5 py-3 font-semibold">Penilaian</th>
                                    <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-white">
                                @foreach($pendingDecision as $sub)
                                    <tr class="hover:bg-soft-surface/50 transition-colors">
                                        <td class="px-5 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-5 py-3 font-semibold text-text truncate max-w-[200px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-5 py-3 text-xs text-text-secondary">
                                            <span class="font-bold text-primary">{{ $sub->reviews->count() }}</span> Masuk
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <a href="{{ route('decisions.show', $sub) }}" class="text-xs font-bold text-primary hover:underline">
                                                Putuskan
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Recent Validation History (30% or lg:col-span-1) --}}
        <div class="bg-white border border-border rounded-lg flex flex-col overflow-hidden">
            <div class="px-5 py-4 border-b border-border">
                <h2 class="text-sm font-bold text-text uppercase tracking-wider">Riwayat Validasi</h2>
            </div>
            
            @if($recentValidations->isEmpty())
                <div class="text-center py-12 px-6">
                    <p class="text-xs text-text-muted italic">Belum ada riwayat validasi.</p>
                </div>
            @else
                <div class="divide-y divide-border overflow-y-auto max-h-[500px]">
                    @foreach($recentValidations as $sub)
                        <div class="p-4 hover:bg-soft-surface/20 transition-colors">
                            <p class="text-xs font-semibold text-text line-clamp-2">{{ \Illuminate\Support\Str::title($sub->title) }}</p>
                            <p class="text-[10px] text-text-secondary mt-1">Pengusul: {{ optional($sub->student)->name ?? '-' }}</p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-[10px] text-text-muted font-mono">{{ $sub->code }}</span>
                                <x-status-badge :status="$sub->status" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>