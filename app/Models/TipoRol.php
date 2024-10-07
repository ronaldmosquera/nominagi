<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRol extends Model
{
    //protected  $connection = 'pgsql';

    protected $table= "role_type";

    protected $primaryKey = "role_type_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'role_type_id',
        'description'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
