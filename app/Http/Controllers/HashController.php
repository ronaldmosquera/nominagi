<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class HashController extends Controller
{
    public function hash(){
        return hash("sha512",'SessionTemp'.Carbon::now()->toDateString().'InnofarmNomina');
    }
}
