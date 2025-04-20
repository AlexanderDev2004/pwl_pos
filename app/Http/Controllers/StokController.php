<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\BarangModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Stok',
            'list' => ['Home', 'Stok']
        ];

        $page = (object)[
            'title' => 'Daftar Stok yang terdaftar di dalam sistem',
        ];
        $active_menu = 'Stok';
        $stok = StokModel::with(['barang', 'supplier', 'user'])->get();
        return view('stok.index', compact('breadcrumb', 'page', 'stok', 'active_menu'));
    }

    public function list(Request $request)
    {
        $stoks = StokModel::with(['barang', 'supplier', 'user'])
            ->select('stok_id', 'supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah');

        return datatables()->of($stoks)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {
                $btn = '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->addColumn('barang', function ($stok) {
                return $stok->barang->barang_nama;
            })
            ->addColumn('supplier', function ($stok) {
                return $stok->supplier->supplier_nama;
            })
            ->addColumn('user', function ($stok) {
                return $stok->user->nama;
            })
            ->editColumn('stok_tanggal', function ($stok) {
                return date('d-m-Y', strtotime($stok->stok_tanggal));
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object)[
            'title' => 'Stok',
            'list' => ['Home', 'Stok', 'Tambah Stok']
        ];
        $page = (object)[
            'title' => 'Tambah Stok',
        ];
        $active_menu = 'Stok';
        $barang = BarangModel::all();
        $supplier = SupplierModel::all();
        $user = UserModel::all();
        return view('stok.create', compact('breadcrumb', 'page', 'active_menu', 'barang', 'supplier', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'user_id' => 'required|integer',
            'stok_tanggal' => 'required|date',
            'stok_jumlah' => 'required|integer|min:1'
        ]);

        StokModel::create([
            'barang_id' => $request->barang_id,
            'supplier_id' => $request->supplier_id,
            'user_id' => $request->user_id,
            'stok_tanggal' => $request->stok_tanggal,
            'stok_jumlah' => $request->stok_jumlah
        ]);

        return redirect()->route('stok')->with('success', 'Data Stok berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $breadcrumb = (object)[
            'title' => 'Stok',
            'list' => ['Home', 'Stok', 'Detail Stok']
        ];
        $page = (object)[
            'title' => 'Detail Stok',
        ];
        $active_menu = 'Stok';
        $stok = StokModel::with(['barang', 'supplier', 'user'])->find($id);
        return view('stok.show', compact('breadcrumb', 'page', 'stok', 'active_menu'));
    }

    public function edit(string $id)
    {
        $breadcrumb = (object)[
            'title' => 'Stok',
            'list' => ['Home', 'Stok', 'Edit Stok']
        ];
        $page = (object)[
            'title' => 'Edit Stok',
        ];
        $active_menu = 'Stok';
        $stok = StokModel::find($id);
        $barang = BarangModel::all();
        $supplier = SupplierModel::all();
        $user = UserModel::all();
        return view('stok.edit', compact('breadcrumb', 'page', 'stok', 'active_menu', 'barang', 'supplier', 'user'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'barang_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'user_id' => 'required|integer',
            'stok_tanggal' => 'required|date',
            'stok_jumlah' => 'required|integer|min:1'
        ]);

        StokModel::find($id)->update($request->all());
        return redirect()->route('stok')->with('success', 'Data Stok berhasil diubah');
    }

    public function destroy(string $id)
    {
        $check = StokModel::find($id);
        if (!$check) {
            return redirect()->route('stok')->with('error', 'Data stok tidak ditemukan');
        }

        try {
            StokModel::destroy($id);
            return redirect()->route('stok')->with('success', 'Data stok berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('stok')->with('error', 'Data stok gagal dihapus karena masih terdapat data lain yang terkait');
        }
    }

    public function create_ajax()
    {
        $barang = BarangModel::all();
        $supplier = SupplierModel::all();
        $user = UserModel::all();
        return view('stok.create_ajax', compact('barang', 'supplier', 'user'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id' => 'required|integer',
                'supplier_id' => 'required|integer',
                'user_id' => 'required|integer',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer|min:1'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            StokModel::create([
                'barang_id' => $request->barang_id,
                'supplier_id' => $request->supplier_id,
                'user_id' => $request->user_id,
                'stok_tanggal' => $request->stok_tanggal,
                'stok_jumlah' => $request->stok_jumlah
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Stok berhasil ditambahkan',
            ]);
        }
    }

    public function show_ajax(string $id)
    {
        $stok = StokModel::with(['barang', 'supplier', 'user'])->find($id);
        return view('stok.show_ajax', compact('stok'));
    }

    public function edit_ajax(string $id)
    {
        $stok = StokModel::find($id);
        $barang = BarangModel::all();
        $supplier = SupplierModel::all();
        $user = UserModel::all();
        return view('stok.edit_ajax', compact('stok', 'barang', 'supplier', 'user'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id' => 'required|integer',
                'supplier_id' => 'required|integer',
                'user_id' => 'required|integer',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer|min:1'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $stok = StokModel::find($id);
            if ($stok) {
                $stok->update([
                    'barang_id' => $request->barang_id,
                    'supplier_id' => $request->supplier_id,
                    'user_id' => $request->user_id,
                    'stok_tanggal' => $request->stok_tanggal,
                    'stok_jumlah' => $request->stok_jumlah
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diubah',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ]);
            }
        }
    }

    public function confirm_ajax(string $id)
    {
        $stok = StokModel::find($id);
        return view('stok.confirm_ajax', compact('stok'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $stok = StokModel::find($id);
                if ($stok) {
                    $stok->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus',
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak ditemukan',
                    ]);
                }
            } catch (\Exception $e) {
                if ($e->getCode() == '23000') {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak dapat dihapus karena masih terkait dengan data lain.',
                    ]);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
                ]);
            }
        }
        return redirect('/stok');
    }

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_stok');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        $insert[] = [
                            'barang_id' => $value['A'],
                            'supplier_id' => $value['B'],
                            'user_id' => $value['C'],
                            'stok_tanggal' => $value['D'],
                            'stok_jumlah' => $value['E'],
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    StokModel::insert($insert);
                    return response()->json([
                        'status' => true,
                        'message' => 'Data Stok berhasil diimport'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/stok');
    }

    public function export_excel()
    {
        $stoks = StokModel::with(['barang', 'supplier', 'user'])
            ->select('stok_id', 'barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('stok_tanggal', 'desc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Barang');
        $sheet->setCellValue('C1', 'Supplier');
        $sheet->setCellValue('D1', 'User');
        $sheet->setCellValue('E1', 'Tanggal');
        $sheet->setCellValue('F1', 'Jumlah Stok');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($stoks as $stok) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $stok->barang->barang_nama);
            $sheet->setCellValue('C' . $baris, $stok->supplier->supplier_nama);
            $sheet->setCellValue('D' . $baris, $stok->user->nama);
            $sheet->setCellValue('E' . $baris, $stok->stok_tanggal);
            $sheet->setCellValue('F' . $baris, $stok->stok_jumlah);
            $baris++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Stok');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Stok_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $stoks = StokModel::with(['barang', 'supplier', 'user'])
            ->select('stok_id', 'barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('stok_tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('stok.export_pdf', compact('stoks'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(["isRemoteEnabled" => true]);
        $pdf->render();

        return $pdf->stream('Data_Stok_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
