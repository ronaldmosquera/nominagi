<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoProyeccion extends Model
{
    protected $table= "empleado_proyeccion";
    protected $primaryKey = "id_empleado_proyeccion";
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id_empleado',
        'id_proyeccion',
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

    public function mesEmpleadoProyeccion(){
        return $this->hasMany('App\Models\MesEmpleadoProyeccion','id_empleado_proyeccion');
    }
}
