<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';
    protected $fillable = [
        'user_rpe',
        'user_mail',
        'user_role',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    /**protected $hidden = [
       'password',
       'remember_token',
       'password' => 'hashed',
   ];
   protected $casts = [
       'email_verified_at' => 'datetime',
   ];*/
}
