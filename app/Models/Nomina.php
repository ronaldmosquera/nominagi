<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Nomina extends Model
{
    protected $table= "nomina";
    protected $primaryKey = "id_nomina";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_nomina',
        'id_empleado',
        'fecha_nomina',
        'total',
        'id_contrataciones',
        'liquidacion',
        'id_factura',
        'persona',
        'identificacion'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }

}
