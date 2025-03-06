<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = [
        'professor_number',
        'update_date',
        'professor_name',
        'age',
        'birth_date',
        'actual_position',
        'duration'
    ];

    public function Academic_management()
    {
        return $this->hasMany(Academic_management::class, '', 'id');
    }


}
