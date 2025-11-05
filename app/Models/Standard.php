<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    use HasFactory;
    protected $table = 'standards';
    public $timestamps = false;
    protected $primaryKey = 'standard_id';
    protected $foreignKey = 'section_id';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'standard_id',
        'section_id',
        'standard_name',
        'standard_description',
        'is_transversal',
        'help',
        'indice'
    ];
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function evidences(){
        return $this->hasMany(Evidence::class, 'standard_id');
    }
}
