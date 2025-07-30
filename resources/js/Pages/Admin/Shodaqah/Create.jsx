import AdminLayout from "@/Layouts/admin/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { Form } from "react-bootstrap";

const KATEGORI_VALUES = {
    A: {
        dapur_pusat: 40000,
        shodaqah_daerah: 27000,
        shodaqah_kelompok: 8000,
    },
    B: {
        dapur_pusat: 25000,
        shodaqah_daerah: 17000,
        shodaqah_kelompok: 8000,
    },
    C: {
        dapur_pusat: 20000,
        shodaqah_daerah: 12000,
        shodaqah_kelompok: 8000,
    },
    D: {
        dapur_pusat: 0,
        shodaqah_daerah: 0,
        jimpitan: 0,
    },
};

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
        if (data.jamaah_id) {
            const selectedJamaah = jamaah.find(
                (item) => item.id === parseInt(data.jamaah_id)
            );
            if (selectedJamaah) {
                setData((data) => ({
                    ...data,
                    kategori: selectedJamaah.kategori,
                    jimpitan:
                        KATEGORI_VALUES[selectedJamaah.kategori]?.jimpitan ||
                        "",
                    dapur_pusat:
                        KATEGORI_VALUES[selectedJamaah.kategori]?.dapur_pusat ||
                        "",
                    shodaqah_daerah:
                        KATEGORI_VALUES[selectedJamaah.kategori]
                            ?.shodaqah_daerah || "",
                    shodaqah_kelompok:
                        KATEGORI_VALUES[selectedJamaah.kategori]
                            ?.shodaqah_kelompok || "",
                }));

                // Update formatted values
                setFormattedJimpitan(
                    KATEGORI_VALUES[
                        selectedJamaah.kategori
                    ]?.jimpitan?.toLocaleString("id-ID") || ""
                );
                setFormattedDapurPusat(
                    KATEGORI_VALUES[
                        selectedJamaah.kategori
                    ]?.dapur_pusat?.toLocaleString("id-ID") || ""
                );
                setFormattedShodaqahDaerah(
                    KATEGORI_VALUES[
                        selectedJamaah.kategori
                    ]?.shodaqah_daerah?.toLocaleString("id-ID") || ""
                );
                setFormattedShodaqahKelompok(
                    KATEGORI_VALUES[
                        selectedJamaah.kategori
                    ]?.shodaqah_kelompok?.toLocaleString("id-ID") || ""
                );
            }
        } else {
            // Reset values when no jamaah is selected
            setData((data) => ({
                ...data,
                kategori: "",
                jimpitan: "",
                dapur_pusat: "",
                shodaqah_daerah: "",
                shodaqah_kelompok: "",
            }));
            setFormattedJimpitan("");
            setFormattedDapurPusat("");
            setFormattedShodaqahDaerah("");
            setFormattedShodaqahKelompok("");
        }
    }, [data.jamaah_id]);

    // Tambahkan state untuk tampilan jumlah terformat
    const [formattedJumlah, setFormattedJumlah] = useState(
        data.jumlah ? Number(data.jumlah).toLocaleString("id-ID") : ""
    );
    const [formattedDapurPusat, setFormattedDapurPusat] = useState(
        data.dapur_pusat ? Number(data.dapur_pusat).toLocaleString("id-ID") : ""
    );
    const [formattedJimpitan, setFormattedJimpitan] = useState(
        data.jimpitan ? Number(data.jimpitan).toLocaleString("id-ID") : ""
    );
    const [formattedPersenan, setFormattedPersenan] = useState(
        data.persenan ? Number(data.persenan).toLocaleString("id-ID") : ""
    );
    const [formattedShodaqahDaerah, setFormattedShodaqahDaerah] = useState(
        data.shodaqah_daerah
            ? Number(data.shodaqah_daerah).toLocaleString("id-ID")
            : ""
    );
    const [formattedShodaqahKelompok, setFormattedShodaqahKelompok] = useState(
        data.shodaqah_kelompok
            ? Number(data.shodaqah_kelompok).toLocaleString("id-ID")
            : ""
    );

    // Handler untuk input jumlah
    const handlePersenanChange = (e) => {
        // Hapus semua karakter selain angka
        const raw = e.target.value.replace(/\D/g, "");
        setData("persenan", raw);
        setFormattedPersenan(raw ? Number(raw).toLocaleString("id-ID") : "");
    };
    const handleJimpitanChange = (e) => {
        // Hapus semua karakter selain angka
        const raw = e.target.value.replace(/\D/g, "");
        setData("jimpitan", raw);
        setFormattedJimpitan(raw ? Number(raw).toLocaleString("id-ID") : "");
    };
    const handleDapurPusatChange = (e) => {
        // Hapus semua karakter selain angka
        const raw = e.target.value.replace(/\D/g, "");
        setData("dapur_pusat", raw);
        setFormattedDapurPusat(raw ? Number(raw).toLocaleString("id-ID") : "");
    };
    const handleShodaqahDaerahChange = (e) => {
        // Hapus semua karakter selain angka
        const raw = e.target.value.replace(/\D/g, "");
        setData("shodaqah_daerah", raw);
        setFormattedShodaqahDaerah(
            raw ? Number(raw).toLocaleString("id-ID") : ""
        );
    };
    const handleShodaqahKelompokChange = (e) => {
        // Hapus semua karakter selain angka
        const raw = e.target.value.replace(/\D/g, "");
        setData("shodaqah_kelompok", raw);
        setFormattedShodaqahKelompok(
            raw ? Number(raw).toLocaleString("id-ID") : ""
        );
    };
    const handleJumlahChange = (e) => {
        // Hapus semua karakter selain angka
        const raw = e.target.value.replace(/\D/g, "");
        setData("jumlah", raw);
        setFormattedJumlah(raw ? Number(raw).toLocaleString("id-ID") : "");
    };

    useEffect(() => {
        const persenan = parseInt(data.persenan) || 0;
        const jimpitan = parseInt(data.jimpitan) || 0;
        const dapur_pusat = parseInt(data.dapur_pusat) || 0;
        const shodaqah_daerah = parseInt(data.shodaqah_daerah) || 0;
        const shodaqah_kelompok = parseInt(data.shodaqah_kelompok) || 0;
        const total =
            persenan +
            jimpitan +
            dapur_pusat +
            shodaqah_daerah +
            shodaqah_kelompok;
        setData("jumlah", total);
        setFormattedJumlah(total ? total.toLocaleString("id-ID") : "");
    }, [
        data.persenan,
        data.jimpitan,
        data.dapur_pusat,
        data.shodaqah_daerah,
        data.shodaqah_kelompok,
    ]);
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
                                                            {item.nama} |{" "}
                                                            {item.kategori}
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
                                                    type="text"
                                                    value={formattedPersenan}
                                                    onChange={
                                                        handlePersenanChange
                                                    }
                                                    isInvalid={
                                                        !!errors.persenan
                                                    }
                                                    placeholder="Masukkan Persenan"
                                                    inputMode="numeric"
                                                    autoComplete="off"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.persenan}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    Jimpitan
                                                </Form.Label>
                                                <Form.Control
                                                    type="text"
                                                    value={formattedJimpitan}
                                                    onChange={
                                                        handleJimpitanChange
                                                    }
                                                    isInvalid={
                                                        !!errors.jimpitan
                                                    }
                                                    placeholder="Masukkan Jimpitan"
                                                    inputMode="numeric"
                                                    autoComplete="off"
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.jimpitan}
                                                </Form.Control.Feedback>
                                            </Form.Group>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Group className="mb-3">
                                                <Form.Label className="required">
                                                    Dapur Pusat
                                                </Form.Label>
                                                <Form.Control
                                                    type="text"
                                                    value={formattedDapurPusat}
                                                    onChange={
                                                        handleDapurPusatChange
                                                    }
                                                    isInvalid={
                                                        !!errors.dapur_pusat
                                                    }
                                                    placeholder="Masukkan Dapur Pusat"
                                                    inputMode="numeric"
                                                    autoComplete="off"
                                                    // readOnly={!!data.kategori}
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
                                                    type="text"
                                                    value={
                                                        formattedShodaqahDaerah
                                                    }
                                                    onChange={
                                                        handleShodaqahDaerahChange
                                                    }
                                                    isInvalid={
                                                        !!errors.shodaqah_daerah
                                                    }
                                                    placeholder="Masukkan Shodaqah Daerah"
                                                    inputMode="numeric"
                                                    autoComplete="off"
                                                    // readOnly={!!data.kategori}
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
                                                    type="text"
                                                    value={
                                                        formattedShodaqahKelompok
                                                    }
                                                    onChange={
                                                        handleShodaqahKelompokChange
                                                    }
                                                    isInvalid={
                                                        !!errors.shodaqah_kelompok
                                                    }
                                                    placeholder="Masukkan Shodaqah Kelompok"
                                                    inputMode="numeric"
                                                    autoComplete="off"
                                                    // readOnly={!!data.kategori}
                                                />
                                                <Form.Control.Feedback type="invalid">
                                                    {errors.shodaqah_kelompok}
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
                                                    readOnly
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
