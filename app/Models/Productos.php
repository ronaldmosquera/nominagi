<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $table= "productos";

    protected $primaryKey = "id_productos";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre','costo'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
