<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    use HasFactory;

    protected $table = 'standards';
    protected $primaryKey = 'standard_id';
    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'standard_name',
        'standard_description',
        'is_transversal',
        'help'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

}
