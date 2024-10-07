<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacaciones extends Model
{
    protected $table= "vacaciones";

    protected $primaryKey = "id_vacaciones";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'cantidad_dias',
        'dias_entre_semana',
        'dias_fines_semana',
        'comentarios',
        'inicio_generado',
        'fin_generado'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
