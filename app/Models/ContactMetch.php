<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMetch extends Model
{
    //protected  $connection = 'pgsql';

    protected $table= "contact_mech";

    protected $primaryKey = "contact_mech_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'contact_mech_id','contact_mech_type_id','info_string'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
