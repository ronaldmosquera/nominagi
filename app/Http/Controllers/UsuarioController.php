<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracionEmpresa;
use App\Models\Contrataciones;
use App\Models\Comisiones;
use App\Models\ImagenesRoles;
use App\Models\Party;

class UsuarioController extends Controller
{
   public function fichaUsuario(){

       return view('layouts.views.usuario.ficha_usuario',[
           'dataEmpresa'=> ConfiguracionEmpresa::select('nombre_empresa','imagen_empresa')->first(),
           'dataContacto' => Party::where('party.party_id',session('dataUsuario')['id_empleado'])
               ->join('party_relationship as pshp','party.party_id','pshp.party_id_from')
               ->join('person as pa','pshp.party_id_to','pa.party_id')
               //->join('party_contact_mech as pcm','pa.party_id','pcm.party_id')
               //->join('telecom_number as tn','pcm.contact_mech_id','tn.contact_mech_id')
               ->first(),
           'vacacionesAcumuladas' => Contrataciones::where([
               ['estado',1],
               ['id_tipo_contrato_descripcion',2],
               ['id_empleado',session('dataUsuario')['id_empleado']]
           ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')->select('vacaciones')->first()
       ]);
   }

   public function rolesEmpleado()
   {
       $dataRol = ImagenesRoles::where('id_empleado', session('dataUsuario')['id_empleado'])->select('fecha_nomina','nombre_imagen')->orderBy('fecha_nomina', 'Desc')->get();

       $data = [];

       foreach ($dataRol as $dR) {
           $extension = explode(".",$dR->nombre_imagen);
           if ($extension[1] == "jpg" || $extension[1] == "JPG" || $extension[1] == "png" || $extension[1] == "PNG")
               $data[] = [
                   'fecha_nomina' => $dR->fecha_nomina,
                   'nombre_imagen' => $dR->nombre_imagen,
               ];
       }

       return view('layouts.views.nomina.empleado.reporte_roles_pago',[
           'dataRoles' => manualPagination($data,12)
       ]);
   }

   public function comisionesEmpleado(){
       return view('layouts.views.comisiones.list',[
           'dataComisiones' => Comisiones::where('id_empleado',session('dataUsuario')['id_empleado'])
               ->orderBy('id_comisiones','Desc')->paginate(10),
       ]);
   }
}
