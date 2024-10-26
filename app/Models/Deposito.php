<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    use HasFactory;

    protected $table = 'deposito';

    protected $primaryKey = 'deposito_id';

    protected $fillable = [
        'warga_id',
        'jumlah',
    ];

    public function warga()
    {
        return $this->hasOne(Warga::class, 'warga_id', 'warga_id');
    }
}
