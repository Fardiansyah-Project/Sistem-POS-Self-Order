<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peramalan WMA</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Peramalan Bahan Baku (WMA)</h2>
        <p>Periode: {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Tanggal Periode</th>
                <th width="30%">Nama Bahan Baku</th>
                <th class="text-right" width="15%">Penggunaan Aktual</th>
                <th class="text-right" width="15%">Hasil Peramalan</th>
                <th class="text-right" width="20%">MAE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($forecasts as $index => $forecast)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $forecast->period_date->format('d/m/Y') }}</td>
                    <td>{{ $forecast->ingredient->name ?? 'Unknown' }}</td>
                    <td class="text-right">{{ $forecast->actual_usage }} {{ $forecast->ingredient->unit ?? '' }}</td>
                    <td class="text-right">{{ $forecast->forecasted_amount }} {{ $forecast->ingredient->unit ?? '' }}</td>
                    <td class="text-right">{{ $forecast->mean_absolute_error }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data peramalan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
