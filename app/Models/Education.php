<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $table = 'educations';
    protected $primaryKey = 'education_id';
    public $timestamps = false;

    protected $fillable = [
        'cv_id',
        'institution',
        'degree_obtained',
        'obtained_year',
        'professional_license',
        'degree_name',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
