import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import { useEffect } from "react";
import { Form } from "react-bootstrap";

export default function Create({
    title,
    shodaqah,
    jamaah,
    action,
    method = "POST",
}) {

    const today = new Date().toISOString().slice(0, 10);
    const { data, setData, post, put, processing, errors, progress } = useForm({
        tanggal: shodaqah?.transaksi?.tanggal || today,
        jamaah_id: shodaqah?.jamaah_id || "",
        kategori: "",
        persenan: shodaqah?.persenan || "",
        jimpitan: shodaqah?.jimpitan || "",
        dapur_pusat: shodaqah?.dapur_pusat || "",
        shodaqah_daerah: shodaqah?.shodaqah_daerah || "",
        shodaqah_kelompok: shodaqah?.shodaqah_kelompok || "",
        jumlah: shodaqah?.jumlah || "",
    });

    useEffect(() => {
        const persenan = parseInt(data.persenan) || 0;
        const jimpitan = parseInt(data.jimpitan) || 0;
        const dapur_pusat = parseInt(data.dapur_pusat) || 0;
        const shodaqah_daerah = parseInt(data.shodaqah_daerah) || 0;
        const shodaqah_kelompok = parseInt(data.shodaqah_kelompok) || 0;
        setData(
            "jumlah",
            persenan +
                jimpitan +
                dapur_pusat +
                shodaqah_daerah +
                shodaqah_kelompok
        );
    }, [
        data.persenan,
        data.jimpitan,
        data.dapur_pusat,
        data.shodaqah_daerah,
        data.shodaqah_kelompok,
    ]);

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
                                                <Form.Label className="required">
                                                    Persenan
                                                </Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={data.persenan}
                                                    onChange={(e) =>
                                                        setData(
                                                            "persenan",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={
                                                        !!errors.persenan
                                                    }
                                                    placeholder="Masukkan Nominal Persenan"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.persenan}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    Dapur Pusat
                                                </Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={data.dapur_pusat}
                                                    onChange={(e) =>
                                                        setData(
                                                            "dapur_pusat",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={
                                                        !!errors.dapur_pusat
                                                    }
                                                    placeholder="Masukkan Nominal Dapur Pusat"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.dapur_pusat}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    Shodaqah Daerah
                                                </Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={data.shodaqah_daerah}
                                                    onChange={(e) =>
                                                        setData(
                                                            "shodaqah_daerah",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={
                                                        !!errors.shodaqah_daerah
                                                    }
                                                    placeholder="Masukkan Nominal Shodaqah Daerah"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.shodaqah_daerah}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    Shodaqah Kelompok
                                                </Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={
                                                        data.shodaqah_kelompok
                                                    }
                                                    onChange={(e) =>
                                                        setData(
                                                            "shodaqah_kelompok",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={
                                                        !!errors.shodaqah_kelompok
                                                    }
                                                    placeholder="Masukkan Nominal Shodaqah Kelompok"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.shodaqah_kelompok}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    Jimpitan
                                                </Form.Label>
                                                <Form.Control
                                                    type="number"
                                                    value={data.jimpitan}
                                                    onChange={(e) =>
                                                        setData(
                                                            "jimpitan",
                                                            e.target.value
                                                        )
                                                    }
                                                    isInvalid={
                                                        !!errors.jimpitan
                                                    }
                                                    placeholder="Masukkan Nominal Jimpitan"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.jimpitan}
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
