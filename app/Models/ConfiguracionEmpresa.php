<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionEmpresa extends Model
{

    protected $table= "configuracion_empresa";

    protected $primaryKey = "id_configuracion_empresa";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre_empresa',
        'ruc',
        'telefono',
        'representante',
        'identificacion_empresa',
        'correo_representante',
        'correo_empresa',
        'imagen_empresa',
        'descripcion_empresa',
        'direccion_empresa'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
