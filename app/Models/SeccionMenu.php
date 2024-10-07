<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeccionMenu extends Model
{
    protected $table= "seccion_menu";

    protected $primaryKey = "id_seccion_menu";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_registro',
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }


    public function permiso_seccion_menu(){
        return $this->hasMany('App\Models\PermisoSeccionMenu','id_seccion_menu');
    }
}
