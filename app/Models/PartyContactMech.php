<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyContactMech extends Model
{
    //protected  $connection = 'pgsql';

    protected $table= "party_contact_mech";

    protected $primaryKey = "party_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_id',
        'contact_mech_id',
        'from_date',
        'role_type_id'
    ];
    public function __construct() {
        $this->connection = getConnection(0);
    }

}
