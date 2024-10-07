<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DecimoTercero extends Model
{
    protected $table= "decimo_tercero";

    protected $primaryKey = "id_decimo_tercero";
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
