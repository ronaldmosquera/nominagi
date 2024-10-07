<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyAcctgPreference extends Model
{
    protected $table= "party_acctg_preference";

    protected $primaryKey = "party_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'last_invoice_number'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
