<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InoviceItemTypeGlAccount extends Model
{
    protected $table= "invoice_item_type_gl_account";

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'invoice_item_type_id',
        'organization_party_id',
        'gl_account_id',
        'last_updated_stamp',
        'last_updated_tx_stamp',
        'created_stamp',
        'created_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
