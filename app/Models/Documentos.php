<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentos extends Model
{
    protected $table= "documentos";

    protected $primaryKey = "id_documentos";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'cuerpo_documento',
        'estado',
        'relacion_dependencia',
        'file',
        'tipo_documento'
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
