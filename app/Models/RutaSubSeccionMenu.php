<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaSubSeccionMenu extends Model
{
    protected $table= "ruta_sub_seccion_menu";

    protected $primaryKey = "id_ruta_sub_seccion_menu";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_sub_seccion_menu',
        'nombre',
        'url',
        'fecha_registro'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

    public function sub_seccion_menu(){
        return $this->belongsTo('App\Models\SubSeccionMenu','id_sub_seccion_menu');
    }

}
