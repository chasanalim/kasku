<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Jamaah;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class JamaahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = Jamaah::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return [
                        'edit_url' => route('admin.jamaah.edit', $row->id),
                        'delete_url' => route('admin.jamaah.destroy', $row->id)
                    ];
                })
                ->make(true);
        }

        return Inertia::render('Admin/Jamaah/Index', [
            'title' => 'Data Personal',
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
        return Inertia::render('Admin/Jamaah/Create', [
            'title' => 'Tambah Data Personal',
            'action' => route('admin.jamaah.store'),
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
            'kategori' => 'required',
            'jatah' => 'required',
            'status' => 'required',]);

        Jamaah::create([
            'nama' => $request->nama,
            'jatah' => $request->jatah,
            'kategori' => $request->kategori,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.jamaah.index')
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
        $jamaah = Jamaah::findOrFail($id);

        return Inertia::render('Admin/Jamaah/Create', [
            'title' => 'Edit Data Personal',
            'jamaah' => $jamaah,
            'action' => route('admin.jamaah.update', $jamaah->id),
            'method' => 'PUT',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jamaah = Jamaah::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required',
            'jatah' => 'required',
            'status' => 'required',]);

        $jamaah->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'jatah' => $request->jatah,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.jamaah.index')
            ->with('message', 'Data Personal berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jamaah = Jamaah::findOrFail($id);
        $jamaah->delete();

        return redirect()->route('admin.jamaah.index')->with('message', 'Data Personal berhasil dihapus');
    }
}
