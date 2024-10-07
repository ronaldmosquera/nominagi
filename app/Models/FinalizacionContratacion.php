<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalizacionContratacion extends Model
{
    protected $table= "finalizacion_contrataciones";

    protected $primaryKey = "id_finalizacion_contrataciones";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_finalizacion_contrataciones',
        'id_tipo_finalizacion',
        'fecha_finalizacion'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
