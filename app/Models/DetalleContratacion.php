<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleContratacion extends Model
{
    protected $table= "detalles_contrataciones";

    protected $primaryKey = "id_detalle_contrataciones";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_contrataciones',
        'id_cargo',
        'nombre_archivo_contrato',
        'fecha_expedicion_contrato',
        'fecha_expiracion_contrato',
        'hora_entrada',
        'hora_salida',
        'horas_jornada_laboral',
        'salario',
        'decimo_tercero',
        'decimo_cuarto',
        'fondo_reserva',
        'duracion',
        'cantidad_letras',
        'vacaciones',
        'nombre_archivo',
        'iva',
        'retencion_iva',
        'retencion_renta',
        'tipo_documento',
        'id_ciudad',
        'funciones',
        'tipo_retencion_iva',
        'tipo_retencion_renta'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

    public function cargo(){
        return $this->belongsTo('App\Models\Cargo','id_cargo');
    }
}
