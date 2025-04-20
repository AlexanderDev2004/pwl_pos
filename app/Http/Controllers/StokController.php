<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
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
            'title' => 'Dafter Stok yang terdaftar di dalam sistem',
        ];
        $active_menu = 'Stok'; //menu yg sedang aktif
        $Stok = StokModel::all();
        // dd($Stok);
        return view('Stok.index', compact('breadcrumb', 'page', 'Stok', 'active_menu'));
    }

    public function list(Request $request)
    {
        $Stoks = StokModel::select('Stok_id', 'Stok_kode',  'Stok_nama', 'Stok_alamat');
        return datatables()->of($Stoks)
            //menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($Stok) {
                // $btn = '<a href="' . url("/Stok/{$Stok->Stok_id}") . '" class="btn btn-info btn-sm">Detail</a>';
                // $btn .= '<a href="' . url("/Stok/{$Stok->Stok_id}/edit") . '" class="btn btn-warning btn-sm">Edit</a>';
                // $btn .= '<form class="d-inline-block" method="POST" action="' .
                //     url("/Stok/{$Stok->Stok_id}") . '">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                $btn  = '<button onclick="modalAction(\'' . url('/Stok/' . $Stok->Stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/Stok/' . $Stok->Stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/Stok/' . $Stok->Stok_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
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
        $active_menu = 'Stok'; //menu yg sedang aktif
        $Stok = StokModel::all();
        return view('Stok.create', compact('breadcrumb', 'page', 'Stok', 'active_menu'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Stok_kode' => 'required',
            'Stok_nama' => 'required',
            'Stok_alamat' => 'required',
        ]);
        $exist = StokModel::where('Stok_kode', $request->Stok_kode)->first();
        if ($exist) {
            return redirect()->back()->with('error', 'Kode Stok sudah terdaftar');
        } else {
            StokModel::create($request->all());
            return redirect()->route('Stok')->with('success', 'Data Stok berhasil ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $breadcrumb = (object)[
            'title' => 'Stok',
            'list' => ['Home', 'Stok', 'Detail Stok']
        ];
        $page = (object)[
            'title' => 'Detail Stok',
        ];
        $active_menu = 'Stok'; //menu yg sedang aktif
        $Stok = StokModel::find($id);
        return view('Stok.show', compact('breadcrumb', 'page', 'Stok', 'active_menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $breadcrumb = (object)[
            'title' => 'Stok',
            'list' => ['Home', 'Stok', 'Edit Stok']
        ];
        $page = (object)[
            'title' => 'Edit Stok',
        ];
        $active_menu = 'Stok'; //menu yg sedang aktif
        $Stok = StokModel::find($id);
        return view('Stok.edit', compact('breadcrumb', 'page', 'Stok', 'active_menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'Stok_kode' => 'required',
            'Stok_nama' => 'required',
            'Stok_alamat' => 'required',
        ]);
        StokModel::find($id)->update($request->all());
        return redirect()->route('Stok')->with('success', 'Data Stok berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            StokModel::find($id)->delete();
            return redirect('Stok')->with('success', 'Stok deleted successfully');
        } catch (\Exception $e) {
            return redirect('Stok')->with('error', 'Stok gagal dihapus karena masih terdapat barang yang terkait');
        }
    }

    public function create_ajax()
    {
        $Stok = StokModel::select('Stok_id', 'Stok_kode', 'Stok_nama', 'Stok_alamat')->get();

        return view('Stok.create_ajax')->with('Stok', $Stok);
    }

    public function store_ajax(Request $request)
    {
        //cek apakah req berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'Stok_kode' => 'required|string|max:10|unique:m_Stok,Stok_kode',
                'Stok_nama' => 'required|string|min:3|max:100',
                'Stok_alamat' => 'required|string|max:255',
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
            StokModel::create([
                'Stok_kode' => $request->Stok_kode,
                'Stok_nama' => $request->Stok_nama,
                'Stok_alamat' => $request->Stok_alamat,
            ]);
            return response()->json([
                'status' => true, //status berhasil
                'message' => 'Stok berhasil ditambahkan',
            ]);
        }
    }

    public function show_ajax(string $id)
    {
        $Stok = StokModel::find($id);
        return view('Stok.show_ajax', compact('Stok'));
    }


    public function edit_ajax(string $id)
    {
        $Stok = StokModel::find($id);
        return view('Stok.edit_ajax', compact('Stok'));
    }


    public function update_ajax(Request $request, $id)
    {
        //cek apakah req berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'Stok_kode' => 'required|string|max:10|unique:m_Stok,Stok_kode,' . $id . ',Stok_id',
                'Stok_nama' => 'required|string|min:3|max:100',
                'Stok_alamat' => 'required|string|max:255',
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

            $Stok = StokModel::find($id);
            if ($Stok) {
                $Stok->update([
                    'Stok_kode' => $request->Stok_kode,
                    'Stok_nama' => $request->Stok_nama,
                    'Stok_alamat' => $request->Stok_alamat,
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
        $Stok = StokModel::find($id);
        return view('Stok.confirm_ajax', compact('Stok'));
    }

    public function delete_ajax(Request $request, $id)
    {
        //cek apakh req berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $Stok = StokModel::find($id);
                if ($Stok) {
                    $Stok->delete();
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
                if ($e->getCode() == '23000') { //sqlstate:23000
                    return response()->json([
                        'status' => false, //status gagal
                        'message' => 'Data tidak dapat dihapus karena masih terkait dengan data lain.',
                    ]);
                }
                return response()->json([
                    'status' => false, //status gagal
                    'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
                ]);
            }
        }
        return redirect('/Stok');
    }

    public function import(){
        return view('Stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB
                'file_Stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_Stok');  // ambil file dari request

            $reader = IOFactory::createReader('Xlsx');  // load reader file excel
            $reader->setReadDataOnly(true);             // hanya membaca data
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel
            $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif

            $data = $sheet->toArray(null, false, true, true);   // ambil data excel

            $insert = [];
            if (count($data) > 1) { // jika data lebih dari 1 baris
                $insert = [];

                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                        // Check if Stok code already exists
                        if (StokModel::where('Stok_kode', $value['A'])->exists()) {
                            return response()->json([
                                'status' => false,
                                'message' => "Import gagal. Kode Stok '{$value['A']}' sudah terdaftar"
                            ]);
                        }

                        $insert[] = [
                            'Stok_kode' => $value['A'],
                            'Stok_nama' => $value['B'],
                            'Stok_alamat' => $value['C'],
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    foreach ($insert as $row) {
                        StokModel::create($row);
                    }
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
        return redirect('/Stok');
    }

    public function export_excel()
    {
        $Stoks = StokModel::select('Stok_kode', 'Stok_nama', 'Stok_alamat')
            ->orderBy('Stok_kode', 'asc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Stok');
        $sheet->setCellValue('C1', 'Nama Stok');
        $sheet->setCellValue('D1', 'Alamat');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($Stoks as $Stok) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $Stok->Stok_kode);
            $sheet->setCellValue('C' . $baris, $Stok->Stok_nama);
            $sheet->setCellValue('D' . $baris, $Stok->Stok_alamat);
            $baris++;
        }
        foreach (range('A', 'D') as $columnID) {
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
        $Stoks = StokModel::select('Stok_kode', 'Stok_nama', 'Stok_alamat')
            ->orderBy('Stok_kode')
            ->get();

        $pdf = Pdf::loadView('Stok.export_pdf', compact('Stoks'));
        $pdf->setPaper('A4', 'portrait'); //set ukuran kertas dan orientasi
        $pdf->setOptions(["isRemoteEnabled"], true); //set true jika ada gambar
        $pdf->render();

        return $pdf->stream('Data_Stok_' . date('Y-m-d_H-i-s') . '.pdf'); //download file pdf
    }
}
