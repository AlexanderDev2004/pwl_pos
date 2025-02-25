<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Transaksi 1
            ['detail_id' => 1, 'penjualan_id' => 1, 'barang_id' => 1, 'harga' => 15000, 'jumlah' => 2], // Esteh
            ['detail_id' => 2, 'penjualan_id' => 1, 'barang_id' => 2, 'harga' => 25000, 'jumlah' => 1], // Es Jeruk
            ['detail_id' => 3, 'penjualan_id' => 1, 'barang_id' => 3, 'harga' => 35000, 'jumlah' => 3], // Soda Gembira

            // Transaksi 2
            ['detail_id' => 4, 'penjualan_id' => 2, 'barang_id' => 4, 'harga' => 45000, 'jumlah' => 2], // Nasi Goreng
            ['detail_id' => 5, 'penjualan_id' => 2, 'barang_id' => 5, 'harga' => 55000, 'jumlah' => 1], // Ayam Goreng
            ['detail_id' => 6, 'penjualan_id' => 2, 'barang_id' => 6, 'harga' => 65000, 'jumlah' => 2], // Steak

            // Transaksi 3
            ['detail_id' => 7, 'penjualan_id' => 3, 'barang_id' => 7, 'harga' => 75000, 'jumlah' => 1], // Panci
            ['detail_id' => 8, 'penjualan_id' => 3, 'barang_id' => 8, 'harga' => 85000, 'jumlah' => 2], // Wajan
            ['detail_id' => 9, 'penjualan_id' => 3, 'barang_id' => 9, 'harga' => 95000, 'jumlah' => 3], // Telenan

            // Transaksi 4
            ['detail_id' => 10, 'penjualan_id' => 4, 'barang_id' => 10, 'harga' => 105000, 'jumlah' => 1], // Topi
            ['detail_id' => 11, 'penjualan_id' => 4, 'barang_id' => 11, 'harga' => 115000, 'jumlah' => 2], // Celana
            ['detail_id' => 12, 'penjualan_id' => 4, 'barang_id' => 12, 'harga' => 125000, 'jumlah' => 2], // Kaos

            // Transaksi 5
            ['detail_id' => 13, 'penjualan_id' => 5, 'barang_id' => 13, 'harga' => 135000, 'jumlah' => 1], // Remote
            ['detail_id' => 14, 'penjualan_id' => 5, 'barang_id' => 14, 'harga' => 145000, 'jumlah' => 2], // Antena TV
            ['detail_id' => 15, 'penjualan_id' => 5, 'barang_id' => 15, 'harga' => 155000, 'jumlah' => 3], // Kipas Angin

            // Transaksi 6
            ['detail_id' => 16, 'penjualan_id' => 6, 'barang_id' => 1, 'harga' => 15000, 'jumlah' => 2], // Esteh
            ['detail_id' => 17, 'penjualan_id' => 6, 'barang_id' => 3, 'harga' => 35000, 'jumlah' => 1], // Soda Gembira
            ['detail_id' => 18, 'penjualan_id' => 6, 'barang_id' => 5, 'harga' => 55000, 'jumlah' => 2], // Ayam Goreng

            // Transaksi 7
            ['detail_id' => 19, 'penjualan_id' => 7, 'barang_id' => 7, 'harga' => 75000, 'jumlah' => 1], // Panci
            ['detail_id' => 20, 'penjualan_id' => 7, 'barang_id' => 9, 'harga' => 95000, 'jumlah' => 3], // Telenan
            ['detail_id' => 21, 'penjualan_id' => 7, 'barang_id' => 11, 'harga' => 115000, 'jumlah' => 2], // Celana

            // Transaksi 8
            ['detail_id' => 22, 'penjualan_id' => 8, 'barang_id' => 13, 'harga' => 135000, 'jumlah' => 1], // Remote
            ['detail_id' => 23, 'penjualan_id' => 8, 'barang_id' => 2, 'harga' => 25000, 'jumlah' => 2], // Es Jeruk
            ['detail_id' => 24, 'penjualan_id' => 8, 'barang_id' => 4, 'harga' => 45000, 'jumlah' => 1], // Nasi Goreng

            // Transaksi 9
            ['detail_id' => 25, 'penjualan_id' => 9, 'barang_id' => 6, 'harga' => 65000, 'jumlah' => 2], // Steak
            ['detail_id' => 26, 'penjualan_id' => 9, 'barang_id' => 8, 'harga' => 85000, 'jumlah' => 1], // Wajan
            ['detail_id' => 27, 'penjualan_id' => 9, 'barang_id' => 10, 'harga' => 105000, 'jumlah' => 3], // Topi

            // Transaksi 10
            ['detail_id' => 28, 'penjualan_id' => 10, 'barang_id' => 12, 'harga' => 125000, 'jumlah' => 2], // Kaos
            ['detail_id' => 29, 'penjualan_id' => 10, 'barang_id' => 14, 'harga' => 145000, 'jumlah' => 1], // Antena TV
            ['detail_id' => 30, 'penjualan_id' => 10, 'barang_id' => 15, 'harga' => 155000, 'jumlah' => 3], // Kipas Angin
        ];

        DB::table('t_penjualan_detail')->insert($data);
    }
}
