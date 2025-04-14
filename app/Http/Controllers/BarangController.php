<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Barang',
            'list' => ['Home', 'Barang']
        ];

        $page = (object)[
            'title' => 'Dafter barang yang terdaftar di dalam sistem',
        ];
        $active_menu = 'barang'; //menu yg sedang aktif
        $kategori = KategoriModel::all();
        return view('barang.index', compact('breadcrumb', 'page', 'kategori', 'active_menu'));
    }

    public function list(Request $request)
    {
        $barangs = BarangModel::select('barang_id', 'kategori_id', 'barang_kode',  'barang_nama', 'harga_beli', 'harga_jual')->with('kategori');
        //filter data barang berdasarkan kategori_id
        if ($request->kategori_id) {
            $barangs->where('kategori_id', $request->kategori_id);
        }
        return datatables()->of($barangs)
            //menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                // $btn = '<a href="' . url("/barang/{$barang->barang_id}") . '" class="btn btn-info btn-sm">Detail</a>';
                // $btn .= '<a href="' . url("/barang/{$barang->barang_id}/edit") . '" class="btn btn-warning btn-sm">Edit</a>';
                // $btn .= '<form class="d-inline-block" method="POST" action="' .
                //     url("/barang/{$barang->barang_id}") . '">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                $btn  = '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    public function create()
    {
        $breadcrumb = (object)[
            'title' => 'Barang',
            'list' => ['Home', 'Barang', 'Tambah Barang']
        ];
        $page = (object)[
            'title' => 'Tambah Barang',
        ];
        $active_menu = 'barang'; //menu yg sedang aktif
        $kategori = KategoriModel::all();
        return view('barang.create', compact('breadcrumb', 'page', 'kategori', 'active_menu'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required',
            'barang_kode' => 'required',
            'barang_nama' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
        ]);

        BarangModel::create($request->all());
        return redirect()->route('barang')->with('success', 'Barang berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $breadcrumb = (object)[
            'title' => 'Barang',
            'list' => ['Home', 'Barang', 'Detail Barang']
        ];
        $page = (object)[
            'title' => 'Detail Barang',
        ];
        $active_menu = 'barang'; //menu yg sedang aktif
        $barang = BarangModel::find($id);
        return view('barang.show', compact('breadcrumb', 'page', 'barang', 'active_menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $breadcrumb = (object)[
            'title' => 'Barang',
            'list' => ['Home', 'Barang', 'Edit Barang']
        ];
        $page = (object)[
            'title' => 'Edit Barang',
        ];
        $active_menu = 'barang'; //menu yg sedang aktif
        $kategori = KategoriModel::all();
        $barang = BarangModel::find($id);
        return view('barang.edit', compact('breadcrumb', 'page', 'barang', 'kategori', 'active_menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kategori_id' => 'required',
            'barang_kode' => 'required',
            'barang_nama' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
        ]);

        BarangModel::find($id)->update($request->all());
        return redirect()->route('barang')->with('success', 'Barang berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $check = BarangModel::find($id);
        if (!$check) {
            return redirect()->route('barang')->with('error', 'Barang tidak ditemukan');
        }
        try {
            BarangModel::destroy($id);
            return redirect()->route('barang')->with('success', 'Barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('barang')->with('error', 'Barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        return view('barang.create_ajax')->with('kategori', $kategori);
    }

    public function store_ajax(Request $request)
    {
        //cek apakah req berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_id' => 'required|integer',
                'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',
                'barang_nama' => 'required|string|max:100',
                'harga_beli' => 'required|numeric',
                'harga_jual' => 'required|numeric',
            ];
            //use validator
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, //status gagal (kalo true ya berhasil )
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors(), //pesan error validasi
                ]);
            }
            BarangModel::create([
                'kategori_id' => $request->kategori_id,
                'barang_kode' => $request->barang_kode,
                'barang_nama' => $request->barang_nama,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual,
            ]);
            return response()->json([
                'status' => true, //status berhasil
                'message' => 'Barang berhasil ditambahkan',
            ]);
        }
    }

    public function show_ajax(string $id)
    {
        $barang = BarangModel::with('kategori')->find($id);
        return view('barang.show_ajax', compact('barang'));
    }


    public function edit_ajax(string $id)
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
        return view('barang.edit_ajax', compact('barang', 'kategori'));
    }


    public function update_ajax(Request $request, $id)
    {
        //cek apakah req berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_id' => 'required|integer',
                'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode,' . $id . ',barang_id',
                'barang_nama' => 'required|string|max:100',
                'harga_beli' => 'required|numeric',
                'harga_jual' => 'required|numeric',
            ];
            //use validator
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, //status gagal (kalo true ya berhasil )
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors(), //pesan error validasi
                ]);
            }

            $check = BarangModel::find($id);
            if ($check) {
                $check->update([
                    'kategori_id' => $request->kategori_id,
                    'barang_kode' => $request->barang_kode,
                    'barang_nama' => $request->barang_nama,
                    'harga_beli' => $request->harga_beli,
                    'harga_jual' => $request->harga_jual,
                ]);
                return response()->json([
                    'status' => true, //status berhasil
                    'message' => 'Data berhasil diubah',
                ]);
            } else {
                return response()->json([
                    'status' => false, //status gagal
                    'message' => 'Data tidak ditemukan',
                ]);
            }
        }
    }

    public function confirm_ajax(string $id)
    {
        $barang = BarangModel::find($id);
        return view('barang.confirm_ajax', compact('barang'));
    }

    public function delete_ajax(Request $request, $id)
    {
        //cek apakh req berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $barang = BarangModel::find($id);
                if ($barang) {
                    $barang->delete();
                    return response()->json([
                        'status' => true, //status berhasil
                        'message' => 'Data berhasil dihapus',
                    ]);
                } else {
                    return response()->json([
                        'status' => false, //status gagal
                        'message' => 'Data tidak ditemukan',
                    ]);
                }
            } catch (\Exception $e) {
                if ($e->getCode() == '23000') {
                    return response()->json([
                        'status' => false, //status gagal
                        'message' => 'Data tidak dapat dihapus karena masih terkait dengan data lain.',
                    ]);
                } else {
                    return response()->json([
                        'status' => false, //status gagal
                        'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
                    ]);
                }
            }
        }
        return redirect('/barang');
    }
    public function import()
    {
        return view('barang.import');
    }


    public function import_ajax(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'file_barang' => ['required', 'mimes:xlsx,xls', 'max:2048']
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi Gagal',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            $file = $request->file('file_barang');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];

            foreach ($data as $baris => $value) {
                if ($baris == 1) continue; // Skip header

                // Validasi data wajib isi
                if (
                    isset($value['A'], $value['B'], $value['C'], $value['D'], $value['E']) &&
                    is_numeric($value['A']) && is_numeric($value['D']) && is_numeric($value['E'])
                ) {
                    $insert[] = [
                        'kategori_id' => $value['A'],
                        'barang_kode' => $value['B'],
                        'barang_nama' => $value['C'],
                        'harga_beli'  => $value['D'],
                        'harga_jual'  => $value['E'],
                       
                    ];
                }
            }

            if (!empty($insert)) {
                BarangModel::insertOrIgnore($insert);

                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Tidak ada data valid yang dapat diimport'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    return redirect('/');
}


    public function export_excel()
    {
        $barang = BarangModel::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
            ->orderBy('kategori_id')
            ->with('kategori')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Harga Beli');
        $sheet->setCellValue('E1', 'Harga Jual');
        $sheet->setCellValue('F1', 'Kategori');

        // Style headers
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        // Populate data
        $row = 2;
        foreach ($barang as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->barang_kode);
            $sheet->setCellValue('C' . $row, $item->barang_nama);
            $sheet->setCellValue('D' . $row, $item->harga_beli);
            $sheet->setCellValue('E' . $row, $item->harga_jual);
            $sheet->setCellValue('F' . $row, $item->kategori->kategori_nama);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->setTitle('Data Barang');

        // Generate and output the file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Barang_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf() {
        $barang = BarangModel::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
            ->orderBy('kategori_id')
            ->orderBy('barang_kode')
            ->with('kategori')
            ->get();

        return Pdf::loadView('barang.export_pdf', ['barang' => $barang])
            ->setPaper('A4', 'landscape')
            ->setOption("isRemoteEnabled", true)
            ->stream('Data Barang'.date('Y-m-d H:i:s').'.pdf');

    }
}
