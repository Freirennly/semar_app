<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ethical Clearance Certificate</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 40px;
            color: #1a1a1a;
            background-color: #ffffff;
        }
        .border-outer {
            border: 4px double #1a365d;
            padding: 30px;
            height: 90%;
            min-height: 950px;
            box-sizing: border-box;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 26px;
            margin: 0;
            color: #1a365d;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .header p {
            font-size: 14px;
            margin: 5px 0 0 0;
            color: #4a5568;
            font-style: italic;
        }
        .title-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 40px;
        }
        .title-container h2 {
            font-size: 24px;
            margin: 0;
            color: #1a365d;
            text-decoration: underline;
            letter-spacing: 1px;
        }
        .title-container p {
            font-size: 16px;
            margin: 10px 0 0 0;
            font-weight: bold;
            color: #2d3748;
        }
        .content {
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 50px;
        }
        .content p {
            margin: 15px 0;
            text-align: justify;
        }
        .details-table {
            width: 100%;
            margin: 30px 0;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 8px 4px;
            vertical-align: top;
            font-size: 15px;
        }
        .details-table td.label {
            width: 30%;
            font-weight: bold;
            color: #2d3748;
        }
        .details-table td.colon {
            width: 3%;
        }
        .details-table td.value {
            width: 67%;
        }
        .footer-section {
            margin-top: 60px;
            position: absolute;
            bottom: 40px;
            left: 30px;
            right: 30px;
        }
        .qr-code-box {
            float: left;
            width: 40%;
            text-align: left;
        }
        .qr-code-box img {
            width: 110px;
            height: 110px;
            border: 1px solid #e2e8f0;
            padding: 4px;
            background: #fff;
        }
        .qr-code-box p {
            font-size: 10px;
            color: #718096;
            margin: 5px 0 0 0;
            width: 180px;
            line-height: 1.3;
        }
        .signature-box {
            float: right;
            width: 50%;
            text-align: center;
        }
        .signature-box .date {
            margin-bottom: 45px;
            font-size: 14px;
        }
        .signature-box .name {
            font-weight: bold;
            font-size: 16px;
            text-decoration: underline;
            margin: 0;
        }
        .signature-box .role {
            font-size: 14px;
            color: #4a5568;
            margin: 5px 0 0 0;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="border-outer">
        <div class="header">
            <h1>SEMAR UNIVERSITY</h1>
            <p>Komite Etik Penelitian Kesehatan (KEP) | Health Research Ethics Committee</p>
        </div>

        <div class="title-container">
            <h2>ETHICAL CLEARANCE CERTIFICATE</h2>
            <p>No: {{ $submission->ec_number }}</p>
        </div>

        <div class="content">
            <p>Komite Etik Penelitian Kesehatan (KEP) SEMAR University, setelah mempelajari proposal penelitian yang diajukan, dengan ini menyatakan bahwa penelitian berikut dinyatakan laik etik:</p>
            <p><em>The Health Research Ethics Committee (KEP) of SEMAR University, after reviewing the submitted research proposal, hereby declares that the following study is ethically approved:</em></p>

            <table class="details-table">
                <tr>
                    <td class="label">Judul Penelitian<br><small style="font-weight: normal; color: #718096; font-style: italic;">Research Title</small></td>
                    <td class="colon">:</td>
                    <td class="value" style="font-weight: bold; font-size: 16px; color: #1a365d;">{{ $submission->confirmed_title }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Peneliti<br><small style="font-weight: normal; color: #718096; font-style: italic;">Researcher Name</small></td>
                    <td class="colon">:</td>
                    <td class="value">{{ $submission->confirmed_researcher_name }}</td>
                </tr>
            </table>
        </div>

        <div class="footer-section clearfix">
            <div class="qr-code-box">
                <img src="{{ $qrCode }}" alt="QR Code Verification">
                <p>Pindai kode QR di atas untuk memverifikasi keabsahan sertifikat secara online melalui sistem SEMAR.<br><em>Scan QR code to verify online.</em></p>
            </div>

            <div class="signature-box">
                <div class="date">Yogyakarta, {{ $signedDate }}</div>
                <p class="name">{{ optional($submission->signatory)->name }}</p>
                <p class="role">Ketua KEP SEMAR University</p>
            </div>
        </div>
    </div>
</body>
</html>
