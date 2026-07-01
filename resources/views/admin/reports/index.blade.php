<x-layouts.app :title="'Laporan & Statistik'">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Laporan & Statistik</h1>
            <p class="text-sm text-text-secondary mt-1.5">
                Periode: 
                @if(request('start_date') || request('end_date'))
                    {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->timezone('Asia/Jakarta')->format('d M Y') : 'Awal' }}
                    s.d
                    {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->timezone('Asia/Jakarta')->format('d M Y') : 'Sekarang' }}
                @else
                    Kumulatif Semua Data
                @endif
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('admin.reports.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white border border-border p-6 rounded-xl mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label for="q" class="block text-xs font-semibold text-text-secondary uppercase mb-2">Kata Kunci</label>
                <div class="relative w-full">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Judul, kode, nama..." class="input-field pl-9 pr-3 py-2 w-full text-sm bg-bg border border-border rounded-lg focus:bg-white">
                </div>
            </div>
            <div>
                <label for="status" class="block text-xs font-semibold text-text-secondary uppercase mb-2">Status</label>
                <select name="status" id="status" class="input-field py-2 px-3 w-full text-sm bg-bg border border-border rounded-lg focus:bg-white cursor-pointer">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\SubmissionStatus::cases() as $statusCase)
                        <option value="{{ $statusCase->value }}" {{ request('status') === $statusCase->value ? 'selected' : '' }}>
                            {{ $statusCase->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="type" class="block text-xs font-semibold text-text-secondary uppercase mb-2">Jenis Penelitian</label>
                <select name="type" id="type" class="input-field py-2 px-3 w-full text-sm bg-bg border border-border rounded-lg focus:bg-white cursor-pointer">
                    <option value="">Semua Jenis</option>
                    @if(isset($researchTypes))
                        @foreach($researchTypes as $rType)
                            <option value="{{ $rType }}" {{ request('type') === $rType ? 'selected' : '' }}>
                                {{ \Illuminate\Support\Str::title($rType) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label for="year" class="block text-xs font-semibold text-text-secondary uppercase mb-2">Tahun</label>
                <select name="year" id="year" class="input-field py-2 px-3 w-full text-sm bg-bg border border-border rounded-lg focus:bg-white cursor-pointer">
                    <option value="">Semua Tahun</option>
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="month" class="block text-xs font-semibold text-text-secondary uppercase mb-2">Bulan</label>
                <select name="month" id="month" class="input-field py-2 px-3 w-full text-sm bg-bg border border-border rounded-lg focus:bg-white cursor-pointer">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 sm:col-span-2 lg:col-span-5 flex justify-end gap-2 mt-2">
                @if(request()->anyFilled(['q', 'status', 'type', 'year', 'month']))
                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-soft-surface text-text-secondary text-sm font-semibold rounded-lg hover:bg-border transition-colors">
                        Reset Filter
                    </a>
                @endif
                <button type="submit" class="px-6 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-lg transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Ringkasan Statistik (4 KPI Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white border border-border p-6 rounded-xl flex flex-col justify-center">
            <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">Total Proposal</p>
            <p class="text-3xl font-bold text-text">{{ number_format($metrics['total']) }}</p>
        </div>
        <div class="bg-white border border-border p-6 rounded-xl flex flex-col justify-center">
            <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">Proposal Aktif</p>
            <p class="text-3xl font-bold text-primary">{{ number_format($metrics['active']) }}</p>
        </div>
        <div class="bg-white border border-border p-6 rounded-xl flex flex-col justify-center">
            <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">Proposal Selesai</p>
            <p class="text-3xl font-bold text-success">{{ number_format($metrics['done']) }}</p>
        </div>
        <div class="bg-white border border-border p-6 rounded-xl flex flex-col justify-center">
            <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">Proposal Ditolak</p>
            <p class="text-3xl font-bold text-danger">{{ number_format($metrics['rejected']) }}</p>
        </div>
    </div>

    {{-- Main Two Columns Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Kiri: Distribusi Status (Tabel) --}}
        <div class="lg:col-span-1 bg-white border border-border rounded-xl flex flex-col h-fit">
            <div class="px-6 py-4 border-b border-border bg-slate-50/70">
                <h2 class="text-[18px] font-semibold text-text">Distribusi Status</h2>
            </div>
            <div class="p-0">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-border">
                        @php
                            $targetStatuses = [
                                \App\Enums\SubmissionStatus::NEW_PROPOSAL,
                                \App\Enums\SubmissionStatus::PROCESS,
                                \App\Enums\SubmissionStatus::ON_REVIEW,
                                \App\Enums\SubmissionStatus::REVISION_REQUIRED,
                                \App\Enums\SubmissionStatus::REVISED,
                                \App\Enums\SubmissionStatus::APPROVED,
                                \App\Enums\SubmissionStatus::WAITING_SIGNATURE,
                                \App\Enums\SubmissionStatus::DONE,
                                \App\Enums\SubmissionStatus::REJECTED,
                            ];
                        @endphp
                        @foreach($targetStatuses as $statusCase)
                            @php
                                $count = $statusDistribution[$statusCase->value] ?? 0;
                            @endphp
                            <tr class="hover:bg-soft-surface/25 transition-colors">
                                <td class="px-6 py-3 font-semibold text-text-secondary">{{ $statusCase->label() }}</td>
                                <td class="px-6 py-3 font-bold text-text text-right">{{ number_format($count) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Kanan: Proposal Terbaru --}}
        <div class="lg:col-span-2 bg-white border border-border rounded-xl flex flex-col h-fit">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-slate-50/70">
                <h2 class="text-[18px] font-semibold text-text">10 Proposal Terbaru</h2>
                <a href="{{ route('admin.proposals.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/30">
                            <th class="px-6 py-4 font-semibold">Kode</th>
                            <th class="px-6 py-4 font-semibold">Judul</th>
                            <th class="px-6 py-4 font-semibold">Mahasiswa</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($latestSubmissions as $sub)
                            <tr class="hover:bg-soft-surface/25 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-text line-clamp-1" title="{{ $sub->title }}">
                                        {{ \Illuminate\Support\Str::title($sub->title) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-text-secondary truncate max-w-[140px]">{{ optional($sub->student)->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$sub->status" />
                                </td>
                                <td class="px-6 py-4 text-xs text-text-secondary whitespace-nowrap">{{ $sub->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-text-muted italic">Tidak ada proposal yang sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
