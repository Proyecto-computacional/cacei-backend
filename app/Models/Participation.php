<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    use HasFactory;

    protected $table = 'participations';
    protected $primaryKey = 'participation_id';
    public $timestamps = false;

    protected $fillable = [
        'cv_id',
        'institution',
        'period',
        'level_participation',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
