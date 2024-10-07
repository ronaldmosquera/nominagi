<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItemType extends Model
{
    protected $table= "invoice_item_type";

    protected $primaryKey = "invoice_item_type_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'parent_type_id',
        'description'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
