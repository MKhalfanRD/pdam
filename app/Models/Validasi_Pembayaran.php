<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validasi_Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'validasi_pembayaran';

    protected $primaryKey = 'validasi_id';
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'pembayaran_id',
        'statusValidasi',
        'keterangan',
        'waktuValidasi',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'admin_id');
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id', 'pembayaran_id');
    }
}
