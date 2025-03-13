<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryUpdate extends Model
{
    use HasFactory;

    protected $table = 'disciplinary_updates';
    protected $primaryKey = 'disciplinary_update_id';
    public $timestamps = false;

    protected $fillable = [
        'cv_id',
        'title_certification',
        'year_certification',
        'institution_country',
        'hours',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
