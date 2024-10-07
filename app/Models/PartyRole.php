<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyRole extends Model
{
    //protected  $connection = 'pgsql';

    protected $table= "party_role";

    protected $primaryKey = "party_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_id',
        'role_type_id'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
