<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleConsumo extends Model
{
    protected $table= "detalle_consumo";

    protected $primaryKey = "id_detalle_consumo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'cantidad',
        'id_producto',
        'id_consumo'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
