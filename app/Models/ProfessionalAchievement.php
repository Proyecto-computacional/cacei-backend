<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalAchievement extends Model
{
    use HasFactory;

    protected $table = 'professional_achievements';
    protected $primaryKey = 'achievement_id';
    public $timestamps = false;

    protected $fillable = [
        'achievement_id',
        'cv_id',
        'description',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
