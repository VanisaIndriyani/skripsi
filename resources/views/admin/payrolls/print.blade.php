<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji - {{ $payroll->user->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .container { width: 100%; padding: 20px; }
        .header { 
            border-bottom: 3px solid #D4AF37; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
            display: table;
            width: 100%;
        }
        .logo-container {
            display: table-cell;
            vertical-align: middle;
            width: 80px;
        }
        .company-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 20px;
        }
        .company-name { 
            font-size: 24px; 
            font-weight: bold; 
            color: #D4AF37; 
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .company-address { font-size: 12px; color: #666; }
        
        .slip-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 30px;
            letter-spacing: 2px;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .details-table { width: 100%; margin-bottom: 30px; }
        .details-table td { padding: 5px; }
        .label { font-weight: bold; width: 150px; }

        .earnings-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .earnings-table th, .earnings-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .earnings-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .amount { text-align: right; }

        .total-row {
            background-color: #D4AF37;
            color: white;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 14px;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            margin-top: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            margin-top: 60px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <!-- Use absolute path for DOMPDF -->
                <img src="{{ public_path('img/logo.jpeg') }}" width="80" height="80" style="border-radius: 50%; object-fit: cover;">
            </div>
            <div class="company-info">
                <div class="company-name">PT Putra Muara Sukses</div>
                <div class="company-address">Jl. Raya Industri No. 123, Jakarta Utara, Indonesia<br>Telp: (021) 555-0123 | Email: admin@pms-group.com</div>
            </div>
        </div>

        <div class="slip-title">Slip Gaji Karyawan</div>

        <table class="details-table">
            <tr>
                <td class="label">Nama Karyawan</td>
                <td>: {{ $payroll->user->name }}</td>
                <td class="label">Periode</td>
                <td>: 
                    @if($payroll->start_date && $payroll->end_date)
                        {{ \Carbon\Carbon::parse($payroll->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($payroll->end_date)->format('d M Y') }}
                    @else
                        {{ date('F Y', mktime(0, 0, 0, $payroll->month, 10)) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td>: {{ date('d F Y') }}</td>
            </tr>
        </table>

        <table class="earnings-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="amount">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Upah Sesi Kerja</td>
                    <td class="amount">Rp {{ number_format($payroll->allowances, 0, ',', '.') }}</td>
                </tr>
                @if($payroll->deductions > 0)
                <tr>
                    <td style="color: red;">Potongan Lain-lain</td>
                    <td class="amount" style="color: red;">- Rp {{ number_format($payroll->deductions, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>TOTAL DITERIMA</td>
                    <td class="amount">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <div class="signature-box">
                <div>Jakarta, {{ date('d F Y') }}</div>
                <div>Disetujui Oleh,</div>
                <div class="signature-line"></div>
                <div><strong>Manager Keuangan</strong></div>
            </div>
        </div>
    </div>
</body>
</html>
