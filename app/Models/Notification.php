<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';
    protected $primaryKey = 'Notification_id';
    public $timestamps = false;
    public $incrementing = true;//Define que el Notification_id es autoincremental.
   //Lista de campos que puedes llenar automáticamente cuando creas o actualizas una notificación.
    protected $fillable = [
        'Title',
        'Evidence_id',
        'Notification_date',
        'User_rpe',
        'Description',
        'Seen'
    ];
    //Una relacion inversa belongsTo(User::class) indica que cada notificación pertenece a un solo 
    // usuario y User_rpe es de la tabla notification y  user_rpe  es de la tabla user_t (PK).
    public function user()
    {
        return $this->belongsTo(User::class, 'User_rpe', 'user_rpe');
    }
}
