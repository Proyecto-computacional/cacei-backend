<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Academic_management extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_id',
        'job_position',
        'institution',
        'start_date',
        'end_date'
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
