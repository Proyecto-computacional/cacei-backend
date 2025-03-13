<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicManagement extends Model
{
    use HasFactory;

    protected $table = 'academic_managements';
    protected $primaryKey = 'academic_management_id';
    public $timestamps = false;

    protected $fillable = [
        'cv_id',
        'job_position',
        'institution',
        'start_date',
        'end_date',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
