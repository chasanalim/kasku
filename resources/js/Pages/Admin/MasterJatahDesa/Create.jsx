import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";
import { Form } from "react-bootstrap";

export default function Create({ title, jatah, action, method = "POST" }) {
    const { data, setData, post, put, processing, errors, progress } = useForm({
        jatah_desa: jatah?.jatah_desa || "",
        jumlah : jatah?.jatah || "",
    });

    const [formattedJatah, setFormattedJatah] = useState(
        data.jumlah ? Number(data.jumlah).toLocaleString("id-ID") : ""
    );

    // Handler untuk input jumlah
    const handleJatahChange = (e) => {
        // Hapus semua karakter selain angka
        const raw = e.target.value.replace(/\D/g, "");
        setData("jumlah", raw);
        setFormattedJatah(raw ? Number(raw).toLocaleString("id-ID") : "");
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
                                                    Jatah Desa
                                                </Form.Label>
                                                <Form.Control
                                                    type="text"
                                                    value={data.jatah_desa}
                                                    onChange={(e) =>
                                                        setData(
                                                            "jatah_desa",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.jatah_desa}
                                                    placeholder="Masukkan Jatah Desa"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.jatah_desa}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>

                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label>Jumlah</Form.Label>
                                                <Form.Control
                                                    type="text"
                                                    value={formattedJatah}
                                                    onChange={
                                                        handleJatahChange
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
