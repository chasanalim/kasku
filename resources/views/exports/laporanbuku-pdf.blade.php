{{-- filepath: resources/views/exports/laporanbuku-pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Buku Besar</title>
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

        .text-success {
            color: #198754;
        }

        .text-danger {
            color: #dc3545;
        }

        .fw-bold {
            font-weight: bold;
        }

        .bg-success {
            background-color: #198754;
            color: white;
            padding: 8px;
        }

        .card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 8px;
            margin-bottom: 10px;
        }

        .ttd-row {
            margin-top: 20px;
            width: 100%;
        }

        .ttd-table {
            width: 100%;
            border: none;
            margin-top: 10px;
        }

        .ttd-table td {
            border: none;
            height: 20px;
            vertical-align: bottom;
            text-align: center;
        }

        .ttd-label {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3 style="margin:0;">LAPORAN BUKU BESAR KAS KELOMPOK</h3>
    </div>

    <table style="border:none; margin-bottom: 20px;">
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
    </table>

    <div class="card">

        <table>
            <thead>
                <tr>
                    <th style="width: 2%">No</th>
                    <th>Nama Kas</th>
                    <th>Saldo {{ \Carbon\Carbon::parse($tanggal_awal)->subMonth()->locale('id')->translatedFormat('F') }}</th>
                    <th>Pemasukan</th>
                    <th>Pengeluaran</th>
                    <th>Saldo {{ \Carbon\Carbon::parse($tanggal_awal)->locale('id')->translatedFormat('F') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($akun as $key => $row)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ $row->nama }}</td>
                        <td class="text-end">
                            Rp {{ number_format($row->saldo_bulan_lalu, 0, ',', '.') }}
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($row->pemasukan, 0, ',', '.') }}
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($row->pengeluaran, 0, ',', '.') }}
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($row->saldo_sekarang, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="2" class="text-end">Total</td>
                    <td class="text-end">
                        Rp {{ number_format($total['saldo_bulan_lalu'], 0, ',', '.') }}
                    </td>
                    <td class="text-end ">
                        Rp {{ number_format($total['pemasukan'], 0, ',', '.') }}
                    </td>
                    <td class="text-end ">
                        Rp {{ number_format($total['pengeluaran'], 0, ',', '.') }}
                    </td>
                    <td class="text-end">
                        Rp {{ number_format($total['saldo_sekarang'], 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Tanda Tangan -->
    <table class="ttd-table" style="border:none; margin-top: 30px;">
        <tr>
            <td style="border:none; text-align:center;">
                Kresek, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td style="border:none; text-align:center; padding-top:50px;">
                (....................................................)
            </td>
        </tr>
    </table>
</body>

</html>
