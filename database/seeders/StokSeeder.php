<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['supplier_id' => 1, 'barang_id' => 1, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 10],
            ['supplier_id' => 2, 'barang_id' => 2, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 20],
            ['supplier_id' => 3, 'barang_id' => 3, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 30],
            ['supplier_id' => 1, 'barang_id' => 4, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 40],
            ['supplier_id' => 2, 'barang_id' => 5, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 50],
            ['supplier_id' => 3, 'barang_id' => 6, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 60],
            ['supplier_id' => 1, 'barang_id' => 7, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 70],
            ['supplier_id' => 2, 'barang_id' => 8, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 80],
            ['supplier_id' => 3, 'barang_id' => 9, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 90],
            ['supplier_id' => 1, 'barang_id' => 10, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 100],
            ['supplier_id' => 2, 'barang_id' => 11, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 110],
            ['supplier_id' => 3, 'barang_id' => 12, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 120],
            ['supplier_id' => 1, 'barang_id' => 13, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 130],
            ['supplier_id' => 2, 'barang_id' => 14, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 140],
            ['supplier_id' => 3, 'barang_id' => 15, 'user_id' => 1, 'stok_tanggal' => Carbon::now(), 'stok_jumlah' => 150],
        ];
        DB::table('t_stok')->insert($data);
    }
}
