<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtrosDescuentos extends Model
{
    protected $table= "descuentos";

    protected $primaryKey = "id_descuento";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'fecha_descuento',
        'cantidad',
        'descripcion',
        'descontado',
        'nombre',
        'persona',
        'invoice_item_type_id'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
