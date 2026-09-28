<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 14pt; font-weight: bold;">Laporan Peramalan Bahan Baku (WMA)</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center;">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Tanggal Periode</th>
            <th>Nama Bahan Baku</th>
            <th>Penggunaan Aktual (Unit)</th>
            <th>Hasil Peramalan (Unit)</th>
            <th>Mean Absolute Error (MAE)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($forecasts as $index => $forecast)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $forecast->period_date->format('d/m/Y') }}</td>
                <td>{{ $forecast->ingredient->name ?? 'Unknown' }}</td>
                <td>{{ $forecast->actual_usage }}</td>
                <td>{{ $forecast->forecasted_amount }}</td>
                <td>{{ $forecast->mean_absolute_error }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
