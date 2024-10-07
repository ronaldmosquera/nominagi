<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyIdentification extends Model
{
   // protected  $connection = 'pgsql';

    protected $table= "party_identification";

    protected $primaryKey = "party_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_id',
        'party_identification_type_id',
        'id_value'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
