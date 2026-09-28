<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0 0 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Penjualan Koriro POS</h2>
        <p>Periode: {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Kode Pesanan</th>
                <th width="15%">Waktu</th>
                <th width="15%">Pelanggan</th>
                <th width="15%">Pembayaran</th>
                <th width="15%">Pesanan</th>
                <th class="text-right" width="20%">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaction->order_code }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->customer_name ?? '-' }}</td>
                    <td>{{ ucfirst($transaction->payment_status) }}</td>
                    <td>{{ ucfirst($transaction->order_status) }}</td>
                    <td class="text-right">{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                </tr>
                @php $grandTotal += $transaction->total_amount; @endphp
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="6" class="text-right font-bold">Total Keseluruhan</td>
                <td class="text-right font-bold">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
