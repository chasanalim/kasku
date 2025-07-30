import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import $ from "jquery";
import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { Toast, Tooltip } from "bootstrap";
// Import bootstrap JS
import "bootstrap/dist/js/bootstrap.bundle.min.js";

export default function RekapTabunganMasjid({ title, flash }) {
    const tableRef = useRef();

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
                },
                {
                    data: "total_tabungan",
                    name: "total_tabungan",
                    className: "text-end",
                    render: function (data) {
                        return `Rp ${parseInt(data).toLocaleString("id-ID")}`;
                    },
                },
                {
                    data: "percentage_format",
                    name: "percentage",
                    className: "text-center",
                    orderable: true,
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                const api = this.api();
                
                // Calculate total jatah
                const totalJatah = api
                    .column(2, { page: 'current' })
                    .data()
                    .reduce((a, b) => {
                        return parseInt(a) + parseInt(b);
                    }, 0);

                // Calculate total tabungan
                const totalTabungan = api
                    .column(3, { page: 'current' })
                    .data()
                    .reduce((a, b) => {
                        return parseInt(a) + parseInt(b);
                    }, 0);

                // Update footer
                $(api.column(2).footer()).html(`Rp ${totalJatah.toLocaleString('id-ID')}`);
                $(api.column(3).footer()).html(`Rp ${totalTabungan.toLocaleString('id-ID')}`);
            }
        });

        return () => {
            dt.destroy();
        };
    }, [flash]);

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
                                    <table ref={tableRef} className="table table-sm">
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
                                                <th colSpan="2" className="text-end">Total:</th>
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
        </AdminLayout>
    );
}
