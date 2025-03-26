<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $table = 'sections';
    public $timestamps = false;
    protected $primaryKey = 'section_id';
    protected $foreignKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'section_id',
        'category_id',
        'section_name',
        'section_description',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
