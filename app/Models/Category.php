<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    public $timestamps = false;
    protected $primaryKey = 'category_id';
    protected $foreignKey = 'frame_id';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'category_id',
        'category_name',
        'frame_id',
        'indice'
    ];

    public function frame()
    {
        return $this->belongsTo(FrameOfReference::class, 'frame_id', 'frame_id');
    }

    public function sections() : HasMany{
        return $this->hasMany(Section::class, 'category_id', 'category_id');
    }
}
