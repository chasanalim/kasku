import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import $ from "jquery";
import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { Toast, Tooltip } from "bootstrap";
// Import bootstrap JS
import "bootstrap/dist/js/bootstrap.bundle.min.js";

export default function Index({ title, flash, infaq, can }) {
    const tableRef = useRef();
    const [selectedIds, setSelectedIds] = useState([]);
    const verifyItem = (url) => {
        router.post(url, {
            onSuccess: () => {
                $(tableRef.current).DataTable().ajax.reload();
            },
        });
    };
    window.verifyItem = verifyItem;
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
    const handleMultipleVerify = () => {
        if (!selectedIds.length) return;

        if (!confirm("Yakin ingin memverifikasi data terpilih?")) return;

        router.post(
            route("admin.kaskelompok.verify-multiple"),
            {
                ids: selectedIds,
            },
            {
                onSuccess: () => {
                    setSelectedIds([]);
                    $("#datatable").DataTable().ajax.reload();
                },
            }
        );
    };

    const handleMultipleDelete = () => {
        if (!selectedIds.length) return;

        if (!confirm("Yakin ingin menghapus data terpilih?")) return;

        router.delete(route("admin.kaskelompok.destroy-multiple"), {
            data: { ids: selectedIds },
            onSuccess: () => {
                setSelectedIds([]);
                $("#datatable").DataTable().ajax.reload();
            },
        });
    };

    useEffect(() => {
        const dt = $(tableRef.current).DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 30, // Default show 100 records
            lengthMenu: [
                [100, 500, -1],
                [100, 500, "All"],
            ],
            ajax: {
                url: route("admin.kaskelompok.index"),
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
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function (data, type, row) {
                        // Only show checkbox if status is not verified (1)
                        if (row.status !== 1) {
                            return `<input type="checkbox" class="form-check-input select-item" value="${row.id}">`;
                        }
                        return "";
                    },
                },
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
                    render: function (data, type, row) {
                        let buttons = [];
                        if (row.status !== 1) {
                            if (can.deletePengeluaran) {
                                buttons.push(`
                                <a href="javascript:void(0)"
                                   onclick="verifyItem('${data.verify_url}')"
                                   class="btn btn-sm btn-primary"
                                   title="Verifikasi">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                            `);
                            }

                            if (can.editPengeluaran) {
                                buttons.push(`
                                <a href="${data.edit_url}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            `);
                            }

                            if (can.deletePemasukan) {
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
                        } else {
                            return `<span class="badge bg-success">Verified</span>`;
                        }
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
                $(".select-all").prop("checked", false);
                $(".select-item").prop("checked", false);

                selectedIds.forEach((id) => {
                    $(`.select-item[value="${id}"]`).prop("checked", true);
                });
                const totalCheckboxes = $(".select-item").length;
                const checkedCheckboxes = $(".select-item:checked").length;
                $(".select-all").prop(
                    "checked",
                    totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes
                );

                // Update individual checkboxes based on selectedIds
                selectedIds.forEach((id) => {
                    $(`.select-item[value="${id}"]`).prop("checked", true);
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
                const totalJumlah = api
                    .column(4, { page: "current" })
                    .data()
                    .reduce((a, b) => parse(a) + parse(b), 0);

                // Tampilkan hasil dengan format yang benar

                $(api.column(4).footer()).html(
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
        // Handle select all checkbox
        $(document).on("change", ".select-all", function () {
            const checked = $(this).prop("checked");

            // Only select checkboxes for unverified items
            $(".select-item").each(function () {
                if ($(this).is(":visible")) {
                    $(this).prop("checked", checked);
                    const id = $(this).val();
                    if (checked) {
                        setSelectedIds((prev) => [...new Set([...prev, id])]);
                    } else {
                        setSelectedIds((prev) =>
                            prev.filter((item) => item !== id)
                        );
                    }
                }
            });
        });

        // Handle individual checkbox
        $(document).on("change", ".select-item", function () {
            const id = $(this).val();
            const checked = $(this).prop("checked");

            setSelectedIds((prev) => {
                if (checked) {
                    return [...new Set([...prev, id])];
                }
                return prev.filter((item) => item !== id);
            });

            // Update select all checkbox state
            const totalCheckboxes = $(".select-item").length;
            const checkedCheckboxes = $(".select-item:checked").length;
            $(".select-all").prop(
                "checked",
                totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes
            );
        });

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
            $(document).off("change", ".select-all");
            $(document).off("change", ".select-item");
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
                                {can.createPengeluaran && (
                                <Link
                                    href={route("admin.kaskelompok.create")}
                                    className="btn btn-sm btn-primary mb-3 "
                                >
                                    <i className="bi bi-plus-circle me-2"></i>
                                    Tambah Pengeluaran Kelompok
                                </Link>
                                )}
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
                                                className="btn btn-sm btn-danger"
                                                onClick={handleReset}
                                            >
                                                <i className="bi bi-x-circle me-1"></i>{" "}
                                                Reset
                                            </button>
                                        </div>
                                        <div className="col-auto">
                                            <button
                                                className="btn btn-sm btn-success mt-2 me-2"
                                                onClick={handleMultipleVerify}
                                                disabled={!selectedIds.length}
                                            >
                                                <i className="bi bi-check2-all me-1"></i>
                                                Verifikasi Terpilih (
                                                {selectedIds.length})
                                            </button>
                                            <button
                                                className="btn btn-sm btn-danger mt-2"
                                                onClick={handleMultipleDelete}
                                                disabled={!selectedIds.length}
                                            >
                                                <i className="bi bi-trash me-1"></i>
                                                Hapus Terpilih (
                                                {selectedIds.length})
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
                                                <th>
                                                    <input
                                                        type="checkbox"
                                                        className="form-check-input select-all"
                                                        title="Pilih Semua"
                                                    />
                                                </th>
                                                <th>No</th>
                                                <th>AKSI</th>
                                                <th>TANGGAL</th>
                                                <th>JUMLAH</th>
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
