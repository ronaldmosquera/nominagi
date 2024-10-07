<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostalAddres extends Model
{
   // protected  $connection = 'pgsql';

    protected $table= "postal_address";

    protected $primaryKey = "contact_mech_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'address1',
        'city',
        'country_geo_id',
        'state_province_geo_id'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
