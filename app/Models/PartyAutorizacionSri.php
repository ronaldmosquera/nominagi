<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyAutorizacionSri extends Model
{
    protected $table= "party_autorizacion_sri";

    protected $primaryKey = "party_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tipo_documento',
        'secuencial_final',
        'secuencial_actual',
        'cod_estab',
        'cod_pto_emision',
        'estado'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
