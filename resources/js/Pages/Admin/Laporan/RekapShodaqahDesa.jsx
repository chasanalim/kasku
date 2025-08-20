import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import $ from "jquery";
import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { Toast, Tooltip } from "bootstrap";
// Import bootstrap JS
import "bootstrap/dist/js/bootstrap.bundle.min.js";

export default function RekapShodaqah({ title, flash, shodaqah, can }) {
    const tableRef = useRef();

    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 2)
        .toISOString()
        .split("T")[0];
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 1)
        .toISOString()
        .split("T")[0];

    const [tanggalAwal, setTanggalAwal] = useState(firstDay);
    const [tanggalAkhir, setTanggalAkhir] = useState(lastDay);
    const handleTanggalAwalChange = (event) => {
        setTanggalAwal(event.target.value);
        if (tanggalAkhir && event.target.value) {
            $(tableRef.current).DataTable().ajax.reload();
        }
    };

    const handleTanggalAkhirChange = (event) => {
        setTanggalAkhir(event.target.value);
        if (tanggalAwal && event.target.value) {
            $(tableRef.current).DataTable().ajax.reload();
        }
    };
    const handleReset = () => {
        setTanggalAwal("");
        setTanggalAkhir("");
        $(tableRef.current).DataTable().ajax.reload();
    };

    const handleExport = (type) => {
        const url = route(`admin.shodaqah-desa.export.${type}`, {
            tanggal_awal: tanggalAwal,
            tanggal_akhir: tanggalAkhir,
        });
        window.open(url, "_blank");
    };

    useEffect(() => {
        const dt = $(tableRef.current).DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 100, // Default show 100 records
            lengthMenu: [
                [100, 500, -1],
                [100, 500, "All"],
            ],
            ajax: {
                url: route("admin.laporan.rekap-shodaqah-desa"),
                type: "GET",
                data: (d) => {
                    if (tanggalAwal && tanggalAkhir) {
                        d.tanggal_awal = tanggalAwal;
                        d.tanggal_akhir = tanggalAkhir;
                    }
                },
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                },

                {
                    data: "nama",
                    name: "nama",
                    orderable: true,
                    searchable: true,
                    render: function (data, type, row) {
                        if (!row.tanggal) {
                            return `<span style="color: red;">${data}</span>`;
                        }
                        return data;
                    },
                },
                {
                    data: "persenan",
                    name: "persenan",
                    orderable: true,
                    searchable: false,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "jimpitan",
                    name: "jimpitan",
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "dapur_pusat",
                    name: "dapur_pusat",
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "kk",
                    name: "kk",
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "ppg",
                    name: "ppg",
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "zakat",
                    name: "zakat",
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "jumlah",
                    name: "jumlah",
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "status_format",
                    name: "status",
                    title: "Status",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                },
            ],
            drawCallback: function () {
                // Initialize tooltips
                const tooltips = document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]'
                );
                tooltips.forEach((tooltipNode) => {
                    new Tooltip(tooltipNode);
                });
            },
            footerCallback: function (row, data, start, end, display) {
                const api = this.api();

                // Perbaikan fungsi parse
                const parse = (val) => {
                    if (typeof val === "string") {
                        // Hapus semua titik sebagai pemisah ribuan
                        // Ganti koma dengan titik untuk desimal
                        let cleanNumber = val
                            .replace(/\.00/g, "")
                            .replace(",", ".");
                        return parseFloat(cleanNumber) || 0;
                    }
                    return typeof val === "number" ? val : 0;
                };

                // Hitung total dengan menggunakan page.info()
                const totalPersenan = api
                    .column(2, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                const totalJimpitan = api
                    .column(3, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                const totalDapurPusat = api
                    .column(4, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                const totalShodaqahDaerah = api
                    .column(5, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                const totalShodaqahKelompok = api
                    .column(6, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                const totalJumlah = api
                    .column(7, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                // Tampilkan hasil dengan format yang benar
                $(api.column(2).footer()).html(
                    `Rp ${Math.round(totalPersenan).toLocaleString("id-ID")}`
                );
                $(api.column(3).footer()).html(
                    `Rp ${Math.round(totalJimpitan).toLocaleString("id-ID")}`
                );
                $(api.column(4).footer()).html(
                    `Rp ${Math.round(totalDapurPusat).toLocaleString("id-ID")}`
                );
                $(api.column(5).footer()).html(
                    `Rp ${Math.round(totalShodaqahDaerah).toLocaleString(
                        "id-ID"
                    )}`
                );
                $(api.column(6).footer()).html(
                    `Rp ${Math.round(totalShodaqahKelompok).toLocaleString(
                        "id-ID"
                    )}`
                );
                $(api.column(7).footer()).html(
                    `Rp ${Math.round(totalJumlah).toLocaleString("id-ID")}`
                );
            },
        });

        // Handle flash messages
        if (flash?.message) {
            const toastEl = document.getElementById("toast");
            if (toastEl) {
                const toast = new Toast(toastEl);
                toast.show();
            }
        }

        return () => {
            dt.destroy();
            // Dispose tooltips
            const tooltips = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]'
            );
            tooltips.forEach((tooltipNode) => {
                const tooltip = Tooltip.getInstance(tooltipNode);
                if (tooltip) {
                    tooltip.dispose();
                }
            });
        };
    }, [flash, tanggalAwal, tanggalAkhir]);
    const deleteItem = (url) => {
        if (confirm("Apakah anda yakin ingin menghapus data ini?")) {
            router.delete(url, {
                onSuccess: () => {
                    $(tableRef.current).DataTable().ajax.reload();
                },
            });
        }
    };

    window.deleteItem = deleteItem;

    return (
        <AdminLayout>
            <Head title={title} />

            <div className="container-fluid py-4">
                <div className="row">
                    <div className="col-12">
                        <div className="card">
                            <div className="card-header pb-0 d-flex justify-content-between align-items-center">
                                <h5 className="mb-2 fw-bold">{title}</h5>
                            </div>
                            <div className="card-body">
                                <div className="mb-4">
                                    <div className="row align-items-end gx-2">
                                        <div className="col-auto">
                                            <label className="form-label">
                                                Tanggal Awal:
                                            </label>
                                        </div>
                                        <div className="col-auto">
                                            <input
                                                type="date"
                                                className="form-control"
                                                value={tanggalAwal}
                                                onChange={
                                                    handleTanggalAwalChange
                                                }
                                            />
                                        </div>
                                        <div className="col-auto">
                                            <label className="form-label">
                                                Tanggal Akhir:
                                            </label>
                                        </div>
                                        <div className="col-auto">
                                            <input
                                                type="date"
                                                className="form-control"
                                                value={tanggalAkhir}
                                                onChange={
                                                    handleTanggalAkhirChange
                                                }
                                            />
                                        </div>
                                        <div className="col-auto">
                                            <button
                                                className="btn btn-danger"
                                                onClick={handleReset}
                                            >
                                                <i className="bi bi-x-circle me-1"></i>{" "}
                                                Reset
                                            </button>
                                        </div>
                                        <div className="col-auto ms-auto">
                                            <button
                                                className="btn btn-success me-2"
                                                onClick={() =>
                                                    handleExport("excel")
                                                }
                                            >
                                                <i className="bi bi-file-excel me-1"></i>{" "}
                                                Export Excel
                                            </button>
                                            <button
                                                className="btn btn-danger"
                                                onClick={() =>
                                                    handleExport("pdf")
                                                }
                                            >
                                                <i className="bi bi-file-pdf me-1"></i>{" "}
                                                Export PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div className="table-responsive">
                                    <table
                                        ref={tableRef}
                                        className="table table-sm table-hover"
                                    >
                                        <thead>
                                            <tr className="text-center">
                                                <th>No</th>
                                                <th>NAMA</th>
                                                <th>PERSENAN</th>
                                                <th>JIMPITAN</th>
                                                <th>DAPUR PUSAT</th>
                                                <th>KK</th>
                                                <th>PPG</th>
                                                <th>ZAKAT</th>
                                                <th>JUMLAH</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr className="text-center">
                                                <th
                                                    colSpan="2"
                                                    style={{
                                                        textAlign: "right",
                                                        paddingRight: "10px",
                                                    }}
                                                >
                                                    Total:
                                                </th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
