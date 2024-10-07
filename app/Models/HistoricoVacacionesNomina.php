<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoVacacionesNomina extends Model
{
    protected $table= "historico_vacaciones_nomina";

    protected $primaryKey = "id_historico_vacaciones_nomina";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'fecha_nomina',
        'cantidad',
        'estado'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
