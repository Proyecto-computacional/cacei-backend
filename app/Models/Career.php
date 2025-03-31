<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory;
    protected $fillable = [
        'career_id',
        'area_id',
        'career_name',
        'user_rpe',
    ];
    public $timestamps = false;
    protected $primaryKey = 'career_id';

    protected $table = 'careers';

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
    }

    public function coordinador()
    {
        return $this->belongsTo(User::class, 'admin_rpe', 'user_rpe');
    }

}


