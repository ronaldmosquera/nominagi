<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForeginContrataciones extends Model
{

    protected $table= "contrataciones";

    protected $primaryKey = "id_contrataciones";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_id',
        'id_tipo_contrato',
        'estado',
        'id_tipo_contrato_descripcion'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }

    public function person(){
        return $this->belongsTo('App\Models\Person','party_id');
    }
}
