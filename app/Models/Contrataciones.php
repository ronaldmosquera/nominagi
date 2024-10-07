<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrataciones extends Model
{
    protected $table= "contrataciones";

    protected $primaryKey = "id_contrataciones";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_contrataciones',
        'id_empleado',
        'id_tipo_contrato',
        'estado',
        'id_tipo_contrato_descripcion'

    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

    public function tipo_contratacion(){
        return $this->belongsTo('App\Models\TipoContratos','id_tipo_contrato');
    }

    public function detalle_contratacion(){
        return $this->belongsTo('App\Models\DetalleContratacion','id_contrataciones');
    }

    public function identificacion(){
        return $this->belongsTo('App\Modelos\PartyIdentification','id_empleado');
    }



}
