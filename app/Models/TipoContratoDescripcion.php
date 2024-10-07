<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoContratoDescripcion extends Model
{

    protected $table= "tipo_contrato_descripcion";

    protected $primaryKey = "id_tipo_contrato_descripcion";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'descripcion_tipo_contrato'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
