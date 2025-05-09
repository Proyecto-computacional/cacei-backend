<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use function PHPUnit\Framework\returnArgument;

class Reviser extends Model
{
    use HasApiTokens, HasFactory;
    protected $table = 'revisers';
    public $timestamps = false;
    protected $primaryKey = 'reviser_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'reviser_id',
        'user_rpe',
        'evidence_id'
    ];

    public function sentNotification()
    {
        return $this->hasMany(Notification::class, 'reviser_id');
    }
    public function evidence()
    {
        return $this->belongsTo(Evidence::class, 'evidence_id', 'evidence_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_rpe', 'user_rpe');
    }
}
