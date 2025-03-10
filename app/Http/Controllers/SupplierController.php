<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    private const SUPPLIER_URL = '/supplier';
    private const ERROR_SUPPLIER_NOT_FOUND = 'Data supplier tidak ditemukan.';

    public function index(Request $request): View
    {
        $page = (object) ['title' => 'Daftar supplier yang terdaftar dalam sistem.'];
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list' => ['Home', 'Supplier', 'Daftar'],
        ];

        $supplier = Supplier::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat');

        if ($request->supplier_id) {
            $supplier->where('supplier_id', $request->supplier_id);
        }

        return view('supplier.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier->get(),
            'active_menu' => 'supplier'
        ]);
    }

    public function create(): View
    {
        $page = (object) ['title' => 'Tambah Supplier'];
        $breadcrumb = (object) [
            'title' => 'Tambah Supplier',
            'list' => ['Home', 'Supplier', 'Tambah'],
        ];

        return view('supplier.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'active_menu' => 'supplier'
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_kode' => 'required|string',
            'supplier_nama' => 'required|string',
            'supplier_alamat' => 'required|string',
        ]);

        Supplier::create([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
            'supplier_alamat' => $request->supplier_alamat,
        ]);

        return redirect(self::SUPPLIER_URL)->with('success', 'Data supplier berhasil disimpan');
    }

    public function show(string $id): RedirectResponse | View
    {
        $page = (object) ['title' => 'Detail Supplier'];
        $breadcrumb = (object) [
            'title' => 'Detail Supplier',
            'list' => ['Home', 'Supplier', 'Detail'],
        ];

        $supplier = Supplier::find($id);
        if (!$supplier) {
            return redirect(self::SUPPLIER_URL)->with('error', self::ERROR_SUPPLIER_NOT_FOUND);
        }

        return view('supplier.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,
            'active_menu' => 'supplier'
        ]);
    }

    public function edit(string $id): RedirectResponse | View
    {
        $page = (object) ['title' => 'Edit Supplier'];
        $breadcrumb = (object) [
            'title' => 'Edit Supplier',
            'list' => ['Home', 'Supplier', 'Edit'],
        ];

        $supplier = Supplier::find($id);
        if (!$supplier) {
            return redirect(self::SUPPLIER_URL)->with('error', self::ERROR_SUPPLIER_NOT_FOUND);
        }

        return view('supplier.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,
            'active_menu' => 'supplier',
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return redirect(self::SUPPLIER_URL)->with('error', self::ERROR_SUPPLIER_NOT_FOUND);
        }

        $request->validate([
            'supplier_kode' => 'required|string',
            'supplier_nama' => 'required|string',
            'supplier_alamat' => 'required|string',
        ]);

        $supplier->update([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
            'supplier_alamat' => $request->supplier_alamat,
        ]);

        return redirect(self::SUPPLIER_URL)->with('success', 'Data supplier berhasil diubah');
    }
}
