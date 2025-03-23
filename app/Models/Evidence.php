<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    use HasFactory;

    protected $fillable = [
        //no hay nombre
        'evidence_id',
        'standard_id',
        'user_rpe',
        'group_id',
        'process_id',
        'due_date'
    ];

    protected $table = 'evidences';
    public $timestamps = false;
    protected $primaryKey = 'evidence_id';

}
