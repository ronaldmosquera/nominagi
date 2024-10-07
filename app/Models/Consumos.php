<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumos extends Model
{
    protected $table= "consumos";

    protected $primaryKey = "id_consumo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'fecha_descuento',
        'monto_descuento',
        'estado',
        'invoice_id',
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
