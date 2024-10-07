<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlAccountTypeDefault extends Model
{
    protected $table= "gl_account_type_default";

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'gl_account_type_id',
        'organization_party_id',
        'gl_account_id'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }

}
