import { Link, usePage } from "@inertiajs/react";
import React, { useEffect } from "react";
import Dropdown from "@/Components/Dropdown";
import { Nav, NavDropdown } from "react-bootstrap";
import classNames from "classnames";

export default function Navbar(props) {
    const { auth } = usePage().props;

    // Add useEffect to handle initial sidebar state
    useEffect(() => {
        const handleResize = () => {
            const main = document.getElementById("main");
            if (window.innerWidth <= 991.98) {
                main.classList.add("activesidebar");
            } else {
                main.classList.remove("activesidebar");
            }
        };

        // Set initial state
        handleResize();

        // Add resize listener
        window.addEventListener("resize", handleResize);

        // Cleanup
        return () => window.removeEventListener("resize", handleResize);
    }, []);

    const activesidebar = () => {
        const main = document.getElementById("main");
        main.classList.toggle("activesidebar");
    };

    // Get current route name to determine active sidebar
    const currentRoute = route().current();
    const getActiveMenu = () => {
        switch (true) {
            // Dashboard
            case currentRoute.startsWith("admin.dashboard"):
                return "Dashboard";

            // Master Data
            case currentRoute.startsWith("admin.jamaah"):
                return "Data Jamaah";
            case currentRoute.startsWith("admin.mastertabungan"):
                return "Master Tabungan";
            case currentRoute.startsWith("admin.akun"):
                return "Akun Rekening";
            case currentRoute.startsWith("admin.user"):
                return "Users";
            case currentRoute.startsWith("admin.privileges"):
                return "Hak Akses";

            // Transaksi
            case currentRoute.startsWith("admin.transaksi"):
                return "Transaksi";
            case currentRoute.startsWith("admin.shodaqah"):
                return "Amplop IR";
            case currentRoute.startsWith("admin.infaq"):
                return "Infaq Pengajian dan Sholat Jumat";
            case currentRoute.startsWith("admin.pemasukan"):
                return "Pemasukan";
            case currentRoute.startsWith("admin.pengeluaran"):
                return "Pengeluaran";
            case currentRoute.startsWith("admin.kaskelompok"):
                return "Kas Kelompok";
            case currentRoute.startsWith("admin.tabungan"):
                return "Tabungan Masjid";

            // Laporan
            case currentRoute.startsWith("admin.laporan.rekap-shodaqah-desa"):
                return "Rekap Shodaqah Desa";
            case currentRoute.startsWith("admin.laporan.rekap-shodaqah"):
                return "Rekap Shodaqah";
            case currentRoute.startsWith("admin.laporan.rekap-tabungan"):
                return "Rekap Tabungan Masjid";
            case currentRoute.startsWith("admin.laporan"):
                return "Laporan";
            case currentRoute.startsWith("admin.buku-besar"):
                return "Buku Besar";
            case currentRoute.startsWith("admin.jurnal"):
                return "Posting Jurnal";

            // Profile
            case currentRoute.startsWith("admin.profile"):
                return "Profile";

            default:
                return "";
        }
    };

    return (
        <header>
            <nav className="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                <div className="container-fluid">
                    <button
                        onClick={activesidebar}
                        className="btn btn-link text-dark"
                    >
                        <i className="bi bi-list fs-4"></i>
                    </button>

                    <div className="sidebar-active ms-1">
                        <span className="fw-bold">{getActiveMenu()}</span>
                    </div>

                    <Nav className="ms-auto">
                        <NavDropdown
                            className="nav-dropdown-dark"
                            align="end"
                            style={{ color: "black" }}
                            title={
                                <>
                                    <i className="bi bi-person me-2"></i>
                                    {` ${auth.user?.name}`}
                                </>
                            }
                            id="basic-nav-dropdown"
                        >
                            <NavDropdown.Item
                                as={Link}
                                method="post"
                                href={route("logout")}
                                preserveScroll
                                onSuccess={() => {
                                    window.location.href = "";
                                }}
                            >
                                <span>Logout</span>
                            </NavDropdown.Item>
                        </NavDropdown>
                    </Nav>
                </div>
            </nav>
        </header>
    );
}
