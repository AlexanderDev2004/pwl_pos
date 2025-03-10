<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    public function index(): View
    {
        $page = (object) ['title' => 'Daftar kategori yang terdaftar dalam sistem.'];
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori'],
        ];

        return view('kategori.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => KategoriModel::all(),
            'active_menu' => 'kategori',
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        if ($request->kategori_id) {
            $kategori->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($kategori)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                return '
                    <a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a>
                    <a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a>
                    <form method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button>
                    </form>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create(): View
    {
        $page = (object) ['title' => 'Tambah Kategori'];
        $breadcrumb = (object) [
            'title' => 'Tambah Kategori',
            'list' => ['Home', 'Kategori', 'Tambah'],
        ];

        return view('kategori.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'active_menu' => 'kategori',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kategori_kode' => 'required|string',
            'kategori_nama' => 'required|string',
        ]);

        KategoriModel::create($request->only(['kategori_kode', 'kategori_nama']));

        return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
    }

    public function show(string $id): View|RedirectResponse
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan.');
        }

        $page = (object) ['title' => 'Detail Kategori'];
        $breadcrumb = (object) [
            'title' => 'Detail Kategori',
            'list' => ['Home', 'Kategori', 'Detail'],
        ];

        return view('kategori.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,
            'active_menu' => 'kategori',
        ]);
    }

    public function edit(string $id): View|RedirectResponse
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan.');
        }

        $page = (object) ['title' => 'Edit Kategori'];
        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list' => ['Home', 'Kategori', 'Edit'],
        ];

        return view('kategori.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,
            'active_menu' => 'kategori',
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan.');
        }

        $request->validate([
            'kategori_kode' => 'required|string',
            'kategori_nama' => 'required|string',
        ]);

        $kategori->update($request->only(['kategori_kode', 'kategori_nama']));

        return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
    }

    public function destroy(string $id): RedirectResponse
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan.');
        }

        $kategori->delete();

        return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus.');
    }
}
