<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\AkunRekening;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class AkunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = AkunRekening::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return [
                        'edit_url' => route('admin.akun.edit', $row->id),
                        'delete_url' => route('admin.akun.destroy', $row->id)
                    ];
                })
                ->make(true);
        }

        return Inertia::render('Admin/Akun/Index', [
            'title' => 'Data Akun Rekening',
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
        return Inertia::render('Admin/Akun/Create', [
            'title' => 'Tambah Akun Rekening',
            'action' => route('admin.akun.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response()->json($request->all());
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode_akun' => 'required|unique:akun_rekening,kode_akun',
            'tipe' => 'required',
            'saldo_awal' => 'required|numeric',
        
        ]);

        AkunRekening::create([
            'nama' => $request->nama,
            'kode_akun' => $request->kode_akun,
            'tipe' => $request->tipe,
            'saldo_awal' => $request->saldo_awal,
        ]);

        return redirect()->route('admin.akun.index')
            ->with('message', 'Data Akun Rekening berhasil ditambahkan');
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
        $akun = AkunRekening::findOrFail($id);

        return Inertia::render('Admin/Akun/Create', [
            'title' => 'Edit Akun Rekening',
            'akun' => $akun,
            'action' => route('admin.akun.update', $akun->id),
            'method' => 'PUT',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $akun = AkunRekening::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'kode_akun' => 'required|unique:akun_rekening,kode_akun,'.$id,
            'tipe' => 'required',
            'saldo_awal' => 'required|numeric',
        ]);

        $akun->update([
            'nama' => $request->nama,
            'kode_akun' => $request->kode_akun,
            'tipe' => $request->tipe,
            'saldo_awal' => $request->saldo_awal,
        ]);

        return redirect()->route('admin.akun.index')
            ->with('message', 'Data Akun Rekening berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $akun = AkunRekening::findOrFail($id);
        $akun->delete();

        return redirect()->route('admin.akun.index')->with('message', 'Data Akun Rekening berhasil dihapus');
    }
}
