<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'evidence_id',
        'standard_id',
        'user_rpe',
        'group_id',
        'process_id',
        'due_date'
    ];

    protected $table = 'evidence';
    public $timestamps = false;
    protected $primaryKey = 'evidence_id';

}
