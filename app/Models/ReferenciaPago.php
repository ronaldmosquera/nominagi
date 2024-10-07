<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenciaPago extends Model
{
    protected $table= "referencia_pago";

    //protected $primaryKey = "id_productos";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'id_registro',
        'referencia',
        'aplicado',
        'payment_id',
        'fecha'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
