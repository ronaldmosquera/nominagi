<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MesEmpleadoProyeccion extends Model
{
    protected $table= "mes_empleado_proyeccion";
    protected $primaryKey = "id_mes_empleado_proyeccion";
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id_empleado_proyeccion',
        'mes'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }


    public function itemMesEmpleadoProyeccion(){
        return $this->hasMany('App\Models\ItemMesEmpleadoProyeccion','id_mes_empleado_proyeccion');
    }
}
