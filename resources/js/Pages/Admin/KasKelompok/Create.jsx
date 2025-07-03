import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { Form } from "react-bootstrap";

export default function Create({ title, infaq, action, method = "POST" }) {
    const today = new Date().toISOString().slice(0, 10);
    const { data, setData, post, put, processing, errors, progress } = useForm({
        tanggal: infaq?.transaksi?.tanggal || today,
        jumlah: infaq?.jumlah || "",
        keterangan: infaq?.transaksi?.keterangan || "",
    });
    // Tambahkan state untuk tampilan jumlah terformat
    const [formattedJumlah, setFormattedJumlah] = useState(
        data.jumlah ? Number(data.jumlah).toLocaleString("id-ID") : ""
    );

    // Handler untuk input jumlah
    const handleJumlahChange = (e) => {
        // Hapus semua karakter selain angka
        const raw = e.target.value.replace(/\D/g, "");
        setData("jumlah", raw);
        setFormattedJumlah(raw ? Number(raw).toLocaleString("id-ID") : "");
    };
    const handleSubmit = (e) => {
        e.preventDefault();
        if (method === "PUT") {
            put(action);
        } else {
            post(action);
        }
    };

    return (
        <AdminLayout>
            <Head title={title} />

            <div className="container-fluid py-4">
                <div className="row">
                    <div className="col-12">
                        <div className="card">
                            <div className="card-header pb-0">
                                <div className="card-title">
                                    <h5 className="fw-bold">{title}</h5>
                                </div>
                            </div>
                            <div className="card-body">
                                <form onSubmit={handleSubmit}>
                                    <div className="row">
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    Tanggal
                                                </Form.Label>
                                                <Form.Control
                                                    type="date"
                                                    value={data.tanggal}
                                                    onChange={(e) =>
                                                        setData(
                                                            "tanggal",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.tanggal}
                                                    placeholder="Masukkan Tanggal"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.tanggal}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>

                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label>Jumlah</Form.Label>
                                                <Form.Control
                                                    type="text"
                                                    value={formattedJumlah}
                                                    onChange={
                                                        handleJumlahChange
                                                    }
                                                    isInvalid={!!errors.jumlah}
                                                    placeholder="Masukkan Jumlah"
                                                    inputMode="numeric"
                                                    autoComplete="off"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.jumlah}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-6">
                                            <Form.Group className="mb-3">
                                                <Form.Label>
                                                    Keterangan
                                                </Form.Label>
                                                <Form.Control
                                                    type="text"
                                                    value={data.keterangan}
                                                    onChange={(e) =>
                                                        setData(
                                                            "keterangan",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={
                                                        !!errors.keterangan
                                                    }
                                                    placeholder="Masukkan keterangan"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.keterangan}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                    </div>

                                    <div className="d-flex justify-content-center">
                                        <button
                                            type="submit"
                                            disabled={processing}
                                            className="btn btn-primary"
                                        >
                                            {processing
                                                ? "Menyimpan..."
                                                : "Simpan"}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
