import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";
import { Form } from "react-bootstrap";

export default function Create({ title, jamaah, action, method = "POST" }) {
    const { data, setData, post, put, processing, errors, progress } = useForm({
        nama: jamaah?.nama || "",
        kategori: jamaah?.kategori || "",
        jatah : jamaah?.jatah || "",
        status: jamaah?.status || "",
    });

    const [formattedJatah, setFormattedJatah] = useState(
        data.jatah ? Number(data.jatah).toLocaleString("id-ID") : ""
    );

    // Handler untuk input jumlah
    const handleJatahChange = (e) => {
        // Hapus semua karakter selain angka
        const raw = e.target.value.replace(/\D/g, "");
        setData("jatah", raw);
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
                                                    NAMA
                                                </Form.Label>
                                                <Form.Control
                                                    type="text"
                                                    value={data.nama}
                                                    onChange={(e) =>
                                                        setData(
                                                            "nama",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.nama}
                                                    placeholder="Masukkan Nama"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.nama}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    KATEGORI
                                                </Form.Label>
                                                <Form.Control
                                                    as="select"
                                                    value={data.kategori}
                                                    onChange={(e) =>
                                                        setData(
                                                            "kategori",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={
                                                        !!errors.kategori
                                                    }
                                                >
                                                    <option value="" disabled>
                                                        Pilih Kategori
                                                    </option>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                </Form.Control>
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.kategori}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label>Jatah</Form.Label>
                                                <Form.Control
                                                    type="text"
                                                    value={formattedJatah}
                                                    onChange={
                                                        handleJatahChange
                                                    }
                                                    isInvalid={!!errors.jatah}
                                                    placeholder="Masukkan Jatah"
                                                    inputMode="numeric"
                                                    autoComplete="off"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.jatah}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    STATUS
                                                </Form.Label>
                                                <Form.Control
                                                    as="select"
                                                    value={data.status}
                                                    onChange={(e) =>
                                                        setData(
                                                            "status",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.status}
                                                >
                                                    <option value="" disabled>
                                                        Pilih Status
                                                    </option>
                                                    <option value="1">
                                                        Aktif
                                                    </option>
                                                    <option value="0">
                                                        Tidak Aktif
                                                    </option>
                                                </Form.Control>
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.status}
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
