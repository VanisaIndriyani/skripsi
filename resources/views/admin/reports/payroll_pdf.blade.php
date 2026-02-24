<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penggajian</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .company-name { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #D4AF37; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .amount { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">PT Putra Muara Sukses</div>
        <div>Laporan Rekapitulasi Gaji</div>
        <div>Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Periode Gaji</th>
                <th>Nama Karyawan</th>
                <th class="amount">Total Diterima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $row)
            <tr>
                <td>
                    @if($row->start_date && $row->end_date)
                        {{ \Carbon\Carbon::parse($row->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($row->end_date)->format('d M Y') }}
                    @else
                        {{ date('F Y', mktime(0, 0, 0, $row->month, 10)) }}
                    @endif
                </td>
                <td>{{ $row->user->name }}</td>
                <td class="amount">Rp {{ number_format($row->total_salary, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
