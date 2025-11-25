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
        'user_area',
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
        return $this->hasMany(Notification::class, 'user_rpe', 'user_rpe');
    }

    public function revisers()
    {
        return $this->hasMany(Reviser::class, 'user_rpe', 'user_rpe');
    }

    public function reviserNotifications()
    {
        return $this->hasMany(Notification::class, 'reviser_id', 'user_rpe');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'user_area', 'area_id');
    }

    public function cvs()
    {
        return $this->hasOne(Cv::class, 'cv_id', 'cv_id');
    }

    public function hasPermission($permisoNombre)
    {
        // Buscar el rol por el nombre almacenado en el usuario
        $role = Role::where('role_name', $this->user_role)->first();

        if (!$role) {
            return false;
        }

        // Buscar si ese rol tiene el permiso habilitado
        return $role->permissions()
                    ->where('permission_name', $permisoNombre)
                    ->wherePivot('is_enabled', true)
                    ->exists();
    }
}
