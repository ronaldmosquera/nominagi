<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{

   // protected  $connection = 'pgsql';

    protected $table= "user_login_sesion";

    protected $primaryKey = "user_login_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_login_id',
        'token',
        'activo',
        'ip'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
