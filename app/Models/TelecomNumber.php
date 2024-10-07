<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelecomNumber extends Model
{
   // protected  $connection = 'pgsql';

    protected $table= "telecom_number";

    protected $primaryKey = "contact_mech_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'contact_mech_id',
        'country_code',
        'contact_number'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
