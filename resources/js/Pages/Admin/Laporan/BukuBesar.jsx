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
    const [jenis, setJenis] = useState('ku'); // Add jenis state with default 'ku'
    const [loading, setLoading] = useState(false);

    // Update state
    const [akun, setAkun] = useState([]);
    const [total, setTotal] = useState({
        saldo_bulan_lalu: 0,
        pemasukan: 0,
        pengeluaran: 0,
        saldo_sekarang: 0
    });

    // Ambil data dari backend
    const fetchData = async () => {
        setLoading(true);
        try {
            const res = await axios.get(route("admin.buku-besar"), {
                params: {
                    bulan: bulan + 1,
                    tahun,
                    jenis // Add jenis parameter
                },
            });
            setAkun(res.data.akun || []);
            setTotal(res.data.total || {});
        } catch (e) {
            setAkun([]);
            setTotal({
                saldo_bulan_lalu: 0,
                pemasukan: 0,
                pengeluaran: 0,
                saldo_sekarang: 0
            });
        }
        setLoading(false);
    };

    useEffect(() => {
        fetchData();
    }, [bulan, tahun, jenis]); // Add jenis to dependencies

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
                    <div className="col-md-2">
                        <label className="fw-bold">Jenis</label>
                        <select
                            className="form-select"
                            value={jenis}
                            onChange={(e) => setJenis(e.target.value)}
                        >
                            <option value="ku">KU</option>
                            <option value="all">ALL</option>
                        </select>
                    </div>
                    <div className="col-auto ms-auto">
                        <button
                            className="btn btn-danger"
                            onClick={() =>
                                window.open(
                                    route("admin.laporanbuku.export.pdf", {
                                        bulan: bulan + 1,
                                        tahun,
                                        jenis // Add jenis parameter
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
                    <div className="col-12 mb-4">
                        <div className="card">
                            <div className="card-header bg-success text-white">
                                <b>BUKU BESAR KAS KELOMPOK 1</b>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table className="table table-bordered table-sm">
                                        <thead className="text-center">
                                            <tr>
                                                <th style={{ width: "2%" }}>No</th>
                                                <th>Nama Kas</th>
                                                <th>Saldo {bulanList[bulan - 1]}</th>
                                                <th>Pemasukan</th>
                                                <th>Pengeluaran</th>
                                                <th>Saldo {bulanList[bulan]}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {akun.length === 0 && (
                                                <tr>
                                                    <td colSpan={6} className="text-center">
                                                        Tidak ada data
                                                    </td>
                                                </tr>
                                            )}
                                            {akun.map((row, i) => (
                                                <tr key={i}>
                                                    <td className="text-center">{i + 1}</td>
                                                    <td>{row.nama}</td>
                                                    <td className="text-end">
                                                        Rp {Number(row.saldo_bulan_lalu).toLocaleString("id-ID")}
                                                    </td>
                                                    <td className="text-end text-success">
                                                        Rp {Number(row.pemasukan).toLocaleString("id-ID")}
                                                    </td>
                                                    <td className="text-end text-danger">
                                                        Rp {Number(row.pengeluaran).toLocaleString("id-ID")}
                                                    </td>
                                                    <td className="text-end">
                                                        Rp {Number(row.saldo_sekarang).toLocaleString("id-ID")}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot>
                                            <tr className="fw-bold">
                                                <td colSpan={2} className="text-end">Total</td>
                                                <td className="text-end">
                                                    Rp {Number(total.saldo_bulan_lalu).toLocaleString("id-ID")}
                                                </td>
                                                <td className="text-end text-success">
                                                    Rp {Number(total.pemasukan).toLocaleString("id-ID")}
                                                </td>
                                                <td className="text-end text-danger">
                                                    Rp {Number(total.pengeluaran).toLocaleString("id-ID")}
                                                </td>
                                                <td className="text-end">
                                                    Rp {Number(total.saldo_sekarang).toLocaleString("id-ID")}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <div className="fw-bold text-center my-4 fs-3">
                                        Pencapaian Bulan {bulanList[bulan]} {tahun}:{" "}
                                        {total.saldo_sekarang - total.saldo_bulan_lalu >= 0 ? (
                                            <span className="text-success">
                                                <i className="bi bi-arrow-up-short"></i>{" "}
                                                {Number(total.saldo_sekarang - total.saldo_bulan_lalu).toLocaleString(
                                                    "id-ID"
                                                )}
                                            </span>
                                        ) : (
                                            <span className="text-danger">
                                                <i className="bi bi-arrow-down-short"></i>{" "}
                                                {Number(total.saldo_bulan_lalu - total.saldo_sekarang).toLocaleString(
                                                    "id-ID"
                                                )}
                                            </span>
                                        )}
                                    </div>
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
