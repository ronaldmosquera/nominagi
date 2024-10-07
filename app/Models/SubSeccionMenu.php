<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSeccionMenu extends Model
{
    protected $table= "sub_seccion_menu";

    protected $primaryKey = "id_sub_seccion_menu";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_seccion_menu',
        'nombre',
        'url',
        'fecha_registro'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

    public function ruta_sub_seccion_menu(){
        return $this->hasMany('App\Models\RutaSubSeccionMenu','id_sub_seccion_menu')->orderBy('nombre','asc');
    }


    public function seccion_menu(){
        return $this->belongsTo('App\Models\SeccionMenu','id_seccion_menu');
    }
}
