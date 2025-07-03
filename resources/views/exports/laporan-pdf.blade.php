{{-- filepath: resources/views/exports/laporan-pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Keuangan Bulanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .period {
            text-align: center;
            margin-bottom: 20px;
            color: #2b2b2b;
        }

        .flex-row {
            display: flex;
            flex-direction: row;
            gap: 20px;
            justify-content: space-between;
        }

        .table-wrap {
            width: 48%;
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
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-success {
            color: green;
        }

        .text-danger{
            color: #b03737;
        }

        .text-primary {
            color: #0d6efd;
        }

        .fw-bold {
            font-weight: bold;
        }

        .ttd-row {
            margin-top: 40px;
            width: 100%;
        }

        .ttd-table {
            width: 100%;
            border: none;
            margin-top: 40px;
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
        <h3 style="margin:0;">LAPORAN KEUANGAN BULANAN</h3>
        <div class="period">
            Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->translatedFormat('F Y') }}
        </div>
    </div>
    <!-- Ganti bagian .flex-row dengan table layout -->
    <table width="100%" style="border:none; margin-bottom: 20px;">
        <tr>
            <td width="50%" valign="top" style="padding-right:10px; border:none;">
                <!-- Pemasukan -->
                <table>
                    <thead>
                        <tr>
                            <th colspan="3" class="text-center" style="background:#d1e7dd;">Saldo Akhir Bulan Lalu &
                                Pemasukan Bulan
                                Ini</th>
                        </tr>
                        <tr>
                            <th width="25%">Tanggal</th>
                            <th>Keterangan</th>
                            <th width="25%">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">-</td>
                            <td class="fw-bold">Saldo Akhir Bulan Lalu</td>
                            <td class="text-end  fw-bold">
                                Rp {{ number_format($saldo_akhir_bulan_lalu, 0, ',', '.') }}
                            </td>
                        </tr>
                        @forelse($pemasukan as $row)
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                <td>{{ $row->keterangan }}</td>
                                <td class="text-end ">
                                    Rp {{ number_format($row->nominal, 0, ',', '.') }}
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
                            <th colspan="2" class="text-end">Total Pemasukan</th>
                            <th class="text-end ">
                                Rp {{ number_format($total_pemasukan, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td width="50%" valign="top" style="padding-left:10px; border:none;">
                <!-- Pengeluaran -->
                <table>
                    <thead>
                        <tr>
                            <th colspan="3" class="text-center" style="background:#f8d7da;">Pengeluaran Bulan Ini
                            </th>
                        </tr>
                        <tr>
                            <th width="25%">Tanggal</th>
                            <th>Keterangan</th>
                            <th width="25%">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengeluaran as $row)
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                <td>{{ $row->keterangan }}</td>
                                <td class="text-end ">
                                    Rp {{ number_format($row->nominal, 0, ',', '.') }}
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
                            <th colspan="2" class="text-end">Total Pengeluaran</th>
                            <th class="text-end ">
                                Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2" class="text-end">SALDO AKHIR</th>
                            <th class="text-end text-primary">
                                Rp {{ number_format($saldo_akhir, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
    <!-- Tanda Tangan -->
    <table class="ttd-table">
        <tr>
            <td class="ttd-label">K.I KELOMPOK</td>
            <td class="ttd-label">K.I KELOMPOK</td>
            <td class="ttd-label">K.I KELOMPOK</td>
            <td class="ttd-label">K.U KELOMPOK</td>
            <td class="ttd-label">MUBALEGH KELOMPOK</td>
            <td class="ttd-label">AGHNIYA KELOMPOK</td>
            <td class="ttd-label">PENEROBOS KELOMPOK</td>
        </tr>
        <tr>
            <td style="height: 60px"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>(................................)</td>
            <td>(................................)</td>
            <td>(................................)</td>
            <td>(................................)</td>
            <td>(................................)</td>
            <td>(................................)</td>
            <td>(................................)</td>
        </tr>
    </table>
</body>

</html>
