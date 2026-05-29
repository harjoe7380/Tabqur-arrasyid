<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Tabungan Qurban - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        h1, h2, h3 { margin: 0 0 10px 0; }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-box {
            border: 1px solid #333;
            padding: 15px;
            width: 300px;
            float: right;
            background-color: #fafafa;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .print-btn {
            padding: 10px 20px;
            background: #1abc9c;
            color: white;
            border: none;
            cursor: pointer;
            margin-bottom: 20px;
            font-weight: bold;
        }
        @media print {
            .print-btn { display: none; }
            .print-btn, .hide-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="text-center mb-4 hide-print">
        <button onclick="window.print()" class="btn btn-primary px-4 py-2 me-2">🖨️ Cetak / Print Laporan</button>
        <a href="{{ route('admin.report', ['export' => 'pdf']) }}" class="btn btn-danger px-4 py-2 me-2">📄 Unduh PDF</a>
        <a href="{{ route('admin.report', ['export' => 'csv']) }}" class="btn btn-success px-4 py-2">📊 Export Excel/CSV</a>
    </div>

    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <h2>Laporan Keuangan Tabungan Qurban</h2>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    <h3>A. Rekapitulasi per Jamaah</h3>
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Jamaah</th>
                <th width="20%">Target Qurban</th>
                <th width="20%">Total Terkumpul</th>
                <th width="20%">Kekurangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $p->user->name }}<br><small>{{ $p->user->no_hp }}</small></td>
                <td class="text-right">Rp {{ number_format($p->target_amount, 0, ',', '.') }}</td>
                <td class="text-right"><strong>Rp {{ number_format($p->terkumpul, 0, ',', '.') }}</strong></td>
                <td class="text-right">
                    @if($p->kekurangan > 0)
                        <span style="color:red">Rp {{ number_format($p->kekurangan, 0, ',', '.') }}</span>
                    @else
                        <span style="color:green">Lunas</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="summary-box">
            <h3>B. Kas Tabungan</h3>
            <p>Total Seluruh Uang Terkumpul di Bendahara saat ini:</p>
            <h2 class="text-right">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</h2>
        </div>
    </div>

</body>
</html>
