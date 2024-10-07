<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStore extends Model
{

    protected $table= "product_store";

    protected $primaryKey = "product_store_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'type_store'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
