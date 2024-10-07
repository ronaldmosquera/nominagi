<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeductionType extends Model
{
    protected $table= "deduction_type";

    protected $primaryKey = "deduction_type_id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'porcentaje_impuesto',
        'codigo_impuesto',
        'gl_account_id',
        'tipo_impuesto',
        'codigo_retencion'
    ];

    public function __construct() {
        $this->connection = getConnection(0);
    }
}
