<!DOCTYPE html>
<html>

<head>
    <title>Rekap Shodaqah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
        }

        .period {
            text-align: center;
            margin-top: 10px;
            color: #504f4f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 4px;
            border: 1px solid #b8b5b5;
        }

        td {
            padding: 4px;
            border: 1px solid #c4c2c2;
        }

        th {
            font-size: 14px;
            background-color: #ddd7d7;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .amount {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h5 style="margin: 0;">DAFTAR NAMA DONATUR DARI WARGA JAMAAH</h5>
        <h5 style="margin: 0;">DAERAH KEDIRI SELATAN SATU DESA KRESEK KELOMPOK SATU</h5>
    </div>
    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') }} s/d
        {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th width="35%">NAMA</th>
                <th>PERSENAN</th>
                <th>JIMPITAN</th>
                <th>DAPUR PUSAT</th>
                <th>SHOD DAERAH</th>
                <th>SHOD KELOMPOK</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td
                        style="text-align: left; font-size: 14px; @if ($item->jumlah == 0) color: #b03737; @endif">
                        {{ $item->nama }}
                    </td>
                    <td class="amount">{{ number_format($item->persenan, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format($item->jimpitan, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format($item->dapur_pusat, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format($item->shodaqah_daerah, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format($item->shodaqah_kelompok, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">Total</td>
                <td class="amount">{{ number_format($data->sum('persenan'), 0, ',', '.') }}</td>
                <td class="amount">{{ number_format($data->sum('jimpitan'), 0, ',', '.') }}</td>
                <td class="amount">{{ number_format($data->sum('dapur_pusat'), 0, ',', '.') }}</td>
                <td class="amount">{{ number_format($data->sum('shodaqah_daerah'), 0, ',', '.') }}</td>
                <td class="amount">{{ number_format($data->sum('shodaqah_kelompok'), 0, ',', '.') }}</td>
                <td class="amount">{{ number_format($data->sum('jumlah'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
