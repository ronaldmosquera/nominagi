<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GruposEmpleados extends Model
{
   // protected  $connection = 'pgsql';

    protected $table= "party_type";

    protected $primaryKey = "user_login_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_type_id',
        'description'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
