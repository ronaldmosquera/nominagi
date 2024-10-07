<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoComision extends Model
{
    protected $table= "tipo_comisiones";

    protected $primaryKey = "id_tipo_comision";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estandar',
        'descripcion',
        'estado',
        'calculo_decimo_tercero'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
