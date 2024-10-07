<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlAccountType extends Model
{
    protected $table= "gl_account_type";

    protected $primaryKey = "gl_account_type_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'parent_type_id',
        'has_table',
        'description'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
