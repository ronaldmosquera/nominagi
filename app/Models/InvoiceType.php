<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceType extends Model
{
    protected $table= "invoice_type";

    protected $primaryKey = "invoice_type_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'description'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }

}
