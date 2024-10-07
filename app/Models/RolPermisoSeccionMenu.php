<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolPermisoSeccionMenu extends Model
{
    protected $table= "rol_permiso_seccion_menu";

    protected $primaryKey = "id_rol_permiso_seccion_menu";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_permiso_seccion_menu',
        'role_type_id',
        'fecha_registro'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

    public function permiso_seccion_menu(){
        return $this->belongsTo('App\Models\PermisoSeccionMenu','id_permiso_seccion_menu');
    }
}
