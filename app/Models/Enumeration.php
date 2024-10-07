<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enumeration extends Model
{
    protected $table= "enumeration";

    protected $primaryKey = "enum_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'enum_type_id',
        'enum_code',
        'description'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
