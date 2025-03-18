<?php              

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Evidence extends Model
{
    use HasFactory;

    protected $table = 'evidences'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'evidence_id'; // Clave primaria
    public $timestamps = false; // Deshabilita timestamps automáticos
    public $incrementing = true; // Habilita autoincremento en el ID

    protected $fillable = [
        'standard_id',
        'user_rpe',
        'group_id',
        'process_id',
        'due_date'
    ];

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
        return $this->belongsTo(Process::class, 'process_id', 'process_id');
    }

    // Relación con Notificaciones (Una evidencia puede tener muchas notificaciones)
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'evidence_id', 'evidence_id');
    }
}
