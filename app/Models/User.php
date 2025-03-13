<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $primaryKey = 'user_rpe';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'users';
    protected $fillable = [
        'user_rpe',
        'user_mail',
        'user_role',
    ];

}
