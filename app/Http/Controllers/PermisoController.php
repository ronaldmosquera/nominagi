<?php

namespace App\Http\Controllers;

use App\Models\DetallePermisoSeccionMenu;
use App\Models\RolPermisoSeccionMenu;
use App\Models\TipoRol;
use Illuminate\Http\Request;
use App\Models\SeccionMenu;
use App\Models\SubSeccionMenu;
use App\Models\PermisoSeccionMenu;
use App\Models\RutaSubSeccionMenu;


class PermisoController extends Controller
{


    public function inicio(){

        return view('layouts.views.permisos.inicio',[
            'secciones' => SeccionMenu::all(),
            'roles' => TipoRol::orderBy('description','asc')->get(),
        ]);

    }

    public function ver_seccion(Request $request){

        //$idRolSeccionMenu = [];
        $idRutaSubSeccionMenu = [];
        $permisoSeccionMenu = PermisoSeccionMenu::where('id_seccion_menu',$request->id_seccion_menu);

        if(isset($request->rol))
            $permisoSeccionMenu->join('rol_permiso_seccion_menu as rpsm','permiso_seccion_menu.id_permiso_seccion_menu','rpsm.id_permiso_seccion_menu')
            ->where('rpsm.role_type_id',$request->rol);

        $permisoSeccionMenu = $permisoSeccionMenu->get();

        //dd($permisoSeccionMenu);

        foreach ($permisoSeccionMenu as $pSM)
            if(isset($pSM->detalle_permiso_seccion_menu))
                foreach ($pSM->detalle_permiso_seccion_menu as $item)
                    $idRutaSubSeccionMenu[] = $item->id_ruta_sub_seccion_menu;

        /*if(isset($permisoSeccionMenu->rol_permiso_seccion_menu))
            foreach ($permisoSeccionMenu->rol_permiso_seccion_menu as $item)
                $idRolSeccionMenu[] = $item->role_type_id;*/


        return view('layouts.views.permisos.partials.sub_secciones',[
            'subSecciones' => SubSeccionMenu::where('id_seccion_menu',$request->id_seccion_menu)
                ->orderBy('nombre')->get(),
            'roles' => TipoRol::orderBy('description','asc')->get(),
            'permisoSeccionMenu' => $idRutaSubSeccionMenu,
            'vista' => $request->id,
            'id_seccion_menu' => $request->id_seccion_menu
            //'rolsSeccionMenu' =>$idRolSeccionMenu,
        ]);
    }

    public function storePermiso(Request $request){

        //dd($request->all());
        $msg = '<div class="alert alert-danger" role="alert" style="margin: 10px">
                     Hubo un error al tratar de guardar la configuración de los permisos, intente nuevamente
                 </div>';
        $status = 0;

        //$del = PermisoSeccionMenu::where('id_seccion_menu',$request->id_seccion_menu)->get();

        $objPermisoSeccionMenu = new PermisoSeccionMenu;
        $objPermisoSeccionMenu->id_seccion_menu = $request->id_seccion_menu;
        if($objPermisoSeccionMenu->save()){ //GUARDA ADMINISTRACION EMPLEADOS

            $modelPermisoSeccionMenu = PermisoSeccionMenu::All()->last();

            $x = 0;
            foreach ($request->arr_check as $check){
                $objDetallePermisoSeccionMenu = new DetallePermisoSeccionMenu;
                $objDetallePermisoSeccionMenu->id_permiso_seccion_menu = $modelPermisoSeccionMenu->id_permiso_seccion_menu;
                $objDetallePermisoSeccionMenu->id_ruta_sub_seccion_menu = $check['id_ruta_sub_seccion_menu'];
                if($objDetallePermisoSeccionMenu->save()){
                    $x++;
                }else{
                    PermisoSeccionMenu::destroy($modelPermisoSeccionMenu->id_permiso_seccion_menu);
                    break;
                }
            }
            $y = 0;
            if($x == count($request->arr_check)){
                foreach ($request->arr_roles as $rol) {
                    $objRolPermisoSeccionMenu = new RolPermisoSeccionMenu;
                    $objRolPermisoSeccionMenu->id_permiso_seccion_menu = $modelPermisoSeccionMenu->id_permiso_seccion_menu;
                    $objRolPermisoSeccionMenu->role_type_id = $rol['rol'];
                    if($objRolPermisoSeccionMenu->save()) $y++;
                }
            }

            if($y == count($request->arr_roles)){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 10px">
                     Se ha guardado la configuración de los permisos con éxito
                 </div>';
                $status = 0;

                //foreach ($del as $item)
                    //PermisoSeccionMenu::destroy($item->id_permiso_seccion_menu);
            }

        }

        
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function deletePermiso(Request $request){

        $msg = '<div class="alert alert-danger" role="alert" style="margin: 0px">
                     Hubo un error al tratar de eliminar el permiso al rol seleccionado, intente nuevamente
                 </div>';
        $status = 0;

        $rutaSubSeccionMenu = RutaSubSeccionMenu::where('id_ruta_sub_seccion_menu',$request->id_ruta_sub_seccion_menu)->first();

        foreach ($rutaSubSeccionMenu->sub_seccion_menu->seccion_menu->permiso_seccion_menu as $permiso_seccion_menu) {
            foreach($permiso_seccion_menu->rol_permiso_seccion_menu->where('role_type_id',$request->rol) as $rol_permiso_seccion_menu){
                $objDetallePermisoSeccionMenu = DetallePermisoSeccionMenu::where([
                    ['id_permiso_seccion_menu',$rol_permiso_seccion_menu->id_permiso_seccion_menu],
                    ['id_ruta_sub_seccion_menu',$request->id_ruta_sub_seccion_menu]
                ])->get();

                foreach($objDetallePermisoSeccionMenu as $detPerSecMen)
                    if(isset($detPerSecMen->id_detalle_permiso_seccion_menu))
                        DetallePermisoSeccionMenu::destroy($detPerSecMen->id_detalle_permiso_seccion_menu);

            }
        }

        $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                     Se ha eliminado el permiso para el rol seleccionado con éxito
                 </div>';
        $status = true;

        return response()->json(['success'=>$status,'msg'=>$msg]);

    }
}
