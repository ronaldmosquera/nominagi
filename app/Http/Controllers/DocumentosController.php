<?php

namespace App\Http\Controllers;

use App\Models\Documentos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class DocumentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       return view('layouts.views.documentos.list',
           [
               'dataDocumentos' => Documentos::where('estado',isset($request->estado) ? $request->estado : 1)
                   ->orderBy('id_documentos','Desc')
                   ->paginate(10)
           ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('layouts.views.documentos.partials.form_documentos');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $valida =  Validator::make($request->all(), [
            'nombre_documento' => 'required',
            'tipo_documento' => 'required',
            'relacion_dependencia' => 'required',
            'cuerpo_documento' => function($attribute,$value,$onFailure) use ($request){
                if($request->tipo_documento =='TRANSCRITO' && !isset($value))
                    $onFailure('Debe transcribir el cuerpo del documento');
            }

        ],[
            'tipo_documento' =>'Debe seleccionar el tipo de documento a crear o cargar',
            'nombre_documento.required' => 'Debe escribir el nombre del documento',
            'relacion_dependencia.required' => 'Debe seleccionar que tipo de contrato usará el documento',
        ]);

        $msg ='';
        if(!$valida->fails()) {

            $objDocumentos = empty($request->id_documento) ? new Documentos : Documentos::find($request->id_documento);

            $objDocumentos->nombre = $request->nombre_documento;
            if($request->tipo_documento == 'TRASCRITO')
                $objDocumentos->cuerpo_documento = $request->cuerpo_documento;

            if($request->tipo_documento == 'PDF'){
                $archivo = $request->file('file');
                $imagen = mt_rand().'_'.mt_rand().$archivo->getClientOriginalName();

                Storage::disk('public')->put($imagen, \File::get($archivo));
                $objDocumentos->file = $imagen;
            }
            $objDocumentos->tipo_documento = $request->tipo_documento;
            $objDocumentos->relacion_dependencia = $request->relacion_dependencia;
            $objDocumentos->estado = 1;

            if( $objDocumentos->save()){

                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                            El documento se ha guardado con éxito
                        </div>';
                $status = 1;

            }else{
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                      Hubo un error al guardar el docuemnto, intente nuevamente
                </div>';
                $status = 0;
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
            $msg .= '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
            $status = 0;
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_documento)
    {
        return view('layouts.views.documentos.partials.form_documentos',
        [
            'dataDocumento' => Documentos::where('id_documentos',$id_documento)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

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

    public function actualizarDocumento(Request $request){

        $dataDocumentos = Documentos::find($request->id_documento);
        $dataDocumentos->estado = $request->estado == 1 ? 0 : 1;

        if($dataDocumentos->update()){

            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      El estado se ha actualizado con exito
                </div>';
            $success = true;

        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                      Hubo un error al actualizar el estado del docuemnto, intente nuevamente
                </div>';
            $success = false;

        }
        return response()->json(['msg'=>$msg,'success'=>$success]);
    }
}
