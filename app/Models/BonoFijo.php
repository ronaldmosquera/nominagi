<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonoFijo extends Model
{
    protected $table= "bono_fijo";

    protected $primaryKey = "id_bono_fijo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_contratacion',
        'nombre',
        'monto',
        'apt_personal',
        'fecha_asignacion'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
