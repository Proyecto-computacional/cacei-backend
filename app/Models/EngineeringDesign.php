<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineeringDesign extends Model
{
    use HasFactory;

    protected $table = 'engineering_designs';
    protected $primaryKey = 'engineering_design_id';
    public $timestamps = false;

    protected $fillable = [
        'cv_id',
        'institution',
        'period',
        'level_experience',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
