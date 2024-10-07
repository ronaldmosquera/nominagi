<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $table= "user_login";

    protected $primaryKey = "user_login_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_login_id',
        'party_id',
        'enabled',
        'current_password',
        'last_updated_stamp',
        'last_updated_tx_stamp',
        'created_stamp',
        'created_tx_stamp'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }


}
