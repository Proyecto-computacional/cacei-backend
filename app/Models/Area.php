<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $fillable = [
        'area_id',
        'area_name',
        'user_rpe',
    ];
    public $timestamps = false;
    protected $primaryKey = 'area_id';

    protected $table = 'areas';

    public function manager()
    {
        return $this->belongsTo(User::class, 'admin_rpe', 'user_rpe');
    }

}
