<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicProduct extends Model
{
    use HasFactory;

    protected $table = 'academic_products';
    protected $primaryKey = 'academic_product_id';
    public $timestamps = false;

    protected $fillable = [
        'cv_id',
        'academic_product_number',
        'description',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
