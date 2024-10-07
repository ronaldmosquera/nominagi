<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlcanceNomina extends Model
{
    protected $table= "alcance_nomina";

    protected $primaryKey = "id_alcance_nomina";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_nomina',
        'sueldo',
        'hora_extra',
        'comision',
        'bono',
        'dcmo_3ro',
        'dcmo_4to',
        'fondo_reserva',
        'comentario',
        'user_login_id',
        'retencion_renta',
        'retencion_iva',
        'total',
        'iva',
        'invoice_id',
        'aporte_personal',
        'aporte_patronal',
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
