<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    use HasFactory;

    protected $fillable = [
        'standard_id',
        'standard_name',
        'section_id'
    ];
    protected $table = 'standards';
    public $timestamps = false;
    protected $primaryKey = 'standard_id';

}
