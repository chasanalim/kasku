import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head } from "@inertiajs/react";
import { useCallback, useEffect, useState } from "react";
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

export default function LaporanDesa({ title, can }) {
    const [jatah, setJatah] = useState([]);
    const [totalJatah, setTotalJatah] = useState(0);
    const [loading, setLoading] = useState(false);
    const [bulan, setBulan] = useState(new Date().getMonth());
    const [tahun, setTahun] = useState(new Date().getFullYear());
    const [showModal, setShowModal] = useState(false);
    const [formData, setFormData] = useState({
        jatahProker: "",
        infaqJumat: "",
    });

    // Fetch data function
    const fetchData = useCallback(() => {
        setLoading(true);
        axios
            .get(route("admin.laporan-desa"), {
                params: {
                    bulan: bulan + 1,
                    tahun: tahun,
                },
            })
            .then((response) => {
                setJatah(response.data.jatah);
                setTotalJatah(response.data.total_jatah);
            })
            .catch((error) => {
                console.error("Error fetching data:", error);
                alert("Gagal mengambil data");
            })
            .finally(() => setLoading(false));
    }, [bulan, tahun]);

    // Initial load and when month/year changes
    useEffect(() => {
        fetchData();
    }, [fetchData]);

    // Tahun dinamis (5 tahun ke belakang)
    const tahunList = [];
    for (
        let i = new Date().getFullYear();
        i >= new Date().getFullYear() - 5;
        i--
    ) {
        tahunList.push(i);
    }

    // Handle form input changes with number formatting
    const handleInputChange = (e) => {
        const { name, value } = e.target;
        // Remove non-numeric characters except dots
        const numericValue = value.replace(/[^\d]/g, "");

        setFormData((prev) => ({
            ...prev,
            [name]: numericValue ? Number(numericValue).toLocaleString("id-ID") : "",
        }));
    };

    // Handle sync with validation
    const handleSync = () => {
        if (!formData.jatahProker || !formData.infaqJumat) {
            alert("Semua field harus diisi!");
            return;
        }

        // Parse the formatted numbers back to numeric values
        const jatahProker = parseFloat(formData.jatahProker.replace(/\./g, ""));
        const infaqJumat = parseFloat(formData.infaqJumat.replace(/\./g, ""));

        setLoading(true);
        axios
            .post(route("admin.sync"), {
                bulan: bulan + 1,
                tahun: tahun,
                jatahProker,
                infaqJumat,
            })
            .then((response) => {
                if (response.data.status === "success") {
                    alert(response.data.message);
                    fetchData();
                    setShowModal(false);
                    setFormData({ jatahProker: "", infaqJumat: "" });
                }
            })
            .catch((error) => {
                console.error("Sync failed:", error);
                if (error.response?.data?.message) {
                    alert(error.response.data.message);
                } else {
                    alert("Gagal melakukan sinkronisasi data");
                }
            })
            .finally(() => setLoading(false));
    };

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
                        {can.createLaporan && (
                            <button
                                className="btn btn-success"
                                onClick={() => setShowModal(true)}
                                disabled={loading}
                            >
                                <i className="bi bi-arrow-repeat me-1"></i>
                                SYNCHRONIZE
                            </button>
                        )}
                        <button
                            className="btn btn-danger ms-2"
                            onClick={() =>
                                window.open(
                                    route("admin.laporandesa.export.pdf", {
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
                    <div className="col-md-6 offset-md-3 mb-4">
                        <div className="card">
                            <div className="card-header bg-success text-white">
                                <b>
                                    Laporan Setoran Kelompok Kresek 1{" "}
                                    {bulanList[bulan]} {tahun}
                                </b>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table className="table table-bordered table-sm">
                                        <thead>
                                            <tr className="text-center">
                                                <th style={{ width: "2%" }}>
                                                    No
                                                </th>
                                                <th>Uraian</th>
                                                <th>Jumlah</th>
                                                {/* <th>Aksi</th> */}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {jatah.length === 0 ? (
                                                <tr>
                                                    <td
                                                        colSpan={4}
                                                        className="text-center"
                                                    >
                                                        {loading
                                                            ? "Loading..."
                                                            : "Tidak ada data, Silahkan Sync Dahulu"}
                                                    </td>
                                                </tr>
                                            ) : (
                                                jatah.map((row, i) => (
                                                    <tr key={row.id}>
                                                        <td className="text-center">
                                                            {i + 1}
                                                        </td>
                                                        <td>
                                                            {row.keterangan}
                                                        </td>
                                                        <td className="text-end">
                                                            Rp{" "}
                                                            {Number(
                                                                row.jumlah
                                                            ).toLocaleString(
                                                                "id-ID"
                                                            )}
                                                        </td>
                                                        {/* <td className="text-center">
                                                            <button
                                                                className="btn btn-sm btn-danger"
                                                                onClick={() =>
                                                                    hapusData(
                                                                        row.id
                                                                    )
                                                                }
                                                            >
                                                                <i className="bi bi-trash"></i>
                                                            </button>
                                                        </td> */}
                                                    </tr>
                                                ))
                                            )}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th
                                                    colSpan={2}
                                                    className="text-end"
                                                >
                                                    Total
                                                </th>
                                                <th className="text-end">
                                                    Rp{" "}
                                                    {Number(
                                                        totalJatah
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

            {/* Add Modal Form */}
            {showModal && (
                <div
                    className="modal d-block"
                    tabIndex="-1"
                    style={{ backgroundColor: "rgba(0,0,0,0.5)" }}
                >
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">
                                    Input Data Sinkronisasi
                                </h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowModal(false)}
                                    disabled={loading}
                                ></button>
                            </div>
                            <div className="modal-body">
                                <div className="mb-3">
                                    <label className="form-label">
                                        Jatah Proker
                                    </label>
                                    <div className="input-group">
                                        <span className="input-group-text">Rp</span>
                                        <input
                                            type="text" /* Changed from number to text */
                                            className="form-control text-end" /* Added text-end */
                                            name="jatahProker"
                                            value={formData.jatahProker}
                                            onChange={handleInputChange}
                                            placeholder="Masukkan nominal"
                                            required
                                        />
                                    </div>
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">
                                        Infaq 2/3 Jumatan
                                    </label>
                                    <div className="input-group">
                                        <span className="input-group-text">Rp</span>
                                        <input
                                            type="text" /* Changed from number to text */
                                            className="form-control text-end" /* Added text-end */
                                            name="infaqJumat"
                                            value={formData.infaqJumat}
                                            onChange={handleInputChange}
                                            placeholder="Masukkan nominal"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                            <div className="modal-footer">
                                <button
                                    type="button"
                                    className="btn btn-secondary"
                                    onClick={() => setShowModal(false)}
                                    disabled={loading}
                                >
                                    Batal
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-primary"
                                    onClick={handleSync}
                                    disabled={loading}
                                >
                                    {loading ? (
                                        <>
                                            <span className="spinner-border spinner-border-sm me-1"></span>
                                            Processing...
                                        </>
                                    ) : (
                                        "Sinkronkan"
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}
