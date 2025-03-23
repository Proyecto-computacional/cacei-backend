<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    use HasApiTokens, HasFactory;
    protected $table = 'evidences';
    public $timestamps = false;
    protected $primaryKey = 'evidence_id';
    protected $foreignKey = 'standard_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'evidence_id',
        'standard_id',
        'user_rpe',
        'group_id',
        'process_id',
        'due_date',
    ];
}
