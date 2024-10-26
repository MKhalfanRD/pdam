<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';

    protected $primaryKey = 'warga_id';

    protected $fillable = [
        'user_id',
        'nama',
        'alamat',
        'telp,'
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }

    public function pemakaianAir()
    {
        return $this->hasMany(Pemakaian_Air::class, 'warga_id', 'warga_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'warga_id', 'warga_id');
    }

    public function deposito()
    {
        return $this->hasOne(Deposito::class, 'warga_id', 'warga_id');
    }
}
