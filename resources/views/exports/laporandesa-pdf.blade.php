{{-- filepath: resources/views/exports/laporan-pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Setoran Kelompok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 6px 4px;
            border: 1px solid #b8b5b5;
        }

        th {
            background: #e2e2e2;
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .bg-success {
            background-color: #198754;
            color: white;
            padding: 8px;
        }

        .ttd-table {
            width: 100%;
            border: none;
            margin-top: 30px;
        }

        .ttd-table td {
            border: none;
            text-align: center;
            padding: 4px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3 style="margin:0;">LAPORAN SETORAN KEUANGAN KELOMPOK KRESEK 1</h3>
        <h3 style="margin:0;">BULAN {{ strtoupper(\Carbon\Carbon::parse($tanggal_awal)->locale('id')->translatedFormat('F Y')) }}</h3>
    </div>

    {{--  <table style="border:none; margin-bottom: 20px;">
        <tr>
            <td style="border:none; width: 100px;">KELOMPOK</td>
            <td style="border:none; width: 10px;">:</td>
            <td style="border:none;">KRESEK 1</td>
        </tr>
        <tr>
            <td style="border:none;">BULAN</td>
            <td style="border:none;">:</td>
            <td style="border:none;">
                {{ strtoupper(\Carbon\Carbon::parse($tanggal_awal)->locale('id')->translatedFormat('F Y')) }}
            </td>
        </tr>
    </table>  --}}

    <div class="card">
        

        <table>
            <thead>
                <tr>
                    <th style="width: 2%">No</th>
                    <th>Uraian</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jatah as $key => $row)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ $row->keterangan }}</td>
                        <td class="text-end">
                            Rp {{ number_format($row->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-end">Total</th>
                    <th class="text-end">
                        Rp {{ number_format($total_jatah, 0, ',', '.') }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Tanda Tangan -->
    <table class="ttd-table">
        <tr>
            <td style="text-align:center;">
                 {{ \Carbon\Carbon::now()->locale('id_ID')->translatedFormat('l, d F Y') }}
            </td>
        </tr>
        <tr>
            <td style="padding-top:50px;">
                (....................................................)
            </td>
            <td style="padding-top:50px;">
                (....................................................)
            </td>
        </tr>
    </table>
</body>

</html>
