<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EftAccount extends Model
{

    protected $table= "eft_account";

    protected $primaryKey = "payment_method_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'account_type',
        'codigo_banco',
        'account_number',
        'name_on_account',
        'created_stamp',
        'created_tx_stamp',
        'last_updated_stamp',
        'last_updated_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
