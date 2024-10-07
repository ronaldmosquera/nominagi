<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NominasPasadas extends Model
{
    protected $table= "nominas_pasadas";
    protected $primaryKey = "id_nominas_pasadas";
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id_nomina',
        'fecha_nomina',
        'nombre',
        'identificacion',
        'cargo',
        'sueldo',
        'he_50',
        'he_100',
        'comisiones',
        'bonos',
        'iva',
        'decimo_3ero',
        'decimo_4to',
        'fondo_reserva',
        'apt_patronal',
        'anticipos',
        'consumos',
        'prestamos',
        'descuentos',
        'ret_iva',
        'ret_renta',
        'total',
        'fecha_registro',
        'apt_personal'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
