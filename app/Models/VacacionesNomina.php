<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacacionesNomina extends Model
{
    protected $table= "vacaciones_nomina";

    protected $primaryKey = "id_vacaciones_nomina";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_nomina',
        'cantidad',
        'estado'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
