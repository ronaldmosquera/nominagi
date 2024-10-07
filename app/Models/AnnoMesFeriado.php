<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnoMesFeriado extends Model
{
    protected $table= "anno_mes_feriado";

    protected $primaryKey = "id_anno_mes_feriado";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'fecha',
    ];

    public function __construct() {
        $this->connection = getConnection(1);
    }
}
