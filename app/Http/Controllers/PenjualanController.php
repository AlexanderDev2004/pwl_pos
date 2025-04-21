<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object)[
            'title' => 'Daftar penjualan yang terdaftar dalam sistem'
        ];

        $active_menu = 'penjualan';

        $user = UserModel::all();

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'user' => $user,
            'active_menu' => $active_menu
        ]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with('user');

        if ($request->user_id) {
            $penjualans->where('user_id', $request->user_id);
        }

        return DataTables::of($penjualans)
            ->addIndexColumn()
            ->addColumn('username', function ($penjualan) {
                return $penjualan->user ? $penjualan->user->username : '-';
            })
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button>';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $user = UserModel::select('user_id', 'username')->get();
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_jual')->get();

        return view('penjualan.create_ajax')
            ->with('user', $user)
            ->with('barang', $barang);
    }

    public function store_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'pembeli' => 'required|string|max:50',
            'penjualan_tanggal' => 'required|date',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'jumlah.*' => 'required|integer|min:1',
            'harga.*' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            DB::beginTransaction();

            $lastId = PenjualanModel::max('penjualan_id') ?? 0;
            $newId = $lastId + 1;
            $kode = 'PJ-' . str_pad($newId, 4, '0', STR_PAD_LEFT);

            $penjualan = PenjualanModel::create([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $kode,
                'penjualan_tanggal' => $request->penjualan_tanggal
            ]);

            $barang_ids = $request->barang_id;
            $harga = $request->harga;
            $jumlah = $request->jumlah;

            for ($i = 0; $i < count($barang_ids); $i++) {
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barang_ids[$i],
                    'harga' => $harga[$i],
                    'jumlah' => $jumlah[$i]
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan',
                'data' => $penjualan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['penjualanDetail.barang', 'user'])->find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ]);
        }

        return view('penjualan.show_ajax', ['penjualan' => $penjualan]);
    }

    public function edit_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['penjualanDetail.barang', 'user'])->find($id);
        $user = UserModel::all();
        $barang = BarangModel::all();

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ]);
        }

        return view('penjualan.edit_ajax', compact('penjualan', 'user', 'barang'));
    }

    public function update_ajax(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'pembeli' => 'required|string|max:50',
            'penjualan_tanggal' => 'required|date',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'jumlah.*' => 'required|integer|min:1',
            'harga.*' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            DB::beginTransaction();

            $penjualan = PenjualanModel::find($id);

            if (!$penjualan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }

            $penjualan->update([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_tanggal' => $request->penjualan_tanggal
            ]);

            PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)->delete();

            $barang_ids = $request->barang_id;
            $harga = $request->harga;
            $jumlah = $request->jumlah;

            for ($i = 0; $i < count($barang_ids); $i++) {
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barang_ids[$i],
                    'harga' => $harga[$i],
                    'jumlah' => $jumlah[$i]
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil diperbarui',
                'data' => $penjualan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ]);
        }
    }

    public function delete_ajax(string $id)
    {
        try {
            DB::beginTransaction();

            $penjualan = PenjualanModel::find($id);

            if (!$penjualan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }

            PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)->delete();
            $penjualan->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['penjualanDetail.barang', 'user'])->find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ]);
        }

        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function import()
    {
        return view('penjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_penjualan' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_penjualan');

            try {
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true);

                DB::beginTransaction();

                try {
                    $insertedCount = 0;
                    $currentPenjualanId = null;

                    if (count($data) > 1) {
                        foreach ($data as $baris => $value) {
                            if ($baris > 1) {
                                if (!empty($value['A']) && !empty($value['B']) && !empty($value['C']) && !empty($value['D'])) {
                                    $penjualan = PenjualanModel::create([
                                        'user_id' => $value['A'],
                                        'pembeli' => $value['B'],
                                        'penjualan_kode' => $value['C'],
                                        'penjualan_tanggal' => $value['D'],
                                    ]);

                                    $currentPenjualanId = $penjualan->penjualan_id;
                                    $insertedCount++;
                                }

                                if (!empty($value['E']) && $currentPenjualanId) {
                                    PenjualanDetailModel::create([
                                        'penjualan_id' => $currentPenjualanId,
                                        'barang_id' => $value['E'],
                                        'harga' => $value['F'],
                                        'jumlah' => $value['G'],
                                    ]);
                                }
                            }
                        }

                        DB::commit();

                        if ($insertedCount > 0) {
                            return response()->json([
                                'status' => true,
                                'message' => 'Data penjualan berhasil diimport'
                            ]);
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => 'Tidak ada data yang diimport'
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'File Excel tidak memiliki data'
                        ]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Gagal import data: ' . $e->getMessage()
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal membaca file Excel: ' . $e->getMessage(),
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $penjualan = PenjualanModel::with(['user', 'penjualanDetail.barang'])
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Penjualan');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Pembeli');
        $sheet->setCellValue('E1', 'User');
        $sheet->setCellValue('F1', 'Kode Barang');
        $sheet->setCellValue('G1', 'Nama Barang');
        $sheet->setCellValue('H1', 'Harga');
        $sheet->setCellValue('I1', 'Jumlah');
        $sheet->setCellValue('J1', 'Subtotal');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($penjualan as $p) {
            $firstRow = true;

            foreach ($p->penjualanDetail as $detail) {
                $sheet->setCellValue('A' . $baris, $firstRow ? $no : '');
                $sheet->setCellValue('B' . $baris, $firstRow ? $p->penjualan_kode : '');
                $sheet->setCellValue('C' . $baris, $firstRow ? $p->penjualan_tanggal : '');
                $sheet->setCellValue('D' . $baris, $firstRow ? $p->pembeli : '');
                $sheet->setCellValue('E' . $baris, $firstRow ? ($p->user ? $p->user->username : '-') : '');
                $sheet->setCellValue('F' . $baris, $detail->barang->barang_kode);
                $sheet->setCellValue('G' . $baris, $detail->barang->barang_nama);
                $sheet->setCellValue('H' . $baris, $detail->harga);
                $sheet->setCellValue('I' . $baris, $detail->jumlah);
                $sheet->setCellValue('J' . $baris, $detail->harga * $detail->jumlah);

                $baris++;
                $firstRow = false;
            }

            $no++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penjualan ' . date('Y-m-d_H-i-s') . '.xlsx';

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
        $penjualan = PenjualanModel::with(['user', 'penjualanDetail.barang'])
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        $penjualan->map(function ($p) {
            $p->total = $p->penjualanDetail->sum(function ($detail) {
                return $detail->harga * $detail->jumlah;
            });
            return $p;
        });

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->render();
        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
