<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionHorario extends Model
{
    protected $table= "asignacion_horarios";

    protected $primaryKey = "id_asignacion_horarios";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado','entrada','salida','fecha','clase'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

}
