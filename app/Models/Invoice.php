<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table= "invoice";

    protected $primaryKey = "invoice_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'invoice_type_id',
        'party_id_from',
        'party_id',
        'status_id',
        'invoice_date',
        'due_date',
        'description',
        'currency_uom_id',
        'last_updated_stamp',
        'product_store_id',
        'codigo_establecimiento',
        'sub_total_imp1',
        'sub_total_imp2',
        'total_iva',
        'codigo_punto_emision',
        'last_updated_tx_stamp',
        'created_stamp',
        'created_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
