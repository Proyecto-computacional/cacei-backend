<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    use HasFactory;
    protected $fillable = [
        'group_id'
    ];
    public $timestamps = false;
    protected $primaryKey = 'group_id';
}
