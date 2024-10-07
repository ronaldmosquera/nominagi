<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthClient extends Model
{
    protected $table= "oauth_clients";
    public $incrementing = false;
    public $timestamps = false;
    protected $connection =  'tnt_ec';

    protected $fillable = [
        'client_id',
        'client_secret',
        'redirect_uri',
        'grant_types',
        'scope',
        'user_id'
    ];

}
