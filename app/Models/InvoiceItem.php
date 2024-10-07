<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table= "invoice_item";

    protected $primaryKey = "invoice_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'invoice_item_seq_id',
        'invoice_item_type_id',
        'invoice_number',
        'parent_invoice_item_seq_id',
        'taxable_flag',
        'quantity',
        'amount',
        'description',
        'tax_auth_party_id',
        'tax_auth_geo_id',
        'tax_authority_rate_seq_id',
        'last_updated_stamp',
        'last_updated_tx_stamp',
        'created_stamp',
        'created_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
