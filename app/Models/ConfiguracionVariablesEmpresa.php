<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionVariablesEmpresa extends Model
{
    protected $table= "configuracion_empresa_variables";

    protected $primaryKey = "id_configuracion_empresa_variables";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_configuracion_empresa',
        'hora_extra_entre_semana',
        'hora_extra_fin_semana',
        'sueldo_basico_unificado_vigente',
        'vacaciones_dias_entre_semana',
        'vacaciones_dias_fines_semana',
        'porcentaje_avance',
        'diferir_consumos_meses',
        'iva',
        'aporte_patronal',
        'aporte_personal',
        'fondo_reserva',
        'hora_extra_entre_semana_relacion_dependencia',
        'hora_extra_fin_semana_relacion_dependencia',
        'anno_calculo_fondo_reserva',
        'antiguedad',
        'fecha_hasta',
        'intervalo',
        'tiempo_carga_he',
        'tiempo_aprov_he'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
