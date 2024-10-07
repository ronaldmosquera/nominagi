<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    //protected  $connection = 'pgsql';

    protected $table= "party";

    protected $primaryKey = "party_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_id','party_type_id'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
