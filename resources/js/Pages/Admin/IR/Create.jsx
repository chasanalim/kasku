import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import { useEffect } from "react";
import { Form } from "react-bootstrap";

export default function Create({
    title,
    transaksi,
    detailTransaksi,
    jamaah,
    action,
    method = "POST",
}) {
    const { data, setData, post, put, processing, errors, progress } = useForm({
        tanggal: transaksi?.tanggal || "",
        jamaah_id: detailTransaksi?.jamaah_id || "",
        ir: detailTransaksi?.ir || "",
        syiar: detailTransaksi?.syiar || "",
        jumlah: detailTransaksi?.jumlah || "",
        keterangan: transaksi?.keterangan || "",
    });

     useEffect(() => {
        const ir = parseInt(data.ir) || 0;
        const syiar = parseInt(data.syiar) || 0;
        setData("jumlah", ir + syiar);
    }, [data.ir, data.syiar]);

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
                                            </div>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    IR
                                                </Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={data.ir}
                                                    onChange={(e) =>
                                                        setData(
                                                            "ir",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.ir}
                                                    placeholder="Masukkan Nominal IR"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.ir}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    Syiar - syiar
                                                </Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={data.syiar}
                                                    onChange={(e) =>
                                                        setData(
                                                            "syiar",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.syiar}
                                                    placeholder="Masukkan Nominal Syiar"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.syiar}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label>Jumlah</Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={data.jumlah}
                                                    onChange={(e) =>
                                                        setData(
                                                            "jumlah",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={!!errors.jumlah}
                                                    readOnly
                                                    placeholder="Masukkan Jumlah"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.jumlah}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-9">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    Keterangan Transaksi
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
                                                    placeholder="Masukkan Keterangan Transaksi"
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
