<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SequenceValueItem extends Model
{
    //protected  $connection = 'pgsql';

    protected $table= "sequence_value_item";

    protected $primaryKey = "seq_name";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'seq_name',
        'seq_id'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
