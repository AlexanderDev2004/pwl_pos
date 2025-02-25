<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['supplier_kode' => 'SUP001', 'supplier_nama' => 'Maju Kiri', 'supplier_alamat' => 'Malang'],
            ['supplier_kode' => 'SUP002', 'supplier_nama' => 'Berkah Tapan', 'supplier_alamat' => 'Polinema'],
            ['supplier_kode' => 'SUP003', 'supplier_nama' => 'Toko Semangat', 'supplier_alamat' => 'Suhat'],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
