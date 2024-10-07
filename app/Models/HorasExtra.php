<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorasExtra extends Model
{
    protected $table= "horas_extras";

    protected $primaryKey = "id_horas_extras";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'fecha_solicitud',
        'desde',
        'hasta',
        'cantidad_horas',
        'comentarios',
        'estado',
        'invoice_item_type_id'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}

