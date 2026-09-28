<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 14pt; font-weight: bold;">Laporan Penjualan Koriro POS</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Kode Pesanan</th>
            <th>Tanggal Transaksi</th>
            <th>Nama Pelanggan</th>
            <th>Status Pembayaran</th>
            <th>Status Pesanan</th>
            <th>Total Bayar (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grandTotal = 0;
        @endphp
        @foreach($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transaction->order_code }}</td>
                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $transaction->customer_name ?? '-' }}</td>
                <td>{{ ucfirst($transaction->payment_status) }}</td>
                <td>{{ ucfirst($transaction->order_status) }}</td>
                <td>{{ $transaction->total_amount }}</td>
            </tr>
            @php
                $grandTotal += $transaction->total_amount;
            @endphp
        @endforeach
        <tr>
            <td colspan="6" style="text-align: right; font-weight: bold;">Total Keseluruhan</td>
            <td style="font-weight: bold;">{{ $grandTotal }}</td>
        </tr>
    </tbody>
</table>
