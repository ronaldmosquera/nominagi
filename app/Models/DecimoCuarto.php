<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DecimoCuarto extends Model
{
    protected $table= "decimo_cuarto";

    protected $primaryKey = "id_decimo_cuarto";
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
