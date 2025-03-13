<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'cv_id';

    protected $table = 'cvs';
    protected $fillable = [
        'cv_id', 
        'professor_number', 
        'update_date', 
        'professor_name', 
        'age', 
        'birth_date', 
        'actual_position', 
        'duration'
    ];
}
