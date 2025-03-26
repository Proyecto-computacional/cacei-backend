<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';
    protected $primaryKey = 'section_id';
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'section_name',
        'section_description'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
