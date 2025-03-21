<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accreditation_Process extends Model
{
    use HasFactory;
    protected $fillable = [
        'frame_id',
        'process_id',
        'career_id',
    ];

    // Define the inverse relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}