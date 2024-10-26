<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $primaryKey = 'pembayaran_id';

    protected $fillable = [
        'warga_id',
        'pemakaianAir_id',
        'buktiBayar',
        'waktuBayar',
        'tunggakan',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id', 'warga_id');
    }

    public function pemakaianAir()
    {
        return $this->belongsTo(Pemakaian_Air::class, 'pemakaianAir_id', 'pemakaianAir_id');
    }
}
