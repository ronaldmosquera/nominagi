<?php

namespace App\Http\Controllers;

use App\Models\DetalleContratacion;
use App\Models\ForeginContrataciones;
use App\Models\ItemMesEmpleadoProyeccion;
use App\Models\MesEmpleadoProyeccion;
use App\Models\EmpleadoProyeccion;
use App\Models\Proyeccion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Contrataciones;
use App\Models\Person;
use Validator;

class ProyeccionNominaController extends Controller
{
    public $data;
    public $msg;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.views.proyeccion_nomina.inicio', [
            'dataEmpleados' => ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
            ->select('p.first_name','p.last_name','p.party_id')->where('contrataciones.estado',1)->get(),
            'aanos' => Proyeccion::select('anno')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request){

        $this->data = $request;
        $archivo = Excel::create('Prueba', function($excel) {

            $dataContrataciones = ForeginContrataciones::where([
                ['id_tipo_contrato_descripcion',2],
                ['estado',1]
            ])->join('person as p', 'contrataciones.party_id','p.party_id')
            ->whereIn('contrataciones.party_id',$this->data->arrEmpleado)->get();

            $excel->sheet('Datos de proyección', function($sheet) use ($dataContrataciones) {

                $sheet->row(1,['ID_EMPLEADO', 'NOMBRE EMPLEADO']);
                $idEmpleado = [];

                foreach ($dataContrataciones as $key => $contratacion){
                    $sheet->row(3+($key+1), [
                        $contratacion->party_id, $contratacion->first_name." ".$contratacion->last_name
                    ]);

                    $idEmpleado[] = $contratacion->party_id;
                    $sheet->cell('A'.(3+$key+1).'', function($cell) { $cell->setAlignment('center'); });
                }

                //$cantMeses = $this->data->fecha_fin_calculo - $this->data->fecha_inicio_calculo;

                /*$meses = [];
                for($x=1;$x<=($cantMeses+1);$x++){
                    $meses[] = ($this->data->fecha_inicio_calculo + $x)-1;
                }
                dd($meses);*/

                //if(in_array(1,$meses,true)){
                //--------- ENERO ----------//
                $sheet->mergeCells('C1:Y1');
                $sheet->cell('C1', function($cell) {
                        $cell->setValue(strtoupper(getMes(1)));
                        $cell->setAlignment('center');
                        $cell->setBackground('#c1c1c1');
                    });

                $sheet->mergeCells('F2:M2');
                $sheet->cell('F2', function($cell) {
                        $cell->setValue('INGRESOS');
                        $cell->setAlignment('center');
                        $cell->setBackground('#FFEB3B');
                        $cell->setFontWeight('bold');
                    });

                $sheet->mergeCells('N2:R2');
                $sheet->cell('N2', function($cell) {
                        $cell->setValue('BENEFICIOS SOCIALES');
                        $cell->setAlignment('center');
                        $cell->setBackground('#2196f3');
                        $cell->setFontWeight('bold');
                    });

                $sheet->mergeCells('S2:Y2');
                $sheet->cell('S2', function($cell) {
                        $cell->setValue('GASTOS');
                        $cell->setAlignment('center');
                        $cell->setBackground('#00a65a');
                        $cell->setFontWeight('bold');
                    });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('C'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('C3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('D3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('E3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('F3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('G3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('H3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('I3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('J3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('K3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('L3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('M3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('N3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('O3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('P3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('Q3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('R3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('S3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('T3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('U3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('V3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('W3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('X3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('Y3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });
                //}

                //--------- FEBRERO ----------//

                $sheet->mergeCells('AA1:AW1');
                $sheet->cell('AA1', function($cell) {
                    $cell->setValue(strtoupper(getMes(2)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('AD2:AK2');
                $sheet->cell('AD2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('AL2:AP2');
                $sheet->cell('AL2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('AQ2:AW2');
                $sheet->cell('AQ2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('AA'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('AA3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('AB3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('AC3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('AD3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('AE3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('AF3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('AG3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('AH3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('AI3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('AJ3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('AK3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('AL3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('AM3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('AN3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('AO3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('AP3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('AQ3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('AR3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('AS3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('AT3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('AU3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('AV3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('AW3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });


                //--------- MARZO ----------//

                $sheet->mergeCells('AY1:BU1');
                $sheet->cell('AY1', function($cell) {
                    $cell->setValue(strtoupper(getMes(3)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('BB2:BI2');
                $sheet->cell('BB2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('BJ2:BN2');
                $sheet->cell('BJ2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('BO2:BU2');
                $sheet->cell('BO2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('AY'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('AY3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('AZ3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('BA3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('BB3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('BC3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('BD3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('BE3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('BF3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('BG3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('BH3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('BI3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('BJ3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('BK3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('BL3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('BM3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('BN3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('BO3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('BP3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('BQ3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('BR3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('BS3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('BT3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('BU3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });


                //--------- ABRIL ----------//

                $sheet->mergeCells('BW1:CS1');
                $sheet->cell('BW1', function($cell) {
                    $cell->setValue(strtoupper(getMes(4)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('BZ2:CG2');
                $sheet->cell('BZ2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('CH2:CL2');
                $sheet->cell('CH2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('CM2:CS2');
                $sheet->cell('CM2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('BW'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('BW3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('BX3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('BY3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('BZ3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('CA3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('CB3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('CC3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('CD3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('CE3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('CF3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('CG3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('CH3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('CI3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('CJ3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('CK3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('CL3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('CM3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('CN3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('CO3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('CP3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('CQ3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('CR3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('CS3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });


                //--------- MAYO ----------//

                $sheet->mergeCells('CU1:DQ1');
                $sheet->cell('CU1', function($cell) {
                    $cell->setValue(strtoupper(getMes(5)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('CX2:DE2');
                $sheet->cell('CX2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('DF2:DJ2');
                $sheet->cell('DF2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('DK2:DQ2');
                $sheet->cell('DK2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('CU'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('CU3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('CV3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('CW3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('CX3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('CY3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('CZ3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('DA3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('DB3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('DC3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('DD3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('DE3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('DF3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('DG3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('DH3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('DI3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('DJ3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('DK3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('DL3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('DM3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('DN3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('DO3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('DP3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('DQ3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });


                //--------- JUNIO ----------//

                $sheet->mergeCells('DS1:EO1');
                $sheet->cell('DS1', function($cell) {
                    $cell->setValue(strtoupper(getMes(6)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('DV2:EC2');
                $sheet->cell('DV2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('ED2:EH2');
                $sheet->cell('ED2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('EI2:EO2');
                $sheet->cell('EI2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('DS'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('DS3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('DT3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('DU3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('DV3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('DW3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('DX3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('DY3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('DZ3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('EA3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('EB3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('EC3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('ED3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('EE3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('EF3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('EG3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('EH3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('EI3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('EJ3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('EK3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('EL3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('EM3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('EN3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('EO3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });


                //--------- JULIO ----------//

                $sheet->mergeCells('EQ1:FM1');
                $sheet->cell('EQ1', function($cell) {
                    $cell->setValue(strtoupper(getMes(7)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('ET2:FA2');
                $sheet->cell('ET2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('FB2:FF2');
                $sheet->cell('FB2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('FG2:FM2');
                $sheet->cell('FG2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('EQ3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('ER3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('ES3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('ET3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('EU3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('EV3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('EW3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('EX3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('EY3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('EZ3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('FA3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('FB3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('FC3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('FD3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('FE3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('FF3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('FG3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('FH3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('FI3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('FJ3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('FK3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('FL3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('FM3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });


                //--------- AGOSTO ----------//

                $sheet->mergeCells('FO1:GK1');
                $sheet->cell('FO1', function($cell) {
                    $cell->setValue(strtoupper(getMes(8)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('FR2:FY2');
                $sheet->cell('FR2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('FZ2:GD2');
                $sheet->cell('FZ2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('GE2:GK2');
                $sheet->cell('GE2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('FO'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('FO3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('FP3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('FQ3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('FR3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('FS3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('FT3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('FU3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('FV3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('FW3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('FX3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('FY3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('FZ3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('GA3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('GB3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('GC3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('GD3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('GE3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('GF3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('GG3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('GH3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('GI3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('GJ3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('GK3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });


                //--------- SEPTIEMBRE ----------//

                $sheet->mergeCells('GM1:HI1');
                $sheet->cell('GM1', function($cell) {
                    $cell->setValue(strtoupper(getMes(9)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('GP2:GW2');
                $sheet->cell('GP2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('GX2:HB2');
                $sheet->cell('GX2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('HC2:HI2');
                $sheet->cell('HC2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('GM'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('GM3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('GN3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('GO3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('GP3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('GQ3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('GR3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('GS3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('GT3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('GU3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('GV3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('GW3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('GX3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('GY3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('GZ3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('HA3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('HB3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('HC3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('HD3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('HE3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('HF3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('HG3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('HH3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('HI3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });


                //--------- OCTUBRE ----------//

                $sheet->mergeCells('HK1:IG1');
                $sheet->cell('HK1', function($cell) {
                    $cell->setValue(strtoupper(getMes(10)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('HN2:HU2');
                $sheet->cell('HN2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('HV2:HZ2');
                $sheet->cell('HV2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('IA2:IG2');
                $sheet->cell('IA2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });



                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('HK'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });
                $sheet->cell('HK3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('HL3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('HM3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('HN3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('HO3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('HP3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('HQ3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('HR3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('HS3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('HT3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('HU3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('HV3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('HW3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('HX3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('HY3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('HZ3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('IA3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('IB3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('IC3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('ID3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('IE3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('IF3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('IG3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });


                //--------- NOVIEMBRE ----------//

                $sheet->mergeCells('II1:JE1');
                $sheet->cell('II1', function($cell) {
                    $cell->setValue(strtoupper(getMes(11)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('IL2:IS2');
                $sheet->cell('IL2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('IT2:IX2');
                $sheet->cell('IT2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('IY2:JE2');
                $sheet->cell('IY2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('II'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('II3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('IJ3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('IK3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('IL3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('IM3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('IN3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('IO3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('IP3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('IQ3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('IR3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('IS3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('IT3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('IU3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('IV3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('IW3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('IX3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('IY3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('IZ3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('JA3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('JB3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('JC3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('JD3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('JE3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });

                //--------- DICIEMBRE ----------//

                $sheet->mergeCells('JG1:KC1');
                $sheet->cell('JG1', function($cell) {
                    $cell->setValue(strtoupper(getMes(12)));
                    $cell->setAlignment('center');
                    $cell->setBackground('#c1c1c1');
                });

                $sheet->mergeCells('JJ2:JQ2');
                $sheet->cell('JJ2', function($cell) {
                    $cell->setValue('INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#FFEB3B');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('JR2:JV2');
                $sheet->cell('JR2', function($cell) {
                    $cell->setValue('BENEFICIOS SOCIALES');
                    $cell->setAlignment('center');
                    $cell->setBackground('#2196f3');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('JW2:KC2');
                $sheet->cell('JW2', function($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setAlignment('center');
                    $cell->setBackground('#00a65a');
                    $cell->setFontWeight('bold');
                });

                foreach ($idEmpleado as $key => $empleado)
                    $sheet->cell('JG'.(3+$key+1), function($cell) use ($empleado) { $cell->setValue(getCargo($empleado)->nombre); });

                $sheet->cell('JG3', function($cell) {  $cell->setValue('CARGO'); });
                $sheet->cell('JH3', function($cell) {  $cell->setValue('SALARIO'); });
                $sheet->cell('JI3', function($cell) {  $cell->setValue('HORAS LABORADAS'); });
                $sheet->cell('JJ3', function($cell) {  $cell->setValue('SUELDO LABORADO'); });
                $sheet->cell('JK3', function($cell) {  $cell->setValue('N_HE50'); });
                $sheet->cell('JL3', function($cell) {  $cell->setValue('N_HE100'); });
                $sheet->cell('JM3', function($cell) {  $cell->setValue('HE50_GANADO'); });
                $sheet->cell('JN3', function($cell) {  $cell->setValue('HE100_GANADO'); });
                $sheet->cell('JO3', function($cell) {  $cell->setValue('COMISIONES'); });
                $sheet->cell('JP3', function($cell) {  $cell->setValue('TRANSPORTE'); });
                $sheet->cell('JQ3', function($cell) {  $cell->setValue('MOVILIZACION'); });
                $sheet->cell('JR3', function($cell) {  $cell->setValue('10mo 3ero'); });
                $sheet->cell('JS3', function($cell) {  $cell->setValue('10mo 4to'); });
                $sheet->cell('JT3', function($cell) {  $cell->setValue('FONDO RESERVA'); });
                $sheet->cell('JU3', function($cell) {  $cell->setValue('VACACIONES'); });
                $sheet->cell('JV3', function($cell) {  $cell->setValue('APORTE PATRONAL'); });
                $sheet->cell('JW3', function($cell) {  $cell->setValue('APORTE PERSONAL'); });
                $sheet->cell('JX3', function($cell) {  $cell->setValue('PRESTAMOS IESS'); });
                $sheet->cell('JY3', function($cell) {  $cell->setValue('PRESTAMO OFICINA'); });
                $sheet->cell('JZ3', function($cell) {  $cell->setValue('ANTICIPOS'); });
                $sheet->cell('KA3', function($cell) {  $cell->setValue('CONSUMOS'); });
                $sheet->cell('KB3', function($cell) {  $cell->setValue('PERDIDAS'); });
                $sheet->cell('KC3', function($cell) {  $cell->setValue('OTROS_DESCUENTOS'); });

                $sheet->setAutoSize(true);

            });

        });

        $archivo = $archivo->string('xlsx');
        $response =  array(
            'name' => "Formato de proyección de nómina", //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($archivo) //mime type of used format
        );
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ini_set('max_execution_time',600);
        $valida =  Validator::make($request->all(), [
            'archivo' => 'required|mimes:xls,xlsx',
            'anno' => 'required'
        ]);

        $this->msg='';
        if(!$valida->fails()) {
            $objProyeccion = new Proyeccion;
            $objProyeccion->anno = $request->anno;

            if($objProyeccion->save()) {
                Excel::load($request->file('archivo'), function ($reader) {

                    $modelProyeccion = Proyeccion::all()->last();
                    $rows = $reader->get()->count();
                    $cantRows = $rows+1;
                    $sheet = $reader->getSheetByName('Datos de proyección');
                    $x = [4, 28, 52, 76, 100, 124, 148, 172, 196, 220, 244, 268];
                    $meses = ['C1','AA1','AY1','BW1','CU1','DS1','EQ1','FO1','GM1','HK1','II1','JG1'];

                    for($i=4;$i<=$cantRows;$i++){

                        $objEmpleadoProyeccion = new EmpleadoProyeccion;
                        $objEmpleadoProyeccion->id_empleado = (int)$sheet->getCell('A'.$i)->getValue();
                        $objEmpleadoProyeccion->id_proyeccion= $modelProyeccion->id_proyeccion;

                        if($objEmpleadoProyeccion->save()){

                            $modelEmpleadoProyeccion = EmpleadoProyeccion::all()->last();
                            foreach($meses as $m => $mes) {
                                if(!empty($sheet->getCell($mes)->getValue()) && $sheet->getCell($mes)->getValue() != null) {

                                    $objMesEmpleadoProyeccion = new MesEmpleadoProyeccion;
                                    $objMesEmpleadoProyeccion->id_empleado_proyeccion = $modelEmpleadoProyeccion->id_empleado_proyeccion;
                                    $objMesEmpleadoProyeccion->mes = array_search(ucfirst(strtolower($sheet->getCell($mes)->getValue())),getMes(0,true));

                                    if($objMesEmpleadoProyeccion->save()){

                                        $modelMesmpleadoProyeccion = MesEmpleadoProyeccion::all()->last();

                                        for ($l=$x[$m]; $l<=($x[$m]+21); $l++) {
                                            $coordenada = $this->getCoordenada($l);
                                            if (!empty($sheet->getCell($coordenada . $i)->getValue())) {

                                                $objItemMesEmpleadoProyeccion = new ItemMesEmpleadoProyeccion;
                                                $objItemMesEmpleadoProyeccion->id_mes_empleado_proyeccion = $modelMesmpleadoProyeccion->id_mes_empleado_proyeccion;
                                                $objItemMesEmpleadoProyeccion->id_item = array_search($sheet->getCell($coordenada . "3")->getValue(), arrItemsProyeccion());
                                                $objItemMesEmpleadoProyeccion->valor = $sheet->getCell($coordenada . $i)->getValue();

                                                if($objItemMesEmpleadoProyeccion->save()){
                                                    $this->msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                                          <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                           Proyección cargada con éxito
                                                        </div>';
                                                }else{
                                                    $this->msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                                                 Hubo un inconveniente al guardar los datos, intente nuevamente
                                                            </div>';
                                                   // //MesProyeccion::where('id_proyeccion',$modelProyeccion->id_proyeccion)->delete();
                                                    //Proyeccion::destroy($modelProyeccion->id_proyeccion);
                                                }
                                            }
                                        }

                                    }
                                }
                            }
                        }
                    }
                });
            }else{
                $this->msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                 Hubo un inconveniente al guardar los datos, intente nuevamente
                             </div>';
            }

        }else {

            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $this->msg .= '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';

        }
        return response()->json(['msg'=>$this->msg]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //dd($request->all());
        $cantMeses = $request->fecha_fin - $request->fecha_inicio;

        $meses = [];
        for($x=1;$x<=($cantMeses+1);$x++){
            $meses[] = ($request->fecha_inicio_calculo + $x);
        }

        return view('layouts.views.proyeccion_nomina.partials.proyeccion',[
            'proyeccion' => Proyeccion::where('anno',$request->anno)->first(),
            'meses' =>$meses
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function formProyeccion(Request $request){
        return view('layouts.views.proyeccion_nomina.partials.form_upload_programacion');
    }


    public function getCoordenada($coordenada){

        $x = [
            "1"=>"A","2"=>"B","3"=>"C","4"=>"D","5"=>"E",
            "6"=>"F","7"=>"G","8"=>"H","9"=>"I","10"=>"J",
            "11"=>"K","12"=>"L","13"=>"M","14"=>"N","15"=>"O",
            "16"=>"P","17"=>"Q","18"=>"R","19"=>"S","20"=>"T",
            "21"=>"U","22"=>"V","23"=>"W","24"=>"X","25"=>"Y",
            "26"=>"Z","27"=>"AA","28"=>"AB","29"=>"AC","30"=>"AD",
            "31"=>"AE","32"=>"AF","33"=>"AG","34"=>"AH","35"=>"AI",
            "36"=>"AJ","37"=>"AK","38"=>"AL","39"=>"AM","40"=>"AN",
            "41"=>"AO","42"=>"AP","43"=>"AQ","44"=>"AR","45"=>"AS",
            "46"=>"AT","47"=>"AU","48"=>"AV","49"=>"AW","50"=>"AX",
            "51"=>"AY","52"=>"AZ","53"=>"BA","54"=>"BB","55"=>"BC",
            "56"=>"BD","57"=>"BE","58"=>"BF","59"=>"BG","60"=>"BH",
            "61"=>"BI","62"=>"BJ","63"=>"BK","64"=>"BL","65"=>"BM",
            "66"=>"BN","67"=>"BO","68"=>"BP","69"=>"BQ","70"=>"BR",
            "71"=>"BS","72"=>"BT","73"=>"BU","74"=>"BV","75"=>"BW",
            "76"=>"BX","77"=>"BY","78"=>"BZ","79"=>"CA","80"=>"CB",
            "81"=>"CC","82"=>"CD","83"=>"CE","84"=>"CF","85"=>"CG",
            "86"=>"CH","87"=>"CI","88"=>"CJ","89"=>"CK","90"=>"CL",
            "91"=>"CM","92"=>"CN","93"=>"CO","94"=>"CP","95"=>"CQ",
            "96"=>"CR","97"=>"CS","98"=>"CT","99"=>"CU","100"=>"CV",
            "101"=>"CW","102"=>"CX","103"=>"CY","104"=>"CZ","105"=>"DA",
            "106"=>"DB","107"=>"DC","108"=>"DD","109"=>"DE","110"=>"DF",
            "111"=>"DG","112"=>"DH","113"=>"DI","114"=>"DJ","115"=>"DK",
            "116"=>"DL","117"=>"DM","118"=>"DN","119"=>"DO","120"=>"DP",
            "121"=>"DQ","122"=>"DR","123"=>"DS","124"=>"DT","125"=>"DU",
            "126"=>"DV","127"=>"DW","128"=>"DX","129"=>"DY","130"=>"DZ",
            "131"=>"EA","132"=>"EB","133"=>"EC","134"=>"ED","135"=>"EE",
            "136"=>"EF","137"=>"EG","138"=>"EH","139"=>"EI","140"=>"EJ",
            "141"=>"EK","142"=>"EL","143"=>"EM","144"=>"EN","145"=>"EO",
            "146"=>"EP","147"=>"EQ","148"=>"ER","149"=>"ES","150"=>"ET",
            "151"=>"EU","152"=>"EV","153"=>"EW","154"=>"EX","155"=>"EY",
            "156"=>"EZ","157"=>"FA","158"=>"FB","159"=>"FC","160"=>"FD",
            "161"=>"FE","162"=>"FF","163"=>"FG","164"=>"FH","165"=>"FI",
            "166"=>"FJ","167"=>"FK","168"=>"FL","169"=>"FM","170"=>"FN",
            "171"=>"FO","172"=>"FP","173"=>"FQ","174"=>"FR","175"=>"FS",
            "176"=>"FT","177"=>"FU","178"=>"FV","179"=>"FW","180"=>"FX",
            "181"=>"FY","182"=>"FZ","183"=>"GA","184"=>"GB","185"=>"GC",
            "186"=>"GD","187"=>"GE","188"=>"GF","189"=>"GG","190"=>"GH",
            "191"=>"GI","192"=>"GJ","193"=>"GK","194"=>"GL","195"=>"GM",
            "196"=>"GN","197"=>"GO","198"=>"GP","199"=>"GQ","200"=>"GR",
            "201"=>"GS","202"=>"GT","203"=>"GU","204"=>"GV","205"=>"GW",
            "206"=>"GX","207"=>"GY","208"=>"GZ","209"=>"HA","210"=>"HB",
            "211"=>"HC","212"=>"HD","213"=>"HE","214"=>"HF","215"=>"HG",
            "216"=>"HH","217"=>"HI","218"=>"HJ","219"=>"HK","220"=>"HL",
            "221"=>"HM","222"=>"HN","223"=>"HO","224"=>"HP","225"=>"HQ",
            "226"=>"HR","227"=>"HS","228"=>"HT","229"=>"HU","230"=>"HV",
            "231"=>"HW","232"=>"HX","233"=>"HZ","234"=>"IA","235"=>"IB",
            "236"=>"IC","237"=>"ID","238"=>"IE","239"=>"IF","240"=>"IG",
            "241"=>"IH","242"=>"II","243"=>"IJ","244"=>"IK","245"=>"IL",
            "246"=>"IM","247"=>"IN","248"=>"IO","249"=>"IP","250"=>"IQ",
            "251"=>"IR","252"=>"IS","253"=>"IT","254"=>"IU","255"=>"IV",
            "256"=>"IW","257"=>"IX","258"=>"IY","259"=>"IZ","260"=>"JA",
            "261"=>"JB","262"=>"JC","263"=>"JD","264"=>"JE","265"=>"JF",
            "266"=>"JG","267"=>"JH","268"=>"JI","269"=>"JK","270"=>"JL",
            "271"=>"JM","272"=>"JN","273"=>"JO","274"=>"JP","275"=>"JQ",
            "276"=>"JR","277"=>"JS","278"=>"JT","279"=>"JU","280"=>"JV",
            "281"=>"JW","282"=>"JX","283"=>"JY","284"=>"JZ","285"=>"KA",
            "286"=>"KB","287"=>"KC","288"=>"KD","289"=>"KE"
        ];
            return $x[$coordenada];
    }

}
