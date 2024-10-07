<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table= "person";

    protected $primaryKey = "party_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_id',
        'first_name',
        'last_name',
        'gender',
        'nacionalidad'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }

    public function identification(){
        return $this->belongsTo('App\Models\PartyIdentification','party_id');
    }
}
