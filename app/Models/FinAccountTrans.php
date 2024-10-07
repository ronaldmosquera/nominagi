<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinAccountTrans extends Model
{
    protected $table= "fin_account_trans";

    protected $primaryKey = "fin_account_trans_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'fin_account_trans_type_id',
        'fin_account_id',
        'party_id',
        'transaction_date',
        'entry_date',
        'amount',
        'performed_by_party_id',
        'comments',
        'status_id',
        'created_stamp',
        'created_tx_stamp',
        'last_updated_stamp',
        'last_updated_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
