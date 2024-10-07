<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Geo extends Model
{
    //protected $connection = 'pgsql';

    protected $table= "geo";

    protected $primaryKey = "geo_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'geo_type_id',
        'geo_name',
        'geo_code'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
