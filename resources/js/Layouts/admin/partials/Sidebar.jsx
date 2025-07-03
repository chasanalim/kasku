import React, { useEffect, useRef, useState } from "react";
import { Link, usePage } from "@inertiajs/react";
import NavLink from "@/Components/NavLink";
import { Nav, NavDropdown } from "react-bootstrap";

export default function Sidebar() {
    const { auth, userProfileImage, can } = usePage().props;
    const [isDropdownPemasukanOpen, setIsDropdownPemasukanOpen] =
        useState(false);
    const [isDropdownPengeluaranOpen, setIsDropdownPengeluaranOpen] =
        useState(false);
    const [isDropdownQurbanOpen, setIsDropdownQurbanOpen] = useState(false);
    const [isDropdownLaporanOpen, setIsDropdownLaporanOpen] = useState(false);

    // Add null checks
    if (!auth?.user) {
        return null;
    }
    // Check if any submenu is active
    const isAnySubmenuPemasukanActive = () => {
        return (
            route().current("admin.shodaqah.index") ||
            route().current("admin.infaq.index") ||
            route().current("admin.pemasukan.index")
        );
    };
    const isAnySubmenuPengeluaranActive = () => {
        return (
            route().current("admin.pengeluaran.index") ||
            route().current("admin.kaskelompok.index")
        );
    };
    const isAnySubmenuLaporanActive = () => {
        return (
            route().current("admin.laporan") ||
            route().current("admin.laporan.rekap-shodaqah") ||
            route().current("admin.laporan.rekap-shodaqah-desa")
        );
    };
    const isAnySubmenuQurbanActive = () => {
        return (
            route().current("admin.jamaah.index") ||
            route().current("admin.jamaah.index")
        );
    };

    // Set dropdown open state on mount and when route changes
    useEffect(() => {
        setIsDropdownPemasukanOpen(isAnySubmenuPemasukanActive());
        setIsDropdownPengeluaranOpen(isAnySubmenuPengeluaranActive());
        setIsDropdownLaporanOpen(isAnySubmenuLaporanActive());
        setIsDropdownQurbanOpen(isAnySubmenuQurbanActive());
    }, []);

    return (
        <div className="sidebar h-100 pt-3">
            <div className="px-4" style={{ maxWidth: "100%" }}>
                <div className="text-center">
                    <NavLink>
                        <span className="text-decoration-none fs-3 text-center text-info fw-bold">
                            KU KRESEK 1
                        </span>
                    </NavLink>
                </div>
                <hr className="text-white border-2" />
                <div className="sidebarnav">
                    <ul className="list-unstyled text-white mt-3">
                        {can.viewDashboard && (
                            <>
                                <li>
                                    <h6 className="text-uppercase mt-3 menu">
                                        Dashboard
                                    </h6>
                                </li>
                                <li>
                                    <NavLink
                                        href={route("admin.dashboard")}
                                        active={route().current(
                                            "admin.dashboard"
                                        )}
                                        className={`sidebar-link rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                            route().current("admin.dashboard")
                                                ? "active"
                                                : ""
                                        }`}
                                    >
                                        <i className="bi bi-clipboard-data fs-5 "></i>
                                        <span className="text-white mt-1 ms-2">
                                            Dashboard
                                        </span>
                                    </NavLink>
                                </li>
                            </>
                        )}
                        {can.viewPemasukan && (
                            <>
                                <li>
                                    <h6 className="text-uppercase mt-3 menu">
                                        Pemasukan
                                    </h6>
                                </li>
                                <li>
                                    <Nav className="sidebar-link rounded-3 d-flex text-decoration-none text-white">
                                        <NavDropdown
                                            show={isDropdownPemasukanOpen}
                                            onToggle={(isOpen) =>
                                                setIsDropdownPemasukanOpen(
                                                    isOpen
                                                )
                                            }
                                            title={
                                                <div className="d-flex align-items-center justify-content-between w-100">
                                                    <div className="d-flex align-items-center">
                                                        <i className="bi bi-cash-stack fs-5 text-white"></i>
                                                        <span className="text-white mt-1 ms-2">
                                                            Dana Masuk
                                                        </span>
                                                    </div>
                                                    <i
                                                        className="bi bi-chevron-down mt-1 text-white"
                                                        style={{
                                                            marginLeft: "95px",
                                                        }}
                                                    ></i>
                                                </div>
                                            }
                                            id="basic-nav-dropdown"
                                        >
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.shodaqah.index"
                                                )}
                                                active={route().current(
                                                    "admin.shodaqah.index"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.shodaqah.index"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>SHODAQAH</span>
                                            </NavDropdown.Item>
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.infaq.index"
                                                )}
                                                active={route().current(
                                                    "admin.infaq.index"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.infaq.index"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>INFAQ RUTIN</span>
                                            </NavDropdown.Item>
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.pemasukan.index"
                                                )}
                                                active={route().current(
                                                    "admin.pemasukan.index"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.pemasukan.index"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>PEMASUKAN KAS</span>
                                            </NavDropdown.Item>
                                        </NavDropdown>
                                    </Nav>
                                </li>
                            </>
                        )}
                        {can.viewPengeluaran && (
                            <>
                                <li>
                                    <h6 className="text-uppercase mt-3 menu">
                                        Pengeluaran
                                    </h6>
                                </li>
                                <li>
                                    <Nav className="sidebar-link rounded-3 d-flex text-decoration-none text-white">
                                        <NavDropdown
                                            show={isDropdownPengeluaranOpen}
                                            onToggle={(isOpen) =>
                                                setIsDropdownPengeluaranOpen(
                                                    isOpen
                                                )
                                            }
                                            title={
                                                <div className="d-flex align-items-center justify-content-between w-100">
                                                    <div className="d-flex align-items-center">
                                                        <i className="bi bi-cash-stack fs-5 text-white"></i>
                                                        <span className="text-white mt-1 ms-2">
                                                            Dana keluar
                                                        </span>
                                                    </div>
                                                    <i
                                                        className="bi bi-chevron-down mt-1 text-white"
                                                        style={{
                                                            marginLeft: "95px",
                                                        }}
                                                    ></i>
                                                </div>
                                            }
                                            id="basic-nav-dropdown"
                                        >
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.kaskelompok.index"
                                                )}
                                                active={route().current(
                                                    "admin.kaskelompok.index"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.kaskelompok.index"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>PENGELUARAN KELOMPOK</span>
                                            </NavDropdown.Item>
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.pengeluaran.index"
                                                )}
                                                active={route().current(
                                                    "admin.pengeluaran.index"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.pengeluaran.index"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>PENGELUARAN KAS</span>
                                            </NavDropdown.Item>
                                        </NavDropdown>
                                    </Nav>
                                </li>
                            </>
                        )}
                        {can.viewLaporan && (
                            <>
                                <li>
                                    <h6 className="text-uppercase mt-3 menu">
                                        Laporan Keuangan
                                    </h6>
                                </li>
                                <li>
                                    <Nav className="sidebar-link rounded-3 d-flex text-decoration-none text-white">
                                        <NavDropdown
                                            show={isDropdownLaporanOpen}
                                            onToggle={(isOpen) =>
                                                setIsDropdownLaporanOpen(isOpen)
                                            }
                                            title={
                                                <div className="d-flex align-items-center justify-content-between w-100">
                                                    <div className="d-flex align-items-center">
                                                        <i className="bi bi-file-earmark-ruled fs-5 text-white"></i>
                                                        <span className="text-white mt-1 ms-2">
                                                            LAPORAN
                                                        </span>
                                                    </div>
                                                    <i
                                                        className="bi bi-chevron-down mt-1 text-white"
                                                        style={{
                                                            marginLeft: "95px",
                                                        }}
                                                    ></i>
                                                </div>
                                            }
                                            id="basic-nav-dropdown"
                                        >
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.jamaah.index"
                                                )}
                                                active={route().current(
                                                    "admin.jamaah.index"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.jamaah.index"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>BUKU BESAR</span>
                                            </NavDropdown.Item>
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.laporan"
                                                )}
                                                active={route().current(
                                                    "admin.jamaah.index"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.jamaah.index"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>LAPORAN KEUANGAN</span>
                                            </NavDropdown.Item>
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.laporan.rekap-shodaqah"
                                                )}
                                                active={route().current(
                                                    "admin.laporan.rekap-shodaqah"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.laporan.rekap-shodaqah"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>REKAP SHODAQAH KELOMPOK</span>
                                            </NavDropdown.Item>
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.laporan.rekap-shodaqah-desa"
                                                )}
                                                active={route().current(
                                                    "admin.laporan.rekap-shodaqah-desa"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.laporan.rekap-shodaqah-desa"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>REKAP SHODAQAH DESA</span>
                                            </NavDropdown.Item>
                                        </NavDropdown>
                                    </Nav>
                                </li>
                            </>
                        )}
                        {can.viewQurban && (
                            <>
                                <li>
                                    <h6 className="text-uppercase mt-3 menu">
                                        Qurban
                                    </h6>
                                </li>
                                <li>
                                    <Nav className="sidebar-link rounded-3 d-flex text-decoration-none text-white">
                                        <NavDropdown
                                            show={isDropdownQurbanOpen}
                                            onToggle={(isOpen) =>
                                                setIsDropdownQurbanOpen(isOpen)
                                            }
                                            title={
                                                <div className="d-flex align-items-center justify-content-between w-100">
                                                    <div className="d-flex align-items-center">
                                                        <i className="bi bi-people fs-5 text-white"></i>
                                                        <span className="text-white mt-1 ms-2">
                                                            Data Qurban
                                                        </span>
                                                    </div>
                                                    <i
                                                        className="bi bi-chevron-down mt-1 text-white"
                                                        style={{
                                                            marginLeft: "95px",
                                                        }}
                                                    ></i>
                                                </div>
                                            }
                                            id="basic-nav-dropdown"
                                        >
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.jamaah.index"
                                                )}
                                                active={route().current(
                                                    "admin.jamaah.index"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.jamaah.index"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>SAPI</span>
                                            </NavDropdown.Item>
                                            <NavDropdown.Item
                                                as={Link}
                                                method="get"
                                                href={route(
                                                    "admin.jamaah.index"
                                                )}
                                                active={route().current(
                                                    "admin.jamaah.index"
                                                )}
                                                className={`rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                                    route().current(
                                                        "admin.jamaah.index"
                                                    )
                                                        ? "active"
                                                        : ""
                                                }`}
                                            >
                                                <span>KAMBING</span>
                                            </NavDropdown.Item>
                                        </NavDropdown>
                                    </Nav>
                                </li>
                            </>
                        )}

                        {can.viewMaster && (
                            <>
                                <li>
                                    <h6 className="text-uppercase mt-3 menu">
                                        Master Data
                                    </h6>
                                </li>
                                <li>
                                    <NavLink
                                        href={route("admin.jamaah.index")}
                                        active={route().current(
                                            "admin.jamaah.index"
                                        )}
                                        className={`sidebar-link rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                            route().current(
                                                "admin.jamaah.index"
                                            )
                                                ? "active"
                                                : ""
                                        }`}
                                    >
                                        <i className="bi bi-person-fill-lock fs-5"></i>
                                        <span className="text-white mt-1 ms-2">
                                            Data Personal
                                        </span>
                                    </NavLink>
                                </li>

                                <li>
                                    <NavLink
                                        href={route("admin.akun.index")}
                                        active={route().current(
                                            "admin.akun.index"
                                        )}
                                        className={`sidebar-link rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                            route().current("admin.akun.index")
                                                ? "active"
                                                : ""
                                        }`}
                                    >
                                        <i className="bi bi-bank2 fs-5 text-white"></i>
                                        <span className="text-white mt-1 ms-2">
                                            Akun Rekening
                                        </span>
                                    </NavLink>
                                </li>
                            </>
                        )}

                        {can.viewUser && (
                            <>
                                <li>
                                    <h6 className="text-uppercase mt-3 menu">
                                        Manajemen User
                                    </h6>
                                </li>

                                <li>
                                    <NavLink
                                        href={route("admin.user.index")}
                                        active={route().current(
                                            "admin.user.index"
                                        )}
                                        className={`sidebar-link rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                            route().current("admin.user.index")
                                                ? "active"
                                                : ""
                                        }`}
                                    >
                                        <i className="bi bi-person-fill-gear fs-5"></i>
                                        <span className="text-white mt-1 ms-2 ms-2">
                                            User
                                        </span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink
                                        href={route("admin.privileges.index")}
                                        active={route().current(
                                            "admin.privileges.index"
                                        )}
                                        className={`sidebar-link rounded-3 py-2 px-3 mb-1 d-flex text-decoration-none text-white ${
                                            route().current(
                                                "admin.privileges.index"
                                            )
                                                ? "active"
                                                : ""
                                        }`}
                                    >
                                        <i className="bi bi-person-fill-gear fs-5"></i>
                                        <span className="text-white mt-1 ms-2 ms-2">
                                            Privileges
                                        </span>
                                    </NavLink>
                                </li>
                            </>
                        )}
                    </ul>
                </div>
            </div>
        </div>
    );
}
