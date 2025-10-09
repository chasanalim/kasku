import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head } from "@inertiajs/react";
import { Chart, registerables } from 'chart.js';
import { Bar } from "react-chartjs-2";
import { useEffect, useState, useMemo } from "react";

// Register ALL Chart.js components
Chart.register(...registerables);

export default function Dashboard({ kas_kelompok, tabungan }) {
    const [isDataReady, setIsDataReady] = useState(false);

    useEffect(() => {
        if (kas_kelompok && tabungan) {
            setIsDataReady(true);
        }
    }, [kas_kelompok, tabungan]);

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

    // Replace Highcharts options with Chart.js options
    const chartOptions = useMemo(() => {
        const chartData = processChartData();

        return {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Pemasukan & Pengeluaran Kas Kelompok",
                    font: {
                        size: 18,
                        weight: "600",
                    },
                },
            },
            scales: {
                y: {
                    ticks: {
                        callback: function (value) {
                            return formatCurrency(value).replace("Rp", "");
                        },
                    },
                    title: {
                        display: true,
                        text: "Jumlah (IDR)",
                    },
                },
            },
        };
    }, []);

    const chartData = useMemo(() => {
        const data = processChartData();

        // Add fallback for empty data
        if (!data || data.length === 0) {
            return {
                labels: [],
                datasets: [
                    {
                        label: "Pemasukan",
                        data: [],
                        backgroundColor: "rgba(25, 135, 84, 0.8)",
                        borderColor: "rgb(25, 135, 84)",
                        borderWidth: 1,
                        borderRadius: 5,
                    },
                    {
                        label: "Pengeluaran",
                        data: [],
                        backgroundColor: "rgba(220, 53, 69, 0.8)",
                        borderColor: "rgb(220, 53, 69)",
                        borderWidth: 1,
                        borderRadius: 5,
                    },
                ],
            };
        }

        return {
            labels: data.map((item) => `${item.nama_bulan} ${item.tahun}`),
            datasets: [
                {
                    label: "Pemasukan",
                    data: data.map((item) => item.pemasukan),
                    backgroundColor: "rgba(25, 135, 84, 0.8)",
                    borderColor: "rgb(25, 135, 84)",
                    borderWidth: 1,
                    borderRadius: 5,
                },
                {
                    label: "Pengeluaran",
                    data: data.map((item) => item.pengeluaran),
                    backgroundColor: "rgba(220, 53, 69, 0.8)",
                    borderColor: "rgb(220, 53, 69)",
                    borderWidth: 1,
                    borderRadius: 5,
                },
            ],
        };
    }, [kas_kelompok]);

    const processChartDataTabungan = () => {
        // Gabungkan semua bulan yang unik
        const allMonths = [
            ...new Set([
                ...tabungan.uang_masuk.map(
                    (item) => `${item.tahun}-${item.bulan}`
                ),
                ...tabungan.uang_keluar.map(
                    (item) => `${item.tahun}-${item.bulan}`
                ),
            ]),
        ].sort();

        // Buat data yang terstruktur
        const chartData = allMonths.map((monthKey) => {
            const [year, month] = monthKey.split("-");
            const uang_masuk = tabungan.uang_masuk.find(
                (item) =>
                    item.tahun === parseInt(year) &&
                    item.bulan === parseInt(month)
            );
            const uang_keluar = tabungan.uang_keluar.find(
                (item) =>
                    item.tahun === parseInt(year) &&
                    item.bulan === parseInt(month)
            );

            return {
                bulan: month,
                tahun: year,
                nama_bulan:
                    uang_masuk?.nama_bulan ||
                    new Date(year, month - 1).toLocaleString("id-ID", {
                        month: "long",
                    }),
                uang_masuk: uang_masuk ? parseFloat(uang_masuk.total) : 0,
                uang_keluar: uang_keluar ? parseFloat(uang_keluar.total) : 0,
            };
        });

        return chartData;
    };

    // Tabungan chart options and data
    const chartTabunganOptions = useMemo(() => {
        return {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Uang Masuk dan Setor Tabungan Masjid Daerah",
                    font: {
                        size: 18,
                        weight: "600",
                    },
                },
            },
            scales: {
                y: {
                    ticks: {
                        callback: function (value) {
                            return formatCurrency(value).replace("Rp", "");
                        },
                    },
                    title: {
                        display: true,
                        text: "Jumlah (IDR)",
                    },
                },
            },
        };
    }, []);

    const chartTabunganData = useMemo(() => {
        const data = processChartDataTabungan();

        return {
            labels: data.map((item) => `${item.nama_bulan} ${item.tahun}`),
            datasets: [
                {
                    label: "Uang Masuk",
                    data: data.map((item) => item.uang_masuk),
                    backgroundColor: "rgba(25, 135, 84, 0.8)",
                    borderColor: "rgb(25, 135, 84)",
                    borderWidth: 1,
                    borderRadius: 5,
                },
                {
                    label: "Setor Uang",
                    data: data.map((item) => item.uang_keluar),
                    backgroundColor: "rgba(220, 53, 69, 0.8)",
                    borderColor: "rgb(220, 53, 69)",
                    borderWidth: 1,
                    borderRadius: 5,
                },
            ],
        };
    }, [tabungan]);

    useEffect(() => {
        console.log('Chart Data:', chartData);
        console.log('Kas Kelompok:', kas_kelompok);
    }, [chartData, kas_kelompok]);

    return (
        <AdminLayout header={<h2 className="fs-4 fw-semibold">Dashboard</h2>}>
            <Head title="Dashboard" />
            <section>
                <div className="card m-3 shadow">
                    <h4 className="text-center mt-3 mb-0 fw-bold">
                        KAS KELOMPOK
                    </h4>
                    <div className="container">
                        {/* Summary Cards */}
                        <div className="row g-4 mb-4 m-1 mt-0">
                            <div className="col-12 col-md-4">
                                <div className="card h-100 border-0 shadow">
                                    <div className="card-body">
                                        <div className="d-flex align-items-center justify-content-between ">
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
                                        <div>
                                            <span
                                                className={`fw-semi-bold ms-1 ${
                                                    kas_kelompok.summary
                                                        .persentase_saldo >= 0
                                                        ? "text-success"
                                                        : "text-danger"
                                                }`}
                                            >
                                                {kas_kelompok.summary
                                                    .persentase_saldo >= 0
                                                    ? "+"
                                                    : ""}
                                                {kas_kelompok.summary.persentase_saldo.toFixed(
                                                    2
                                                )}
                                                %
                                            </span>
                                            <small className="text-muted ms-1">
                                                dari bulan lalu
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="col-12 col-md-4">
                                <div className="card h-100 border-0 shadow">
                                    <div className="card-body">
                                        <div className="d-flex align-items-center justify-content-between ">
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
                                        <div>
                                            <span
                                                className={`fw-semi-bold ms-1 ${
                                                    kas_kelompok.summary
                                                        .persentase_pemasukan >=
                                                    0
                                                        ? "text-success"
                                                        : "text-danger"
                                                }`}
                                            >
                                                {kas_kelompok.summary
                                                    .persentase_pemasukan >= 0
                                                    ? "+"
                                                    : ""}
                                                {kas_kelompok.summary.persentase_pemasukan.toFixed(
                                                    2
                                                )}
                                                %
                                            </span>
                                            <small className="text-muted ms-1">
                                                dari bulan lalu
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="col-12 col-md-4">
                                <div className="card h-100 border-0 shadow">
                                    <div className="card-body">
                                        <div className="d-flex align-items-center justify-content-between">
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
                                        <div>
                                            <span
                                                className={`fw-semi-bold ms-1 ${
                                                    kas_kelompok.summary
                                                        .persentase_pengeluaran >=
                                                    0
                                                        ? "text-success"
                                                        : "text-danger"
                                                }`}
                                            >
                                                {kas_kelompok.summary
                                                    .persentase_pengeluaran >= 0
                                                    ? "+"
                                                    : ""}
                                                {kas_kelompok.summary.persentase_pengeluaran.toFixed(
                                                    2
                                                )}
                                                %
                                            </span>
                                            <small className="text-muted ms-1">
                                                dari bulan lalu
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Chart */}
                        {isDataReady && (
                            <div className="card border-0 shadow m-1 mb-4">
                                <div className="card-body">
                                    <Bar
                                        options={chartOptions}
                                        data={chartData}
                                        height={100}
                                        redraw={true}
                                    />
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </section>
            <section>
                <div className="card m-3 shadow">
                    <h4 className="text-center mt-3 mb-0 fw-bold">
                        TABUNGAN MASJID DAERAH
                    </h4>
                    <div className="container">
                        {/* Summary Cards */}
                        <div className="row g-3 mb-4 m-1 mt-0">
                            <div className="col-12 col-md-3">
                                <div className="card h-100 border-0 shadow">
                                    <div className="card-body">
                                        <div className="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h6 className="text-muted mb-2 ">
                                                    Total Jatah
                                                </h6>
                                                <h4 className="fw-bold mb-0">
                                                    {formatCurrency(
                                                        tabungan.summary
                                                            .total_jatah
                                                    )}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="col-12 col-md-3">
                                <div className="card h-100 border-0 shadow">
                                    <div className="card-body">
                                        <div className="d-flex align-items-center justify-content-between ">
                                            <div>
                                                <h6 className="text-muted mb-2">
                                                    Uang Sudah Masuk
                                                </h6>
                                                <h4 className="fw-bold mb-0">
                                                    {formatCurrency(
                                                        tabungan.summary
                                                            .uang_masuk
                                                    )}
                                                </h4>
                                            </div>
                                        </div>
                                        <div>
                                            <span
                                                className={`fw-semi-bold ms-1 ${
                                                    tabungan.summary
                                                        .persentase_uang_masuk >=
                                                    80
                                                        ? "text-success"
                                                        : "text-danger"
                                                }`}
                                            >
                                                {tabungan.summary.persentase_uang_masuk.toFixed(
                                                    2
                                                )}
                                                %
                                            </span>
                                            <small className="text-muted ms-1">
                                                dari total jatah
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="col-12 col-md-3">
                                <div className="card h-100 border-0 shadow">
                                    <div className="card-body">
                                        <div className="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h6 className="text-muted mb-2">
                                                    Uang Sudah Disetor
                                                </h6>
                                                <h4 className="fw-bold mb-0">
                                                    {formatCurrency(
                                                        tabungan.summary
                                                            .uang_keluar
                                                    )}
                                                </h4>
                                            </div>
                                        </div>
                                        <div>
                                            <span
                                                className={`fw-semi-bold ms-1 ${
                                                    tabungan.summary
                                                        .persentase_uang_keluar >=
                                                    80
                                                        ? "text-success"
                                                        : "text-danger"
                                                }`}
                                            >
                                                {tabungan.summary.persentase_uang_keluar.toFixed(
                                                    2
                                                )}
                                                %
                                            </span>
                                            <small className="text-muted ms-1">
                                                dari total jatah
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-12 col-md-3">
                                <div className="card h-100 border-0 shadow">
                                    <div className="card-body">
                                        <div className="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h6 className="text-muted mb-2">
                                                    Uang Belum Disetor
                                                </h6>
                                                <h4 className="fw-bold mb-0">
                                                    {formatCurrency(
                                                        tabungan.summary
                                                            .uang_belum_setor
                                                    )}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Chart */}
                        {isDataReady && (
                            <div className="card border-0 shadow m-1">
                                <div className="card-body">
                                    <Bar
                                        options={chartTabunganOptions}
                                        data={chartTabunganData}
                                        height={100}
                                        redraw={true}
                                    />
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </section>
        </AdminLayout>
    );
}
