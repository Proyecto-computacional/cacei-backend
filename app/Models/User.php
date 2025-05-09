<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';
    public $timestamps = false;
    protected $primaryKey = 'user_rpe';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_rpe',
        'user_mail',
        'user_role',
        'user_name',
        'cv_id'
    ];


    public function processes()
    {
        return $this->hasMany(Accreditation_Process::class);
    }
  
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
  
  
    public function sentNotification()
    {
        return $this->hasMany(Notification::class, 'user_rpe');
    }

    public function revisers()
    {
        return $this->hasMany(Reviser::class, 'user_rpe', 'user_rpe');
    }

    public function cvs()
    {
        return $this->hasOne(Cv::class, 'cv_id', 'cv_id');
    }
}
