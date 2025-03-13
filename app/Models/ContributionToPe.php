<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributionToPe extends Model
{
    use HasFactory;

    protected $table = 'contributions_to_pe';
    protected $primaryKey = 'contribution_id';
    public $timestamps = false;

    protected $fillable = [
        'cv_id',
        'description',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'cv_id');
    }
}
