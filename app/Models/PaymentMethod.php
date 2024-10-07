<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table= "payment_method";

    protected $primaryKey = "payment_method_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'payment_method_type_id',
        'party_id',
        'description',
        'from_date',
        'thru_date',
        'created_stamp',
        'created_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
