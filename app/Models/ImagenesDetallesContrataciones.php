<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenesDetallesContrataciones extends Model
{
    protected $table= "imagenes_detalles_contrataciones";

    protected $primaryKey = "id_imagenes_detalles_contrataciones";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_detalles_contrataciones',
        'imagen'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
