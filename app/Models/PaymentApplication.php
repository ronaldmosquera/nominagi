<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentApplication extends Model
{
    protected $table= "payment_application";

    protected $primaryKey = "payment_application_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'payment_application_id',
        'payment_id',
        'invoice_id',
        'amount_applied',
        'created_stamp',
        'created_tx_stamp',
        'last_updated_stamp',
        'last_updated_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
