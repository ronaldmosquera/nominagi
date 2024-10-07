<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nomina;
use Carbon\Carbon;
use DB;

class NominaEmpleadoController extends Controller
{
    public function estadisticaNominaEmpleado(Request $request)
    {
        $dataRoles = Nomina::where('id_empleado',$request->id_empleado);

        isset($request->fecha)
            ? $dataRoles->whereBetween('fecha_nomina', [$request->fecha.'-01-01', $request->fecha.'-12-31'])
            : $dataRoles->whereBetween('fecha_nomina', [Carbon::now()->format('Y-01-01'), Carbon::now()->format('Y-12-31')]);

        $groupRoles = [];
        foreach ($dataRoles->get() as $dR) {
            $groupRoles[Carbon::parse($dR["fecha_nomina"])->format('Y-m-d')][] = $dR;
        }

        $data = [];
        foreach ($groupRoles as $groupRole){
            $total = 0;
            foreach ($groupRole as $item) {
                $total += $item->total;
                $mes = intval(Carbon::parse($item->fecha_nomina)->format('m'));
            }
            $data[$mes-1] = [$total];
        }

        $data2= [];
        for($a=0;$a<12;$a++){
            if(array_key_exists($a, $data))
                $data2[] = $data[$a][0];
            else
                $data2[] = 0;
        }

        return [
            $data2,
            $request->fecha,
            Nomina::select(DB::raw('EXTRACT(YEAR FROM fecha_nomina) as anno'))->distinct()->get()
        ];
    }
}
