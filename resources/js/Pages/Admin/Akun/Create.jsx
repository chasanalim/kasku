import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import { Form } from "react-bootstrap";

export default function Create({ title, akun, action, method = "POST" }) {
    const { data, setData, post, put, processing, errors, progress } = useForm({
        nama: akun?.nama || "",
        kode_akun: akun?.kode_akun || "",
        tipe: akun?.tipe || "",
        saldo_awal: akun?.saldo_awal || "",
    });
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
                                        <div className="col-md-6">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    NAMA AKUN
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
                                                    placeholder="Masukkan Nama Akun"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.nama}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-6">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    KODE AKUN
                                                </Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={data.kode_akun}
                                                    onChange={(e) =>
                                                        setData(
                                                            "kode_akun",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.kode_akun}
                                                    placeholder="Masukkan Kode Akun"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.kode_akun}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        
                                        <div className="col-md-6">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    TIPE
                                                </Form.Label>
                                                <Form.Control
                                                    as="select"
                                                    value={data.tipe}
                                                    onChange={(e) =>
                                                        setData(
                                                            "tipe",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.tipe}
                                                >
                                                    <option
                                                        value=""
                                                        disabled
                                                    >
                                                        Pilih Tipe Akun
                                                    </option>
                                                    <option value="Kas">
                                                        KAS
                                                    </option>
                                                    <option value="Pendapatan">
                                                        PENDAPATAN
                                                    </option>
                                                    <option value="Pengeluaran">
                                                        PENGELUARAN
                                                    </option>
                                                    <option value="Penampung">
                                                        PENAMPUNG
                                                    </option>
                                                </Form.Control>
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.tipe}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-6">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    SALDO AWAL
                                                </Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={data.saldo_awal}
                                                    onChange={(e) =>
                                                        setData(
                                                            "saldo_awal",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.saldo_awal}
                                                    placeholder="Masukkan Saldo Awal"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.saldo_awal}
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
