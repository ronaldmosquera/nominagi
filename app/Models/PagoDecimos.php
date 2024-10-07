<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoDecimos extends Model
{
    protected $table= "pago_decimos";
    protected $primaryKey = "id_pago_decimos";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'tipo',
        'monto',
        'fecha_registro'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
