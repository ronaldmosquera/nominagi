<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisoSeccionMenu extends Model
{
    protected $table= "permiso_seccion_menu";

    protected $primaryKey = "id_permiso_seccion_menu";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_seccion_menu',
        'fecha_registro',
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

    public function detalle_permiso_seccion_menu(){

        return $this->hasMany('App\Models\DetallePermisoSeccionMenu','id_permiso_seccion_menu');
    }

    public function rol_permiso_seccion_menu(){

        return $this->hasMany('App\Models\RolPermisoSeccionMenu','id_permiso_seccion_menu');
    }
}
