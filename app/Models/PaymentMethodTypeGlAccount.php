<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodTypeGlAccount extends Model
{
    protected $table= "payment_method_type_gl_account";

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'payment_method_type_id',
        'organization_party_id',
        'gl_account_id'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
