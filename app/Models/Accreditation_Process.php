<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Accreditation_Process extends Model
{
    use HasFactory;
    protected $fillable = [
        'process_id',
        'career_id',
        'frame_id',
        'process_name',
        'start_date',
        'end_date',
        'due_date',
        'finished',
        'deleted'
    ];
    public $timestamps = false;
    protected $primaryKey = 'process_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'accreditation_processes';

    public function career()
    {
        return $this->belongsTo(Career::class, 'career_id', 'career_id');
    }

    public function frame()
    {
        return $this->belongsTo(FrameOfReference::class, 'frame_id', 'frame_id');
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class, 'process_id', 'process_id');
    }

    public function getSemester(){
        $dateProcess = new \DateTime($this->end_date);
        $semester = '';
        //calcular el semestre del proceso
        if($dateProcess->format('n') <= 8 && $dateProcess->format('n') >= 1){
            $semester = ($dateProcess->format('Y') - 1) . "-" . $dateProcess->format('Y') . "/II";
        }else{
            $semester = $dateProcess->format('Y') . "-" . ($dateProcess->format('Y') + 1) . "/I";
        }

        return $semester;
    }
}