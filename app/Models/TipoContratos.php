<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TipoContratos extends Model
{

    protected $table= "tipo_contrato";

    protected $primaryKey = "id_tipo_contrato";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'horas_extras',
        'relacion_dependencia',
        'caducidad',
        'sueldo_sectorial'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
