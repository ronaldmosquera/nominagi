<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table= "cargos";

    protected $primaryKey = "id_cargo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'salario_minimo_sectorial',
        'cargo_confianza'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
