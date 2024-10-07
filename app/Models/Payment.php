<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table= "payment";

    protected $primaryKey = "payment_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'payment_type_id',
        'payment_method_type_id',
        'amount',
        'payment_ref_num',
        'comments',
        'effective_date',
        'status_id',
        'party_id_to',
        'currency_uom_id',
        'party_id_from',
        'fin_account_id',
        'created_stamp',
        'created_tx_stamp',
        'last_updated_stamp',
        'last_updated_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
