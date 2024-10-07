<?php

namespace App\Http\Controllers;

use App\Models\Contrataciones;
use App\Models\DetalleContratacion;
use Illuminate\Http\Request;
use App\Models\Documentos;
use App\Models\ConfiguracionEmpresa;
use Barryvdh\DomPDF\Facade as PDF;


class DocumentosEmpleadoController extends Controller
{
    public function index(){
      
        $contratacion = getContratacionByEmpleado(session('dataUsuario')['id_empleado']);

        if(!isset($contratacion))
            return view('layouts.views.errors.sin_contrato');


        return view('layouts.views.solicitud_documentos.list',
            [
                'dataDocumentos' => Documentos::where('relacion_dependencia',$contratacion->tipo_contratacion->relacion_dependencia)->get()
            ]);
    }

    public function generarDocumento($idDocumento){

        $textoDocumentos = Documentos::where('id_documentos', $idDocumento)
                          ->select('cuerpo_documento')->first();
        $dataEmpresa    = ConfiguracionEmpresa::first();

        $detallesContratacionEmpleado = Contrataciones::where([
            ['id_empleado',session('dataUsuario')['id_empleado']],
            ['estado',1]
        ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','=','dc.id_contrataciones')
          ->join('cargos as c','dc.id_cargo','=','c.id_cargo')
          ->select('c.nombre as cargo','dc.salario','dc.horas_jornada_laboral')
          ->first();

        $tags = [
            '[NOMBRE_EMPRESA]',
            '[ID_EMPRESA]',
            '[DIREC_EMPRESA]',
            '[IMG_EMPRESA]',
            '[NOMBRE_REP_EMPRESA]',
            '[ID_REP_EMPRESA]',
            '[NOMBRE_EMPLEADO]',
            '[ID_EMPLEADO]',
            '[DIREC_EMPLEADO]',
            '[CARGO_EMPLEADO]',
            '[SALARIO_EMPLEADO]',
            '[HORAS_TRABAJO]',
            '[D_ACTUAL]',
            '[M_ACTUAL]',
            '[A_ACTUAL]',
            '[SALTO_DE_PAGINA]',
            '[NACIONALIDAD]',
            '[SALARIO_LETRAS]'
        ];

        $data = [
            ucwords($dataEmpresa->nombre_empresa),
            $dataEmpresa->ruc,
            ucwords($dataEmpresa->direccion_empresa),
            "<img src=".getcwd()."/config_empresa/".$dataEmpresa->imagen_empresa.">",
            ucwords($dataEmpresa->representante),
            $dataEmpresa->identificacion_representante,
            session('dataUsuario')['primer_nombre']." ".session('dataUsuario')['apellido'], //nombre empleado
            session('dataUsuario')['identifiacion'],  //identificacion empleado
            session('dataUsuario')['ciudad']." ".session('dataUsuario')['direccion'],  //direccion empleado
            $detallesContratacionEmpleado->cargo,  //cargo empleado
            $detallesContratacionEmpleado->salario,  //salario empleado
            $detallesContratacionEmpleado->horas_jornada_laboral,  //horas de trabajo
            date('d'),
            getMes(intval(date('m'))),
            date('Y'),
            "<div style='page-break-after:always;'></div>", // Salto de página
            session('dataUsuario')['nacionalidad'], //Nacionalidad empleado
            strtoupper(valorEnLetras($detallesContratacionEmpleado->salario)) // Salario en letras
        ];

         $nuevaCadena = preg_replace($tags,$data,$textoDocumentos->cuerpo_documento);
         $eliminar = ['[',']'];
         $vacio = ['', ''];
         $cadenaFormateada = str_replace($eliminar, $vacio, $nuevaCadena);

         $pdf = PDF::loadView('layouts.views.solicitud_documentos.partials.vista_documento',compact('cadenaFormateada'));
         return $pdf->download('documento.pdf');

    }
}
