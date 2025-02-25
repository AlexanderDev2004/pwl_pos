<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['barang_id' => 1, 'kategori_id' => 1, 'barang_kode' => 'BRG001', 'barang_nama' => 'Esteh', 'harga_beli' => 10000, 'harga_jual' => 15000],
            ['barang_id' => 2, 'kategori_id' => 1, 'barang_kode' => 'BRG002', 'barang_nama' => 'Es Jeruk', 'harga_beli' => 15000, 'harga_jual' => 25000],
            ['barang_id' => 3, 'kategori_id' => 1, 'barang_kode' => 'BRG003', 'barang_nama' => 'Soda Gembira', 'harga_beli' => 20000, 'harga_jual' => 35000],
            ['barang_id' => 4, 'kategori_id' => 2, 'barang_kode' => 'BRG004', 'barang_nama' => 'Nasi Goreng', 'harga_beli' => 30000, 'harga_jual' => 45000],
            ['barang_id' => 5, 'kategori_id' => 2, 'barang_kode' => 'BRG005', 'barang_nama' => 'Ayam Goreng', 'harga_beli' => 40000, 'harga_jual' => 55000],
            ['barang_id' => 6, 'kategori_id' => 2, 'barang_kode' => 'BRG006', 'barang_nama' => 'Steak', 'harga_beli' => 50000, 'harga_jual' => 65000],
            ['barang_id' => 7, 'kategori_id' => 3, 'barang_kode' => 'BRG007', 'barang_nama' => 'Panci', 'harga_beli' => 60000, 'harga_jual' => 75000],
            ['barang_id' => 8, 'kategori_id' => 3, 'barang_kode' => 'BRG008', 'barang_nama' => 'Wajan', 'harga_beli' => 70000, 'harga_jual' => 85000],
            ['barang_id' => 9, 'kategori_id' => 3, 'barang_kode' => 'BRG009', 'barang_nama' => 'Telenan', 'harga_beli' => 80000, 'harga_jual' => 95000],
            ['barang_id' => 10, 'kategori_id' => 4, 'barang_kode' => 'BRG010', 'barang_nama' => 'Topi', 'harga_beli' => 90000, 'harga_jual' => 105000],
            ['barang_id' => 11, 'kategori_id' => 4, 'barang_kode' => 'BRG011', 'barang_nama' => 'Celana', 'harga_beli' => 100000, 'harga_jual' => 115000],
            ['barang_id' => 12, 'kategori_id' => 4, 'barang_kode' => 'BRG012', 'barang_nama' => 'Kaos', 'harga_beli' => 110000, 'harga_jual' => 125000],
            ['barang_id' => 13, 'kategori_id' => 5, 'barang_kode' => 'BRG013', 'barang_nama' => 'Remote', 'harga_beli' => 120000, 'harga_jual' => 135000],
            ['barang_id' => 14, 'kategori_id' => 5, 'barang_kode' => 'BRG014', 'barang_nama' => 'Antena TV', 'harga_beli' => 130000, 'harga_jual' => 145000],
            ['barang_id' => 15, 'kategori_id' => 5, 'barang_kode' => 'BRG015', 'barang_nama' => 'Kipas Angin', 'harga_beli' => 140000, 'harga_jual' => 155000],
        ];

        DB::table('m_barang')->insert($data);
    }
}
