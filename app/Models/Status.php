<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'status_id'; // Clave primaria
    public $timestamps = false; // Deshabilita timestamps automáticos
    public $incrementing = true; // Habilita autoincremento en el ID

    protected $fillable = [
        'status_id',
        'status_description',
        'user_rpe',
        'evidence_id',
        'status_date',
        'feedback',
    ];

    // Relación con el Usuario (Cada status pertenece a un usuario)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_rpe', 'user_rpe');
    }
}
