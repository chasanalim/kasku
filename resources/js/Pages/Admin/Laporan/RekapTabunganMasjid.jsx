import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import $ from "jquery";
import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { Modal } from "bootstrap";
// Remove Toast and Tooltip if not used

// Import bootstrap JS
import "bootstrap/dist/js/bootstrap.bundle.min.js";

export default function RekapTabunganMasjid({ title, flash }) {
    const tableRef = useRef();
    const [detailData, setDetailData] = useState([]);
    const [selectedJamaah, setSelectedJamaah] = useState(null);

    // Format currency
    const formatCurrency = (number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        }).format(number);
    };

    // Fetch detail data
    const fetchDetailData = async (jamaahId) => {
        try {
            const response = await fetch(
                route("admin.laporan.tabungan-detail", { jamaah_id: jamaahId })
            );
            const data = await response.json();
            setDetailData(data);
        } catch (error) {
            console.error("Error fetching detail:", error);
        }
    };

    useEffect(() => {
        const dt = $(tableRef.current).DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 100,
            lengthMenu: [
                [100, 500, -1],
                [100, 500, "All"],
            ],
            ajax: {
                url: route("admin.laporan.rekap-tabungan"),
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
                    data: "nama",
                    name: "nama",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: "jatah",
                    name: "jatah",
                    className: "text-end",
                    render: function (data) {
                        return `Rp ${parseInt(data).toLocaleString("id-ID")}`;
                    },
                    searchable: false,
                },
                {
                    data: "total_tabungan",
                    name: "total_tabungan",
                    className: "text-end",
                    render: function (data) {
                        return `Rp ${parseInt(data).toLocaleString("id-ID")}`;
                    },
                    searchable: false,
                },
                {
                    data: "percentage_format",
                    name: "percentage",
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                    render: function (data, type, row) {
                        const percentage = parseInt(row.percentage);
                        let className = "btn btn-sm ";
                        if (percentage >= 100) className += "btn-success";
                        else if (percentage >= 75) className += "btn-info";
                        else if (percentage >= 50) className += "btn-warning";
                        else className += "btn-danger";

                        return `<button class="${className}" data-jamaah-id="${row.jamaah_id}" data-jamaah-name="${row.nama}">${percentage}%</button>`;
                    },
                },
            ],
            footerCallback: function (row, data, start, end, display) {
                const api = this.api();

                // Calculate total jatah
                const totalJatah = api
                    .column(2, { page: "current" })
                    .data()
                    .reduce((a, b) => {
                        return parseInt(a) + parseInt(b);
                    }, 0);

                // Calculate total tabungan
                const totalTabungan = api
                    .column(3, { page: "current" })
                    .data()
                    .reduce((a, b) => {
                        return parseInt(a) + parseInt(b);
                    }, 0);

                // Update footer
                $(api.column(2).footer()).html(
                    `Rp ${totalJatah.toLocaleString("id-ID")}`
                );
                $(api.column(3).footer()).html(
                    `Rp ${totalTabungan.toLocaleString("id-ID")}`
                );
            },
        });

        // Add click handler for percentage buttons
        $(tableRef.current).on("click", "button", function () {
            const jamaahId = $(this).data("jamaah-id");
            const jamaahName = $(this).data("jamaah-name");
            setSelectedJamaah(jamaahName);
            fetchDetailData(jamaahId);

            // Use Bootstrap's Modal class directly
            const modal = new Modal(document.getElementById("detailModal"));
            modal.show();
        });

        return () => {
            dt.destroy();
        };
    }, [flash]);

    useEffect(() => {
        return () => {
            // Clean up modal when component unmounts
            const modalElement = document.getElementById("detailModal");
            if (modalElement) {
                const modalInstance = Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.dispose();
                }
            }
        };
    }, []);

    return (
        <AdminLayout>
            <Head title={title} />
            <div className="container-fluid py-4">
                <div className="row">
                    <div className="col-12">
                        <div className="card">
                            <div className="card-header pb-0">
                                <h5 className="mb-0 fw-bold">{title}</h5>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table
                                        ref={tableRef}
                                        className="table table-sm"
                                    >
                                        <thead>
                                            <tr className="text-center">
                                                <th>NO</th>
                                                <th>NAMA JAMAAH</th>
                                                <th>TARGET TABUNGAN</th>
                                                <th>TOTAL TABUNGAN</th>
                                                <th>PERSENTASE</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th
                                                    colSpan="2"
                                                    className="text-end"
                                                >
                                                    Total:
                                                </th>
                                                <th className="text-end"></th>
                                                <th className="text-end"></th>
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

            {/* Add Modal */}
            <div className="modal fade" id="detailModal" tabIndex="-1">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">
                                Rincian Tabungan {selectedJamaah}
                            </h5>
                            <button
                                type="button"
                                className="btn-close"
                                data-bs-dismiss="modal"
                            ></button>
                        </div>
                        <div className="modal-body">
                            <div className="table-responsive">
                                <table className="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th className="text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {detailData.map((item, index) => (
                                            <tr key={index}>
                                                <td>
                                                    {new Date(
                                                        item.tanggal
                                                    ).toLocaleDateString(
                                                        "id-ID"
                                                    )}
                                                </td>
                                                <td className="text-end">
                                                    {formatCurrency(
                                                        item.jumlah
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th className="text-end">
                                                {formatCurrency(
                                                    detailData.reduce(
                                                        (sum, item) =>
                                                            sum +
                                                            parseFloat(
                                                                item.jumlah
                                                            ),
                                                        0
                                                    )
                                                )}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
