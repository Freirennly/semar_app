<x-layouts.app :title="'Laporan & Statistik'">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-12 animate-fade-in">
        <div>
            <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3]">Laporan & Statistik</h1>
            <p class="text-sm font-medium text-text-secondary mt-2 leading-[1.2]">
                Periode: 
                @if(request('start_date') || request('end_date'))
                    {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'Awal' }}
                    s.d
                    {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Sekarang' }}
                @else
                    Kumulatif Semua Data
                @endif
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('admin.reports.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white border border-border p-6 rounded-xl mb-12">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label for="start_date" class="block text-xs font-semibold text-text-secondary uppercase mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="input-field py-2 px-3 w-full text-sm bg-bg border border-border rounded-lg focus:bg-white">
            </div>
            <div>
                <label for="end_date" class="block text-xs font-semibold text-text-secondary uppercase mb-2">Tanggal Selesai</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="input-field py-2 px-3 w-full text-sm bg-bg border border-border rounded-lg focus:bg-white">
            </div>
            <div>
                <label for="status" class="block text-xs font-semibold text-text-secondary uppercase mb-2">Filter Status</label>
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
                <label for="q" class="block text-xs font-semibold text-text-secondary uppercase mb-2">Kata Kunci</label>
                <div class="relative w-full">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Cari judul, kode..." class="input-field pl-9 pr-3 py-2 w-full text-sm bg-bg border border-border rounded-lg focus:bg-white">
                </div>
            </div>
            <div class="col-span-1 sm:col-span-2 lg:col-span-4 flex justify-end gap-2 mt-2">
                @if(request('start_date') || request('end_date') || request('status') || request('q'))
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

    {{-- Ringkasan Statistik (4 cards, flat) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white border border-border p-6 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Total Pengajuan</p>
            <p class="text-[32px] font-bold text-text mt-2 leading-none">{{ number_format($metrics['total']) }}</p>
        </div>
        <div class="bg-white border border-border p-6 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Total Selesai</p>
            <p class="text-[32px] font-bold text-success mt-2 leading-none">{{ number_format($metrics['done']) }}</p>
        </div>
        <div class="bg-white border border-border p-6 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Sedang Diproses</p>
            <p class="text-[32px] font-bold text-warning mt-2 leading-none">{{ number_format($metrics['processed']) }}</p>
        </div>
        <div class="bg-white border border-border p-6 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Ditolak</p>
            <p class="text-[32px] font-bold text-danger mt-2 leading-none">{{ number_format($metrics['rejected']) }}</p>
        </div>
    </div>

    {{-- Analisis Visual Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6 mb-12">
        {{-- Kiri: Tren Pengajuan (Line Chart) & Distribusi Status (70% or lg:col-span-7) --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Tren Pengajuan per Bulan (Grafik Garis Sederhana SVG) --}}
            <div class="bg-white p-6 border border-border rounded-xl">
                <h2 class="text-[24px] font-semibold text-text mb-6">Tren Pengajuan per Bulan</h2>
                @if(count($monthlyTrend) > 0)
                    @php
                        $maxCount = max(max($monthlyTrend), 1);
                        $points = [];
                        $i = 0;
                        $totalPoints = count($monthlyTrend);
                        foreach ($monthlyTrend as $month => $count) {
                            $x = $totalPoints > 1 ? ($i / ($totalPoints - 1)) * 340 + 30 : 200;
                            $y = 120 - ($count / $maxCount) * 80;
                            $points[] = "$x,$y";
                            $i++;
                        }
                        $polylinePoints = implode(' ', $points);
                    @endphp
                    <div class="w-full">
                        <svg viewBox="0 0 400 150" class="w-full h-auto">
                            <!-- Grid Lines -->
                            <line x1="30" y1="40" x2="370" y2="40" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="4" />
                            <line x1="30" y1="80" x2="370" y2="80" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="4" />
                            <line x1="30" y1="120" x2="370" y2="120" stroke="#9ca3af" stroke-width="1.5" />

                            <!-- Line Path -->
                            @if(count($points) > 1)
                                <polyline fill="none" stroke="#463ee3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" points="{{ $polylinePoints }}" />
                            @endif

                            <!-- Data Circles and Labels -->
                            @php $i = 0; @endphp
                            @foreach($monthlyTrend as $month => $count)
                                @php
                                    $coords = explode(',', $points[$i]);
                                    $monthName = date('M Y', strtotime($month . '-01'));
                                    $i++;
                                @endphp
                                <circle cx="{{ $coords[0] }}" cy="{{ $coords[1] }}" r="4" fill="#463ee3" stroke="#ffffff" stroke-width="1.5" />
                                <text x="{{ $coords[0] }}" y="{{ $coords[1] - 8 }}" font-size="9" font-weight="bold" fill="#463ee3" text-anchor="middle">{{ $count }}</text>
                                <text x="{{ $coords[0] }}" y="138" font-size="8" fill="#6b7280" text-anchor="middle">{{ $monthName }}</text>
                            @endforeach
                        </svg>
                    </div>
                @else
                    <div class="h-40 flex items-center justify-center text-xs text-text-muted italic">Tidak ada data tren pengajuan.</div>
                @endif
            </div>

            {{-- Distribusi Status --}}
            <div class="bg-white p-6 border border-border rounded-xl">
                <h2 class="text-[24px] font-semibold text-text mb-4">Distribusi Status</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($statusDistribution as $status => $count)
                        @php
                            $enum = \App\Enums\SubmissionStatus::tryFrom($status);
                            $statusLabel = $enum ? $enum->label() : str_replace('_', ' ', $status);
                            $percent = $metrics['total'] > 0 ? ($count / $metrics['total'] * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-bold text-text-secondary">{{ $statusLabel }}</span>
                                <span class="font-extrabold text-primary">{{ $count }} ({{ round($percent) }}%)</span>
                            </div>
                            <div class="w-full bg-bg rounded-full h-2 overflow-hidden border border-border">
                                <div class="bg-primary h-full rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-text-muted text-center py-4 italic col-span-2">Belum ada data status.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Kanan: Tren Keputusan (Bar Chart) & Aktivitas Reviewer (30% or lg:col-span-1) --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Tren Keputusan (Grafik Batang Sederhana) --}}
            <div class="bg-white p-6 border border-border rounded-xl">
                <h2 class="text-[24px] font-semibold text-text mb-4">Tren Keputusan</h2>
                <div class="space-y-4">
                    @php
                        $totalDecisions = array_sum($decisionStats);
                    @endphp
                    @foreach($decisionStats as $dec => $count)
                        @php
                            $decLabel = match($dec) {
                                'APPROVED' => 'Disetujui',
                                'APPROVED_WITH_REVISION' => 'Disetujui dengan Revisi',
                                'REJECTED' => 'Ditolak',
                            };
                            $colorClass = match($dec) {
                                'APPROVED' => 'bg-green-600',
                                'APPROVED_WITH_REVISION' => 'bg-orange-500',
                                'REJECTED' => 'bg-red-600',
                            };
                            $percent = $totalDecisions > 0 ? ($count / $totalDecisions) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-text-secondary">{{ $decLabel }}</span>
                                <span class="font-bold text-text">{{ $count }} ({{ round($percent) }}%)</span>
                            </div>
                            <div class="w-full bg-bg rounded-full h-2 overflow-hidden border border-border">
                                <div class="{{ $colorClass }} h-full rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Aktivitas Reviewer --}}
            <div class="bg-white p-6 border border-border rounded-xl">
                <h2 class="text-[24px] font-semibold text-text mb-4">Aktivitas Reviewer</h2>
                <div class="space-y-3">
                    @forelse($reviewerStats as $rev)
                        <div class="flex items-center justify-between text-xs p-2.5 bg-bg border border-border rounded-lg">
                            <span class="font-bold text-text-secondary truncate max-w-[160px]">{{ $rev->name }}</span>
                            <span class="font-mono text-primary font-bold">{{ $rev->reviews_count }} Penilaian</span>
                        </div>
                    @empty
                        <p class="text-xs text-text-muted italic text-center py-4">Tidak ada data reviewer aktif.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Detail --}}
    <div class="bg-white border border-border rounded-xl overflow-hidden mb-12">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between mb-4">
            <h2 class="text-[24px] font-semibold text-text leading-[1.4]">Daftar Detail Pengajuan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-border bg-slate-50/70">
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] w-24">Kode</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Judul Usulan</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Pengusul</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Status</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Tanggal Diajukan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($paginatedSubmissions as $sub)
                        <tr class="hover:bg-soft-surface/25 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                            <td class="px-6 py-4">
                                <span class="font-serif text-text" title="{{ $sub->title }}">
                                    {{ $sub->title }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-text-secondary">{{ optional($sub->student)->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$sub->status" />
                            </td>
                            <td class="px-6 py-4 text-xs text-text-secondary whitespace-nowrap">{{ $sub->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-text-muted italic">Tidak ada data pengajuan dalam filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($paginatedSubmissions->hasPages())
            <div class="px-6 py-4 border-t border-border bg-slate-50/50">
                {{ $paginatedSubmissions->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
