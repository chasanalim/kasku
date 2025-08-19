import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import $ from "jquery";
import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { Toast, Tooltip } from "bootstrap";
// Import bootstrap JS
import "bootstrap/dist/js/bootstrap.bundle.min.js";

export default function Index({ title, can, flash }) {
    const tableRef = useRef();

    useEffect(() => {
        const dt = $(tableRef.current).DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: route("admin.akun.index"),
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

                        if (can.editMaster) {
                            buttons.push(`
                                <a href="${data.edit_url}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            `);
                        }

                        if (can.deleteMaster) {
                            buttons.push(`
                                <a href="javascript:void(0)"
                                   onclick="deleteItem('${data.delete_url}')"
                                   class="btn btn-sm btn-danger"
                                   title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            `);
                        }

                        return `<div class="btn-group">${buttons.join(
                            ""
                        )}</div>`;
                    },
                },
                {
                    data: "nama",
                    name: "nama",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: "kode_akun",
                    name: "kode_akun",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: "tipe",
                    name: "tipe",
                    render: function (data) {
                        return data === "Kas"
                            ? `<span class="badge bg-info p-2">Kas</span>`
                            : data === "Pemasukan"
                            ? `<span class="badge bg-success p-2">Pemasukan</span>`
                            : data === "Pengeluaran"
                            ? `<span class="badge bg-danger p-2">Pengeluaran</span>`
                            : `<span class="badge bg-warning p-2">Penampung</span>`;
                    },
                },
                {
                    data: "saldo_awal",
                    name: "saldo_awal",
                    orderable: true,
                    searchable: true,
                    render: function (data) {
                        return `Rp. ` + data;
                    },
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
                                {can.createMaster && (
                                    <Link
                                        href={route("admin.akun.create")}
                                        className="btn btn-primary mb-3"
                                    >
                                        <i className="bi bi-plus-circle me-2"></i>
                                        Tambah Akun Rekening
                                    </Link>
                                )}
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table
                                        ref={tableRef}
                                        className="table table-sm table-hover"
                                    >
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Aksi</th>
                                                <th>NAMA AKUN</th>
                                                <th>KODE AKUN</th>
                                                <th>TIPE</th>
                                                <th>SALDO AWAL</th>
                                            </tr>
                                        </thead>
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
