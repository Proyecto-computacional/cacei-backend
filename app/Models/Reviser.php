<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

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
}
