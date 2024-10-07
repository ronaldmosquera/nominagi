<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoginTnt extends Model
{
    protected $table= "user_login";

    protected $primaryKey = "user_login_id";
    public $incrementing = false;
    public $timestamps = false;
    protected $connection =  'tnt_ec';

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
}
