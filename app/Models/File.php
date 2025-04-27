<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasApiTokens, HasFactory;
    protected $table = 'files';
    public $timestamps = false;
    protected $primaryKey = 'file_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'file_id',
        'file_url',
        'upload_date',
        'evidence_id',
        'justification',
        'file_name'
    ];

    public function evidence()
    {
        return $this->belongsTo(Evidence::class, 'evidence_id');
    }
}