import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import $ from "jquery";
import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { Toast, Tooltip } from "bootstrap";
// Import bootstrap JS
import "bootstrap/dist/js/bootstrap.bundle.min.js";

export default function Index({ title, flash }) {
    const tableRef = useRef();

    useEffect(() => {
        const dt = $(tableRef.current).DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: route("admin.ir.index"),
                type: "GET",
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
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                    width: "10%",
                    className: "text-center",
                    render: function (data) {
                        let buttons = [];

                        // if (can.edit) {
                        buttons.push(`
                                <a href="${data.edit_url}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            `);
                        // }

                        // if (can.delete) {
                        buttons.push(`
                                <a href="javascript:void(0)"
                                   onclick="deleteItem('${data.delete_url}')"
                                   class="btn btn-sm btn-danger"
                                   title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            `);
                        // }

                        return `<div class="btn-group">${buttons.join(
                            ""
                        )}</div>`;
                    },
                },
                {
                    data: "transaksi.tanggal",
                    name: "transaksi.tanggal",
                    className: "text-center",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: "jamaah.nama",
                    name: "jamaah.nama",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: "ir",
                    name: "ir",
                    orderable: true,
                    searchable: true,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "syiar",
                    name: "syiar",
                    className: "text-center",
                    orderable: true,
                    searchable: true,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "jumlah",
                    name: "jumlah",
                    className: "text-center",
                    orderable: true,
                    searchable: true,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },

                {
                    data: "debit",
                    name: "debit",
                    className: "text-center",
                    orderable: true,
                    searchable: true,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "kredit",
                    name: "kredit",
                    className: "text-center",
                    orderable: true,
                    searchable: true,
                    render: function (data) {
                        return parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    data: "transaksi.keterangan",
                    name: "transaksi.keterangan",
                    orderable: true,
                    searchable: true,
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
                const totalIR = api
                    .column(4, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                const totalSyiar = api
                    .column(5, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                const totalJumlah = api
                    .column(6, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                const totalDebit = api
                    .column(7, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                const totalKredit = api
                    .column(8, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                // Tampilkan hasil dengan format yang benar
                $(api.column(4).footer()).html(
                    `Rp ${Math.round(totalIR).toLocaleString("id-ID")}`
                );
                $(api.column(5).footer()).html(
                    `Rp ${Math.round(totalSyiar).toLocaleString("id-ID")}`
                );
                $(api.column(6).footer()).html(
                    `Rp ${Math.round(totalJumlah).toLocaleString("id-ID")}`
                );
                $(api.column(7).footer()).html(
                    `Rp ${Math.round(totalDebit).toLocaleString("id-ID")}`
                );
                $(api.column(8).footer()).html(
                    `Rp ${Math.round(totalKredit).toLocaleString("id-ID")}`
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
    }, [flash]);
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
                                <Link
                                    href={route("admin.ir.create")}
                                    className="btn btn-primary mb-3"
                                >
                                    <i className="bi bi-plus-circle me-2"></i>
                                    Tambah Data IR
                                </Link>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table
                                        ref={tableRef}
                                        className="table table-sm table-hover"
                                    >
                                        <thead>
                                            <tr className="text-center">
                                                <th>No</th>
                                                <th>AKSI</th>
                                                <th>TANGGAL</th>
                                                <th>NAMA</th>
                                                <th>IR</th>
                                                <th>SYIAR</th>
                                                <th>JUMLAH</th>
                                                <th>DEBIT</th>
                                                <th>KREDIT</th>
                                                <th>KETERANGAN</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr className="text-center">
                                                <th
                                                    colSpan="4"
                                                    style={{
                                                        textAlign: "right",
                                                        paddingRight: "40px",
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
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {flash.message && (
                <div
                    className="position-fixed top-0 end-0 p-3"
                    style={{ zIndex: 5 }}
                >
                    <div
                        id="toast"
                        className="toast align-items-center text-white bg-success border-0"
                        role="alert"
                        aria-live="assertive"
                        aria-atomic="true"
                    >
                        <div className="d-flex">
                            <div className="toast-body">{flash.message}</div>
                            <button
                                type="button"
                                className="btn-close btn-close-white me-2 m-auto"
                                data-bs-dismiss="toast"
                                aria-label="Close"
                            ></button>
                        </div>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}
