<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $table= "prestamo";

    protected $primaryKey = "id_prestamo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_contratacion',
        'nombre',
        'cuota',
        'abonado',
        'total',
        'pagado',
        'persona',
        'fecha_inicio_descuento',
        'invoice_item_type_id'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
