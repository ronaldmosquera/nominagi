<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addendum extends Model
{
    protected $table= "addendum";

    protected $primaryKey = "id_addendum";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_contraracion',
        'cuerpo_addendum',
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
