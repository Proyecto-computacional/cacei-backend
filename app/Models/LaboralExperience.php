<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboralExperience extends Model
{
    use HasFactory;
    
    protected $table = 'laboral_experiences';
    protected $primaryKey = 'laboral_experience_id';
    public $timestamps = false;

    protected $fillable = [
        'cv_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
