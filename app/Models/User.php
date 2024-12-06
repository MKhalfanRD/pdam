<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;


    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'password',
        'email',
        'role',
    ];

    protected $casts = [
        'role' => 'string',
    ];

    public $timestamps = false;

    public function warga()
    {
        return $this->hasOne(Warga::class, 'user_id', 'user_id');
    }
}
