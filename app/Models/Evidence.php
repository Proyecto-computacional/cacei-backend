<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    use HasFactory;

    protected $table = 'evidences';
    protected $primaryKey = 'evidence_id';
    public $timestamps = false;

    protected $fillable = [
        'standard_id',
        'user_rpe',
        'group_id',
        'process_id',
        'due_date'
    ];


    public function standard()
    {
        return $this->belongsTo(Standard::class, 'standard_id');
    }

    public function process()
    {
        return $this->belongsTo(Accreditation_Process::class, 'process_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_rpe');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'evidence_id');
    }
}
