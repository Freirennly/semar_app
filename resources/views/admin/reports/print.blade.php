<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Statistik SEMAR - {{ date('d-m-Y') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            background: #ffffff;
            margin: 0;
            padding: 30px;
            font-size: 11px;
            line-height: 1.4;
        }
        .header-container {
            border-bottom: 3px double #000000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            text-align: center;
        }
        .institution-crest {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 2px;
            margin: 0;
            color: #0f172a;
        }
        .institution-sub {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #475569;
            margin: 3px 0 0 0;
        }
        .report-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            margin: 20px 0 5px 0;
            letter-spacing: 0.5px;
        }
        .report-period {
            text-align: center;
            font-size: 10px;
            color: #475569;
            margin-bottom: 25px;
        }
        .stats-grid {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        .stats-card {
            flex: 1;
            border: 1px solid #94a3b8;
            padding: 12px;
            border-radius: 4px;
            text-align: center;
        }
        .stats-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #475569;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .stats-value {
            font-size: 20px;
            font-weight: 800;
            margin-top: 4px;
            color: #0f172a;
        }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 3px;
            margin-bottom: 12px;
            margin-top: 25px;
            letter-spacing: 0.5px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .data-table th, .data-table td {
            border: 1px solid #94a3b8;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
        }
        .chart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .chart-table td {
            padding: 6px 0;
            border: none;
        }
        .chart-label {
            width: 30%;
            font-weight: 600;
            color: #334155;
        }
        .chart-bar-container {
            width: 60%;
            padding-right: 15px;
        }
        .chart-bar-outer {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            height: 10px;
            border-radius: 2px;
            overflow: hidden;
        }
        .chart-bar-inner {
            background-color: #0f172a;
            height: 100%;
        }
        .chart-value {
            width: 10%;
            text-align: right;
            font-weight: 700;
        }
        .footer-signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 220px;
            text-align: center;
        }
        .signature-title {
            font-size: 10px;
            font-weight: 600;
            color: #334155;
        }
        .signature-space {
            height: 60px;
            border-bottom: 1px solid #334155;
            margin-bottom: 5px;
            width: 160px;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-name {
            font-weight: 700;
            color: #0f172a;
        }
        .signature-nip {
            font-size: 9px;
            color: #475569;
        }
        .print-btn-container {
            text-align: right;
            margin-bottom: 20px;
        }
        .print-btn {
            background-color: #0f172a;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            font-size: 11px;
            font-family: inherit;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            @page {
                size: A4;
                margin: 20mm;
            }
            .page-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 8px;
                color: #64748b;
                border-top: 1px solid #cbd5e1;
                padding-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="print-btn-container no-print">
        <button onclick="window.print()" class="print-btn">Cetak Laporan / Simpan PDF</button>
    </div>

    {{-- Header Kop Resmi Akademik --}}
    <div class="header-container">
        <div class="institution-crest">SEMAR RESEARCH PLATFORM</div>
        <div class="institution-sub">LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT</div>
        <div class="institution-sub" style="font-weight: 400; margin-top: 1px;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</div>
    </div>

    <div class="report-title">LAPORAN ANALISIS DAN STATISTIK PENELITIAN</div>
    <div class="report-period">
        Periode: 
        @if($startDate || $endDate)
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Awal' }}
            s.d
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Sekarang' }}
        @else
            Kumulatif Semua Data
        @endif
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="section-title">I. Ringkasan Statistik Utama</div>
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-label">Total Pengajuan</div>
            <div class="stats-value">{{ number_format($metrics['total']) }}</div>
        </div>
        <div class="stats-card">
            <div class="stats-label">Total Selesai</div>
            <div class="stats-value" style="color: #16a34a;">{{ number_format($metrics['done']) }}</div>
        </div>
        <div class="stats-card">
            <div class="stats-label">Sedang Diproses</div>
            <div class="stats-value" style="color: #ea580c;">{{ number_format($metrics['processed']) }}</div>
        </div>
        <div class="stats-card">
            <div class="stats-label">Ditolak</div>
            <div class="stats-value" style="color: #dc2626;">{{ number_format($metrics['rejected']) }}</div>
        </div>
    </div>

    {{-- Analisis Visual / Grafik Status --}}
    <div class="section-title">II. Distribusi Status Pengajuan</div>
    <table class="chart-table">
        <tbody>
            @forelse($statusDistribution as $status => $count)
                @php
                    $enum = \App\Enums\SubmissionStatus::tryFrom($status);
                    $statusLabel = $enum ? $enum->label() : str_replace('_', ' ', $status);
                    $percent = $metrics['total'] > 0 ? ($count / $metrics['total'] * 100) : 0;
                @endphp
                <tr>
                    <td class="chart-label">{{ $statusLabel }}</td>
                    <td class="chart-bar-container">
                        <div class="chart-bar-outer">
                            <div class="chart-bar-inner" style="width: {{ $percent }}%"></div>
                        </div>
                    </td>
                    <td class="chart-value">{{ $count }} ({{ round($percent) }}%)</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #64748b; font-style: italic;">Tidak ada data status pengajuan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tren Keputusan --}}
    <div class="section-title">III. Tren Keputusan Akhir</div>
    <table class="chart-table">
        <tbody>
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
                    $percent = $totalDecisions > 0 ? ($count / $totalDecisions * 100) : 0;
                @endphp
                <tr>
                    <td class="chart-label">{{ $decLabel }}</td>
                    <td class="chart-bar-container">
                        <div class="chart-bar-outer">
                            <div class="chart-bar-inner" style="width: {{ $percent }}%"></div>
                        </div>
                    </td>
                    <td class="chart-value">{{ $count }} ({{ round($percent) }}%)</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Analisis Tren Bulanan --}}
    <div class="section-title" style="page-break-before: always;">IV. Tren Pengajuan per Bulan (6 Bulan Terakhir)</div>
    <table class="chart-table">
        <tbody>
            @forelse($monthlyTrend as $month => $count)
                @php
                    $maxCount = max(max($monthlyTrend), 1);
                    $percent = ($count / $maxCount) * 100;
                    $monthName = date('F Y', strtotime($month . '-01'));
                @endphp
                <tr>
                    <td class="chart-label">{{ $monthName }}</td>
                    <td class="chart-bar-container">
                        <div class="chart-bar-outer">
                            <div class="chart-bar-inner" style="width: {{ $percent }}%"></div>
                        </div>
                    </td>
                    <td class="chart-value">{{ $count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #64748b; font-style: italic;">Tidak ada data tren pengajuan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Aktivitas Reviewer --}}
    <div class="section-title">V. Aktivitas Penilaian Reviewer</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 70%;">Nama Reviewer</th>
                <th style="width: 30%;">Jumlah Review Diselesaikan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviewerStats as $rev)
                <tr>
                    <td><strong>{{ $rev->name }}</strong></td>
                    <td>{{ $rev->reviews_count }} Penilaian</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #64748b; font-style: italic;">Tidak ada data reviewer aktif.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tabel Detail --}}
    <div class="section-title" style="page-break-before: always;">VI. Tabel Detail Usulan Penelitian</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Kode</th>
                <th style="width: 45%;">Judul Usulan</th>
                <th style="width: 20%;">Nama Pengusul</th>
                <th style="width: 13%;">Status</th>
                <th style="width: 10%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allSubmissions as $sub)
                <tr>
                    <td style="font-family: monospace;">{{ $sub->code }}</td>
                    <td><strong>{{ \Illuminate\Support\Str::title($sub->title) }}</strong></td>
                    <td>{{ optional($sub->student)->name ?? '-' }}</td>
                    <td>{{ $sub->status ? \App\Enums\SubmissionStatus::tryFrom($sub->status->value ?? $sub->status)?->label() : '-' }}</td>
                    <td>{{ $sub->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b; font-style: italic;">Tidak ada data pengajuan dalam range filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan Resmi --}}
    <div class="footer-signatures">
        <div class="signature-box">
            <div class="signature-title">Dicetak Pada</div>
            <div style="font-weight: 600; margin-top: 5px;">{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">Penanggung Jawab Sistem SEMAR</div>
            <div class="signature-space"></div>
            <div class="signature-name">{{ auth()->user()->name }}</div>
            <div class="signature-nip">Administrator LPPM</div>
        </div>
    </div>

    {{-- Footer nomor halaman cetak --}}
    <div class="page-footer">
        Sistem SEMAR - Laporan Analisis Statistik Penelitian &copy; {{ date('Y') }}
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
