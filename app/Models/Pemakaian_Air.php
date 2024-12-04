<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemakaian_Air extends Model
{
    use HasFactory;

    protected $table = 'pemakaian_air';
    protected $primaryKey = 'pemakaianAir_id';

    protected $fillable = [
        'warga_id',
        'operator_id',
        'bulan',
        'pemakaianBaru',
        'pemakaianLama',
        'foto',
        'tagihanAir',
        'kubikasi',
    ];

    public $timestamps = false;

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id', 'warga_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id', 'user_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'pemakaianAir_id', 'pemakaianAir_id');
    }
}
