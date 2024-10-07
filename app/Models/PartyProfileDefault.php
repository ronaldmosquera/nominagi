<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyProfileDefault extends Model
{
    protected $table= "party_profile_default";

    protected $primaryKey = "party_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'party_id',
        'product_store_id',
        'default_pay_meth',
        'ret_iva_id',
        'ret_ir_id',
        'last_updated_stamp',
        'last_updated_tx_stamp',
        'created_stamp',
        'created_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
