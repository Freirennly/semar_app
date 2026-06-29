<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Sertifikat Ethical Clearance - SEMAR</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            box-sizing: border-box;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: none;
            overflow: hidden;
        }
        .card-header {
            background-color: #1e293b;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .card-header h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .card-header p {
            font-size: 12px;
            margin: 6px 0 0 0;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .card-body {
            padding: 32px;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-badge.valid {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .status-badge.invalid {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .details-list {
            margin-top: 16px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }
        .detail-item {
            margin-bottom: 18px;
        }
        .detail-item:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.4;
        }
        .footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>SEMAR UNIVERSITY</h1>
                <p>System Verification Portal</p>
            </div>
            
            <div class="card-body" style="text-align: center;">
                @if($isValid)
                    <div class="status-badge valid">
                        ✓ Validated / Terverifikasi
                    </div>
                    
                    <div style="text-align: left;" class="details-list">
                        <div class="detail-item">
                            <div class="detail-label">Nomor Sertifikat (EC Number)</div>
                            <div class="detail-value" style="font-family: monospace; font-size: 18px; color: #1e293b;">
                                {{ $submission->ec_number }}
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Judul Penelitian (Research Title)</div>
                            <div class="detail-value">{{ $submission->confirmed_title }}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Nama Peneliti (Researcher Name)</div>
                            <div class="detail-value">{{ $submission->confirmed_researcher_name }}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Penandatangan (Signatory / Ketua KEP)</div>
                            <div class="detail-value">{{ optional($submission->signatory)->name }}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Tanggal Ditandatangani (Signed Date)</div>
                            <div class="detail-value">{{ $submission->signed_at->format('d F Y') }}</div>
                        </div>
                    </div>
                @else
                    <div class="status-badge invalid">
                        ✗ Certificate Not Valid
                    </div>
                    
                    <div class="details-list" style="padding-top: 24px; color: #475569;">
                        <p style="font-weight: 600; font-size: 16px; margin: 0 0 10px 0;">Sertifikat Tidak Valid</p>
                        <p style="font-size: 14px; margin: 0; line-height: 1.5;">
                            {{ $message ?? 'Dokumen Ethical Clearance ini tidak ditemukan atau belum diterbitkan oleh sistem.' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} SEMAR - Universitas Gadjah Mada
        </div>
    </div>
</body>
</html>
