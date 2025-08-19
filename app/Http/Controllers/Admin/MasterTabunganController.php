<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\MasterTabungan;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class MasterTabunganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = MasterTabungan::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return [
                        'edit_url' => route('admin.mastertabungan.edit', $row->id),
                        'delete_url' => route('admin.mastertabungan.destroy', $row->id)
                    ];
                })
                ->make(true);
        }

        return Inertia::render('Admin/MasterTabungan/Index', [
            'title' => 'Data Personal Tabungan',
            'flash' => [
                'message' => session('message')
            ],
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/MasterTabungan/Create', [
            'title' => 'Tambah Data Personal',
            'action' => route('admin.mastertabungan.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jatah' => 'required',
            'status' => 'required',]);

        MasterTabungan::create([
            'nama' => $request->nama,
            'jatah' => $request->jatah,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.mastertabungan.index')
            ->with('message', 'Data Personal berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mastertabungan = MasterTabungan::findOrFail($id);

        return Inertia::render('Admin/MasterTabungan/Create', [
            'title' => 'Edit Data Personal',
            'mastertabungan' => $mastertabungan,
            'action' => route('admin.mastertabungan.update', $mastertabungan->id),
            'method' => 'PUT',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mastertabungan = MasterTabungan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jatah' => 'required',
            'status' => 'required',]);

        $mastertabungan->update([
            'nama' => $request->nama,
            'jatah' => $request->jatah,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.mastertabungan.index')
            ->with('message', 'Data Personal berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mastertabungan = MasterTabungan::findOrFail($id);
        $mastertabungan->delete();

        return redirect()->route('admin.mastertabungan.index')->with('message', 'Data Personal berhasil dihapus');
    }
}
