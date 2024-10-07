<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemMesEmpleadoProyeccion extends Model
{
    protected $table= "item_mes_empleado_proyeccion";
    protected $primaryKey = "id_item_mes_empleado_proyeccion";
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id_mes_empleado_proyeccion',
        'id_item',
        'valor'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
