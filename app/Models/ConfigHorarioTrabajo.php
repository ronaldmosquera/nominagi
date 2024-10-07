<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigHorarioTrabajo extends Model
{
    protected $table= "configuracion_horarios_trabajo";

    protected $primaryKey = "id_config_horarios_trabajo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'desde','hasta','clase'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
