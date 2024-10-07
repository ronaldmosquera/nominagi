<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotivoAnulacion extends Model
{

    protected $table= "motivo_anulacion";

    protected $primaryKey = "id_motivo_anulacion";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'desahucio',
        'despido_intempestivo'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

}
