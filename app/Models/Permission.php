<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';
    protected $primaryKey = 'permission_id';
    public $timestamps = false;

    protected $fillable = ['permission_name'];

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id');
    }
}
