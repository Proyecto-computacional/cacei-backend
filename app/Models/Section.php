<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


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
        'indice',
        'is_standard'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function standards() : HasMany
    {
        return $this->hasMany(Standard::class, 'section_id', 'section_id');
    }
}
