<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenesRoles extends Model
{
    protected $table= "imagen_rol_pago";

    protected $primaryKey = "id_imagen_rol";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'fecha_nomina',
        'nombre_imagen',
        'estado',
        'fecha_registro'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

}
