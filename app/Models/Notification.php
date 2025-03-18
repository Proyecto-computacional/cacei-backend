<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;
    public $incrementing = false;//Define que el Notification_id es autoincremental.
    
    //Lista de campos que puedes llenar automáticamente cuando creas o actualizas una notificación.
    protected $fillable = [
        'notification_id',
        'title',
        'evidence_id',
        'notification_date',
        'user_rpe',
        'reviser_id',
        'description',
        'seen',
        'pinned'
    ];
    //Una relacion inversa belongsTo(User::class) indica que cada notificación pertenece a un solo 
    // usuario y User_rpe es de la tabla notification y  user_rpe  es de la tabla user_t (PK).
    public function user()
    {
        return $this->belongsTo(User::class, 'user_rpe', 'user_rpe');
    }
    public function revisor()
    {
        return $this->belongsTo(Reviser::class, 'reviser_id', 'reviser_id');
    }

}
