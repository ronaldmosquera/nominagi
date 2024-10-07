<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anticipos extends Model
{
    protected $table= "anticipos";

    protected $primaryKey = "id_anticipo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'cantidad',
        'fecha_entrega',
        'fecha_descuento',
        'estado',
        'comentario',
        'descontado',
        'invoice_item_type_id',
        'invoice_id'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
