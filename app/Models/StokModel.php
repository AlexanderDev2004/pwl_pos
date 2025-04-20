<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokModel extends Model
{
    use HasFactory;

    protected $table = 't_stok';
    protected $primaryKey = 'stok_id';

    protected $fillable = [
        'stok_id',
        'supplier_id',
        'barang_id',
        'user_id',
        'stok_tanggal',
        'stok_jumlah',
    ];

    public function supplier() {
        return $this->belongsTo('App\Models\SupplierModel', 'supplier_id', 'supplier_id');
    }
    public function barang() {
        return $this->belongsTo('App\Models\BarangModel', 'barang_id', 'barang_id');
    }
    public function user() {
        return $this->belongsTo('App\Models\UserModel', 'user_id', 'user_id');
    }
}
