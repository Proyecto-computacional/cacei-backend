<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accreditation_Process;
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
        'due_date'
    ];



    public function files()
    {
        return $this->hasMany(File::class, 'evidence_id');
    }
    // Relación con Standard (Cada evidencia pertenece a un estándar)
    public function standard()
    {
        return $this->belongsTo(Standard::class, 'standard_id', 'standard_id');
    }

    // Relación con el Usuario (Cada evidencia pertenece a un usuario)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_rpe', 'user_rpe');
    }

    // Relación con Grupo (Cada evidencia pertenece a un grupo)
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    // Relación con Proceso (Cada evidencia pertenece a un proceso)
    public function process()
    {
        return $this->belongsTo(Accreditation_Process::class, 'process_id', 'process_id');
    }

    // Relación con Notificaciones (Una evidencia puede tener muchas notificaciones)
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'evidence_id', 'evidence_id');
    }

    public function assignEvidence()
    {
        return $this->hasMany(Reviser::class, 'evidence_id');
    }
}
