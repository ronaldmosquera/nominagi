<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comisiones extends Model
{

    protected $table= "comisiones";

    protected $primaryKey = "id_comisiones";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'fecha_nomina',
        'cantidad',
        'descripcion',
        'pagada'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
