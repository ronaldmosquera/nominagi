<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iva extends Model
{
    protected $table= "iva";

    protected $primaryKey = "id_iva";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'valor'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
