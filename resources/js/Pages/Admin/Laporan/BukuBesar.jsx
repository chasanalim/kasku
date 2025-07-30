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

export default function BukuBesar({ title }) {
    const today = new Date();
    const [bulan, setBulan] = useState(today.getMonth());
    const [tahun, setTahun] = useState(today.getFullYear());
    const [loading, setLoading] = useState(false);

    // Data state
    const [akun, setAkun] = useState([]);
    const [totalAkun, setTotalAkun] = useState(0);

    // Ambil data dari backend
    const fetchData = async () => {
        setLoading(true);
        try {
            const res = await axios.get(route("admin.buku-besar"), {
                params: { bulan: bulan + 1, tahun },
            });
            setAkun(res.data.akun || []);
            setTotalAkun(res.data.total_akun|| 0);
        } catch (e) {
            setAkun([]);
            setTotalAkun(0);
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
                    {/* Tabel Kiri: Pemasukan */}
                    <div className="col-md-8 offset-md-2  mb-4">
                        <div className="card">
                            <div className="card-header bg-success text-white">
                                <b>KAS KEUANGAN KELOMPOK 1</b>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table className="table table-bordered table-sm">
                                        <thead>
                                            <tr className="text-center">
                                                <th style={{ width: "2%" }}>
                                                    No
                                                </th>
                                                <th>Akun Rekening</th>
                                                <th>Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {akun.length === 0 && (
                                                <tr>
                                                    <td
                                                        colSpan={5}
                                                        className="text-center"
                                                    >
                                                        Tidak ada data
                                                    </td>
                                                </tr>
                                            )}
                                            {akun.map((row, i) => (
                                                <tr key={i}>
                                                    <td className="text-center">
                                                        {i + 1}
                                                    </td>
                                                    
                                                    <td>{row.nama}</td>
                                                    <td className="text-end text-success">
                                                        Rp{" "}
                                                        {Number(
                                                            row.saldo_awal
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
                                                    Total
                                                </th>
                                                <th className="text-end text-success">
                                                    Rp{" "}
                                                    {Number(
                                                        totalAkun
                                                    ).toLocaleString("id-ID")}
                                                </th>
                                                <th></th>
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
