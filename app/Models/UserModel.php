<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserModel extends Authenticatable implements JWTSubject
{
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    use HasFactory;


    protected $table = 'm_user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'level_id',
        'username',
        'nama',
        'password',
        'avatar',
        'image',
    ];

    protected $hidden = [
        'password', //sembunyikan pw saat select
    ];

    protected $casts = [
        'password' => 'hashed', //casting pw agar dihash otomatis
    ];

    public $timestamps = false;
    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');

    }
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/posts/' . $image),
        );
    }

    //ambil nama role
    public function getRoleName(): string{
        return $this->level->level_nama;
    }

    //cek apakah user memiliki role tertentu
    public function hasRole(string $role): bool{
        return $this->level->level_kode === $role;
    }

    public function getRole(){
        return $this->level->level_kode;
    }
}
