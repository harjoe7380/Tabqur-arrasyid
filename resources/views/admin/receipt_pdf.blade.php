<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Setoran Tabungan Kurban</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 20px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0 0; color: #7f8c8d; }
        .content table { width: 100%; border-collapse: collapse; }
        .content table th, .content table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .footer { margin-top: 30px; text-align: center; color: #95a5a6; font-size: 12px; }
        .total-row td { font-weight: bold; font-size: 16px; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $dkm_name }}</h2>
        <p>{{ $dkm_address }} | WA Admin: {{ $admin_phone }}</p>
    </div>

    <h3 style="text-align: center;">TANDA TERIMA SETORAN KURBAN</h3>

    <div class="content">
        <table>
            <tr>
                <th width="35%">No. Transaksi</th>
                <td>#TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d F Y') }}</td>
            </tr>
            <tr>
                <th>Nama Jamaah</th>
                <td>{{ $transaction->participant->user->name }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $transaction->description ?? 'Setoran Tabungan Kurban' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td><strong style="color: green;">{{ strtoupper($transaction->status) }}</strong></td>
            </tr>
            <tr class="total-row">
                <th>Nominal Setoran</th>
                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Jazakumullah Khairan Katsiran. Semoga Allah menerima niat kurban Bpk/Ibu.</p>
        <p><em>Struk ini sah dan digenerate otomatis oleh Sistem Tabungan Kurban.</em></p>
    </div>
</body>
</html>
