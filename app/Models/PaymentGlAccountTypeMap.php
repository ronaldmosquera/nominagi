<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGlAccountTypeMap extends Model
{
    protected $table= "payment_gl_account_type_map";

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'payment_type_id',
        'organization_party_id',
        'gl_account_type_id'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
