<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $primaryKey = 'pembayaran_id';

    public $timestamps = false;

    protected $fillable = [
        'warga_id',
        'pemakaianAir_id',
        'buktiBayar',
        'waktuBayar',
        // 'tunggakan',
        'status',
        'komentar'
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id', 'warga_id');
    }

    public function pemakaianAir()
    {
        return $this->belongsTo(Pemakaian_Air::class, 'pemakaianAir_id', 'pemakaianAir_id');
    }

    public function validasi()
    {
        return $this->hasOne(Validasi_Pembayaran::class, 'pembayaran_id');
    }

}
