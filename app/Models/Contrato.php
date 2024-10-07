<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table= "contrato";

    protected $primaryKey = "id_contrato";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_contrato',
        'cuerpo_contrato',
        'estado'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
