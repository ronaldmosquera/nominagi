<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FondoReserva extends Model
{
    protected $table= "fondo_reserva";

    protected $primaryKey = "id_fondo_reserva";
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
