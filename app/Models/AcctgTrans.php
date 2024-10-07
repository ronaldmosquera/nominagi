<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcctgTrans extends Model
{
    protected $table= "acctg_trans";

    protected $primaryKey = "acctg_trans_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'acctg_trans_type_id',
        'transaction_date',
        'is_posted',
        'party_id',
        'posted_date',
        'description',
        'gl_fiscal_type_id',
        'created_by_user_login',
        'last_modified_by_user_login',
        'payment_id',
        'invoice_id',
        'role_type_id',
        'last_updated_stamp',
        'last_updated_tx_stamp',
        'created_stamp',
        'created_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
