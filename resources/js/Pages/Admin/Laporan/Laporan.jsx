import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head } from "@inertiajs/react";
import { useEffect, useState } from "react";
import axios from "axios";

const bulanList = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
];

export default function Laporan({ title }) {
    const today = new Date();
    const [bulan, setBulan] = useState(today.getMonth());
    const [tahun, setTahun] = useState(today.getFullYear());
    const [loading, setLoading] = useState(false);

    // Data state
    const [saldoAkhir, setSaldoAkhir] = useState(0);
    const [pemasukan, setPemasukan] = useState([]);
    const [pengeluaran, setPengeluaran] = useState([]);
    const [totalPemasukan, setTotalPemasukan] = useState(0);
    const [totalPengeluaran, setTotalPengeluaran] = useState(0);

    // Ambil data dari backend
    const fetchData = async () => {
        setLoading(true);
        try {
            const res = await axios.get(route("admin.laporan"), {
                params: { bulan: bulan + 1, tahun },
            });
            setSaldoAkhir(res.data.saldo_akhir_bulan_lalu || 0);
            setPemasukan(res.data.pemasukan || []);
            setPengeluaran(res.data.pengeluaran || []);
            setTotalPemasukan(res.data.total_pemasukan || 0);
            setTotalPengeluaran(res.data.total_pengeluaran || 0);
        } catch (e) {
            setSaldoAkhir(0);
            setPemasukan([]);
            setPengeluaran([]);
            setTotalPemasukan(0);
            setTotalPengeluaran(0);
        }
        setLoading(false);
    };

    useEffect(() => {
        fetchData();
    }, [bulan, tahun]);

    // Tahun dinamis (5 tahun ke belakang)
    const tahunList = [];
    for (let i = today.getFullYear(); i >= today.getFullYear() - 5; i--) {
        tahunList.push(i);
    }

    return (
        <AdminLayout>
            <Head title={title} />
            <div className="container-fluid py-4">
                <div className="row mb-3">
                    <div className="col-md-3">
                        <label className="fw-bold">Bulan</label>
                        <select
                            className="form-select"
                            value={bulan}
                            onChange={(e) => setBulan(Number(e.target.value))}
                        >
                            {bulanList.map((b, idx) => (
                                <option key={idx} value={idx}>
                                    {b}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div className="col-md-2">
                        <label className="fw-bold">Tahun</label>
                        <select
                            className="form-select"
                            value={tahun}
                            onChange={(e) => setTahun(Number(e.target.value))}
                        >
                            {tahunList.map((t) => (
                                <option key={t} value={t}>
                                    {t}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div className="col-auto ms-auto">
                        <button
                            className="btn btn-danger"
                            onClick={() =>
                                window.open(
                                    route("admin.laporan.export.pdf", {
                                        bulan: bulan + 1,
                                        tahun,
                                    }),
                                    "_blank"
                                )
                            }
                        >
                            <i className="bi bi-file-pdf me-1"></i> Export PDF
                        </button>
                    </div>
                </div>
                <div className="row">
                    {/* Tabel Kiri */}
                    <div className="col-md-6 mb-4">
                        <div className="card">
                            <div className="card-header bg-success text-white">
                                <b>
                                    Saldo Akhir Bulan Lalu & Pemasukan Bulan Ini
                                </b>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table className="table table-bordered table-sm">
                                        <thead>
                                            <tr className="text-center">
                                                <th>Tanggal</th>
                                                <th>Keterangan</th>
                                                <th>Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {/* Baris saldo akhir bulan lalu */}
                                            <tr>
                                                <td className="text-center">
                                                    -
                                                </td>
                                                <td>
                                                    <b>
                                                        Saldo Akhir Bulan Lalu
                                                    </b>
                                                </td>
                                                <td
                                                    className="text-end"
                                                    style={{
                                                        color: "green",
                                                        fontWeight: "bold",
                                                    }}
                                                >
                                                    Rp{" "}
                                                    {Number(
                                                        saldoAkhir
                                                    ).toLocaleString("id-ID")}
                                                </td>
                                            </tr>
                                            {/* Data pemasukan */}
                                            {pemasukan.length === 0 && (
                                                <tr>
                                                    <td
                                                        colSpan={3}
                                                        className="text-center"
                                                    >
                                                        Tidak ada data
                                                    </td>
                                                </tr>
                                            )}
                                            {pemasukan.map((row, i) => (
                                                <tr key={i}>
                                                    <td className="text-center">
                                                        {row.tanggal}
                                                    </td>
                                                    <td>{row.keterangan}</td>
                                                    <td className="text-end text-success">
                                                        Rp{" "}
                                                        {Number(
                                                            row.nominal
                                                        ).toLocaleString(
                                                            "id-ID"
                                                        )}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th
                                                    colSpan={2}
                                                    className="text-end"
                                                >
                                                    Total Pemasukan
                                                </th>
                                                <th className="text-end text-success">
                                                    Rp{" "}
                                                    {Number(
                                                        totalPemasukan
                                                    ).toLocaleString("id-ID")}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/* Tabel Kanan */}
                    <div className="col-md-6 mb-4">
                        <div className="card">
                            <div className="card-header bg-danger text-white">
                                <b>Pengeluaran Bulan Ini</b>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table className="table table-bordered table-sm">
                                        <thead>
                                            <tr className="text-center">
                                                <th>Tanggal</th>
                                                <th>Keterangan</th>
                                                <th>Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {pengeluaran.length === 0 && (
                                                <tr>
                                                    <td
                                                        colSpan={3}
                                                        className="text-center"
                                                    >
                                                        Tidak ada data
                                                    </td>
                                                </tr>
                                            )}
                                            {pengeluaran.map((row, i) => (
                                                <tr key={i}>
                                                    <td className="text-center">
                                                        {row.tanggal}
                                                    </td>
                                                    <td>{row.keterangan}</td>
                                                    <td className="text-end text-danger">
                                                        Rp{" "}
                                                        {Number(
                                                            row.nominal
                                                        ).toLocaleString(
                                                            "id-ID"
                                                        )}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th
                                                    colSpan={2}
                                                    className="text-end"
                                                >
                                                    Total Pengeluaran
                                                </th>
                                                <th className="text-end text-danger">
                                                    Rp{" "}
                                                    {Number(
                                                        totalPengeluaran
                                                    ).toLocaleString("id-ID")}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th
                                                    colSpan={2}
                                                    className="text-end"
                                                >
                                                    SALDO AKHIR
                                                </th>
                                                <th className="text-end text-primary">
                                                    Rp{" "}
                                                    {(
                                                        Number(saldoAkhir) +
                                                        Number(totalPemasukan) -
                                                        Number(totalPengeluaran)
                                                    ).toLocaleString("id-ID")}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {loading && (
                    <div className="text-center">
                        <span className="spinner-border spinner-border-sm"></span>{" "}
                        Memuat data...
                    </div>
                )}
            </div>
        </AdminLayout>
    );
}
