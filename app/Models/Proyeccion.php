<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyeccion extends Model
{
    protected $table= "proyeccion";
    protected $primaryKey = "id_proyeccion";
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'anno',
        'id_proyeccion'
    ];
    public function __construct() {
        $this->connection = getConnection(1);
    }

    public function empleadoProyeccion(){
      return $this->hasMany('App\Models\EmpleadoProyeccion','id_proyeccion');
    }
}
