<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcctgTransEntry extends Model
{
    protected $table= "acctg_trans_entry";

    protected $primaryKey = "acctg_trans_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'acctg_trans_entry_seq_id',
        'gl_account_id',
        'organization_party_id',
        'amount',
        'currency_uom_id',
        'orig_amount',
        'orig_currency_uom_id',
        'debit_credit_flag',
        'reconcile_status_id',
        'last_updated_stamp',
        'last_updated_tx_stamp',
        'created_stamp',
        'created_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
