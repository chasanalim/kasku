import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head } from "@inertiajs/react";
import Highcharts from "highcharts";
import HighchartsReact from "highcharts-react-official";
import { useMemo } from "react";

export default function Dashboard({ kas_kelompok }) {
    const formatCurrency = (number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        }).format(number);
    };

    // Sinkronkan data pemasukan dan pengeluaran
    const processChartData = () => {
        // Gabungkan semua bulan yang unik
        const allMonths = [
            ...new Set([
                ...kas_kelompok.pemasukan.map(
                    (item) => `${item.tahun}-${item.bulan}`
                ),
                ...kas_kelompok.pengeluaran.map(
                    (item) => `${item.tahun}-${item.bulan}`
                ),
            ]),
        ].sort();

        // Buat data yang terstruktur
        const chartData = allMonths.map((monthKey) => {
            const [year, month] = monthKey.split("-");
            const pemasukan = kas_kelompok.pemasukan.find(
                (item) =>
                    item.tahun === parseInt(year) &&
                    item.bulan === parseInt(month)
            );
            const pengeluaran = kas_kelompok.pengeluaran.find(
                (item) =>
                    item.tahun === parseInt(year) &&
                    item.bulan === parseInt(month)
            );

            return {
                bulan: month,
                tahun: year,
                nama_bulan:
                    pemasukan?.nama_bulan ||
                    new Date(year, month - 1).toLocaleString("id-ID", {
                        month: "long",
                    }),
                pemasukan: pemasukan ? parseFloat(pemasukan.total) : 0,
                pengeluaran: pengeluaran ? parseFloat(pengeluaran.total) : 0,
            };
        });

        return chartData;
    };

    // Prepare chart options dengan data yang sudah disinkronkan
    const chartOptions = useMemo(() => {
        const chartData = processChartData();

        return {
            chart: {
                type: "column",
                height: 400,
                backgroundColor: "transparent",
            },
            title: {
                text: "Pemasukan & Pengeluaran Kas Kelompok",
                style: {
                    fontSize: "18px",
                    fontWeight: "600",
                },
            },
            xAxis: {
                categories: chartData.map(
                    (item) => `${item.nama_bulan} ${item.tahun}`
                ),
                labels: {
                    style: {
                        fontSize: "12px",
                    },
                },
            },
            yAxis: {
                title: {
                    text: "Jumlah (IDR)",
                    style: {
                        fontSize: "12px",
                    },
                },
                labels: {
                    formatter: function () {
                        return formatCurrency(this.value).replace("Rp", "");
                    },
                },
            },
            series: [
                {
                    name: "Pemasukan",
                    data: chartData.map((item) => item.pemasukan),
                    color: "#198754",
                },
                {
                    name: "Pengeluaran",
                    data: chartData.map((item) => item.pengeluaran),
                    color: "#dc3545",
                },
            ],
            credits: {
                enabled: false,
            },
            plotOptions: {
                column: {
                    borderRadius: 5,
                },
            },
        };
    }, [kas_kelompok]);

    return (
        <AdminLayout header={<h2 className="fs-4 fw-semibold">Dashboard</h2>}>
            <Head title="Dashboard" />
            <section>
                <div class="card m-3 shadow">
                    <h4 className="text-center mt-3 mb-0 fw-bold">KAS KELOMPOK</h4>
                        <div className="container">
                            {/* Summary Cards */}
                            <div className="row g-4 mb-4 m-1 mt-0">
                                <div className="col-12 col-md-4">
                                    <div className="card h-100 border-0 shadow">
                                        <div className="card-body">
                                            <div className="d-flex align-items-center justify-content-between mb-3">
                                                <div>
                                                    <h6 className="text-muted mb-1">
                                                        Saldo Kas kelompok
                                                    </h6>
                                                    <h3 className="fw-bold mb-0">
                                                        {formatCurrency(
                                                            kas_kelompok.summary
                                                                .saldo_kas
                                                        )}
                                                    </h3>
                                                </div>
                                                <div className="p-3 bg-primary bg-opacity-10 rounded-3">
                                                    <i className="bi bi-wallet2 text-primary fs-4"></i>
                                                </div>
                                            </div>
                                            <div
                                                className="progress"
                                                style={{ height: "4px" }}
                                            >
                                                <div
                                                    className="progress-bar bg-primary"
                                                    style={{ width: "100%" }}
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="col-12 col-md-4">
                                    <div className="card h-100 border-0 shadow">
                                        <div className="card-body">
                                            <div className="d-flex align-items-center justify-content-between mb-3">
                                                <div>
                                                    <h6 className="text-muted mb-1">
                                                        Pemasukan Bulan Ini
                                                    </h6>
                                                    <h3 className="fw-bold mb-0">
                                                        {formatCurrency(
                                                            kas_kelompok.summary
                                                                .pemasukan_bulan_ini
                                                        )}
                                                    </h3>
                                                </div>
                                                <div className="p-3 bg-success bg-opacity-10 rounded-3">
                                                    <i className="bi bi-graph-up-arrow text-success fs-4"></i>
                                                </div>
                                            </div>
                                            <div
                                                className="progress"
                                                style={{ height: "4px" }}
                                            >
                                                <div
                                                    className="progress-bar bg-success"
                                                    style={{ width: "100%" }}
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="col-12 col-md-4">
                                    <div className="card h-100 border-0 shadow">
                                        <div className="card-body">
                                            <div className="d-flex align-items-center justify-content-between mb-3">
                                                <div>
                                                    <h6 className="text-muted mb-1">
                                                        Pengeluaran Bulan Ini
                                                    </h6>
                                                    <h3 className="fw-bold mb-0">
                                                        {formatCurrency(
                                                            kas_kelompok.summary
                                                                .pengeluaran_bulan_ini
                                                        )}
                                                    </h3>
                                                </div>
                                                <div className="p-3 bg-danger bg-opacity-10 rounded-3">
                                                    <i className="bi bi-graph-down-arrow text-danger fs-4"></i>
                                                </div>
                                            </div>
                                            <div
                                                className="progress"
                                                style={{ height: "4px" }}
                                            >
                                                <div
                                                    className="progress-bar bg-danger"
                                                    style={{ width: "100%" }}
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Chart */}
                            <div className="card border-0 shadow m-1">
                                <div className="card-body">
                                    <HighchartsReact
                                        highcharts={Highcharts}
                                        options={chartOptions}
                                    />
                                </div>
                            </div>
                        </div>
                </div>
            </section>
        </AdminLayout>
    );
}
