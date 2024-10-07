<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FechaFeriado extends Model
{
    protected $table= "fecha_feriado";

    protected $primaryKey = "id_fecha_feriado";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_anno_mes_feriado',
        'id_fecha_feriado'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
