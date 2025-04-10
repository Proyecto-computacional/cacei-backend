<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class FrameOfReference extends Model
{
    use HasApiTokens, HasFactory;
    protected $table = 'frames_of_reference';
    public $timestamps = false;
    protected $primaryKey = 'frame_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'frame_id',
        'frame_name'
    ];
}
