<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherTraining extends Model
{
    use HasFactory;

    protected $table = 'teacher_trainings';
    protected $primaryKey = 'teacher_training_id';
    public $timestamps = false;

    protected $fillable = [
        'cv_id',
        'title_certification',
        'obtained_year',
        'institution_country',
        'hours',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
