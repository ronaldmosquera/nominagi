<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyRelationShip extends Model
{
    protected $table= "party_relationship";

    protected $primaryKey = "party_id_from";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_id_from',
        'party_id_to',
        'role_type_id_from',
        'role_type_id_to',
        'from_date',
        'party_relationship_type_id'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
