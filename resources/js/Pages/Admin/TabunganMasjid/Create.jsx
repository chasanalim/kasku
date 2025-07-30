import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { Form } from "react-bootstrap";

export default function Create({
    title,
    tabungan,
    jamaah,
    action,
    method = "POST",
}) {
    const today = new Date().toISOString().slice(0, 10);
    const { data, setData, post, put, processing, errors, progress } = useForm({
        tanggal: tabungan?.tanggal || today,
        jamaah_id: tabungan?.jamaah_id || "",
        kategori: "",
        jumlah: tabungan?.jumlah || "",
    });

    useEffect(() => {
        if (data.jamaah_id) {
            const selectedJamaah = jamaah.find(
                (item) => item.id === parseInt(data.jamaah_id)
            );
            if (selectedJamaah) {
                setData("kategori", selectedJamaah.kategori);
            }
        }
    }, [data.jamaah_id]);

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
                                            <div className="mb-3">
                                                <label className="form-label required">
                                                    Nama Jamaah
                                                </label>
                                                <select
                                                    className={`form-select ${
                                                        errors.jamaah_id
                                                            ? "is-invalid"
                                                            : ""
                                                    }`}
                                                    value={data.jamaah_id}
                                                    onChange={(e) =>
                                                        setData(
                                                            "jamaah_id",
                                                            e.target.value
                                                        )
                                                    }
                                                >
                                                    <option value="">
                                                        Pilih Jamaah
                                                    </option>
                                                    {jamaah.map((item) => (
                                                        <option
                                                            key={item.id}
                                                            value={item.id}
                                                        >
                                                            {item.nama}
                                                        </option>
                                                    ))}
                                                </select>
                                                {errors.jamaah_id && (
                                                    <div className="invalid-feedback">
                                                        {errors.jamaah_id}
                                                    </div>
                                                )}
                                                <input
                                                    type="hidden"
                                                    name="kategori"
                                                    value={data.kategori}
                                                />
                                            </div>
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
