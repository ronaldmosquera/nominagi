<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIdentificacionGrupo extends Model
{
    //protected  $connection = 'pgsql';

    protected $table= "party_identification_type";

    protected $primaryKey = "party_identification_type_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_identification_type_id',
        'description'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
