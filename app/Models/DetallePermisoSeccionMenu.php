<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePermisoSeccionMenu extends Model
{
    protected $table= "detalle_permiso_seccion_menu";

    protected $primaryKey = "id_detalle_permiso_seccion_menu";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_permiso_seccion_menu',
        'id_ruta_sub_seccion_menu',
        'fecha_registro',
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

    public function ruta_sub_seccion_menu(){
        return $this->belongsTo('App\Models\RutaSubSeccionMenu','id_ruta_sub_seccion_menu');
    }
}
