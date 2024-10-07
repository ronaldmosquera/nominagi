@extends('layouts.principal')
@section('title')
    Datos empleado
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-12 text-center">
                        <h3 class="box-title">DATOS DEL EMPLEADO</h3>
                    </div>
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2" style="border: 1px solid #cecece;border-radius:5px">
                            <div class="box-body box-profile">
                                <img class="profile-user-img img-responsive img-circle" src="{{"/config_empresa/".$dataEmpresa->imagen_empresa}}" alt="User profile picture">
                                <h3 class="profile-username text-center">{{strtoupper($dataEmpresa->nombre_empresa)}}</h3>
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-4 pr-1">
                                                <div class="form-group">
                                                    <label>Nombre empleado</label>
                                                    <input type="text" class="form-control" disabled="" placeholder="Company" value="{{ucwords(session('dataUsuario')['primer_nombre'])}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 px-1">
                                                <div class="form-group">
                                                    <label>Apellidos</label>
                                                    <input type="text" class="form-control" disabled="" value="{{ucwords(session('dataUsuario')['apellido'])}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 pl-1">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Documento de identidad</label>
                                                    <input type="email" class="form-control" disabled="" value="{{ucwords(session('dataUsuario')['identifiacion'])}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 pl-1">
                                                <div class="form-group">
                                                    <label>Teléfono</label>
                                                    <input type="text" class="form-control" disabled="" value="{{session('dataUsuario')['contact_number']}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 pr-1">
                                                <div class="form-group">
                                                    <label>Fecha de nacimiento</label>
                                                    <input type="text" class="form-control" disabled="" value="{{ucwords(session('dataUsuario')['cumpleannos'])}}" >
                                                </div>
                                            </div>
                                            <div class="col-md-4 pl-1">
                                                <div class="form-group">
                                                    <label>Género</label>
                                                    <input type="text" class="form-control" disabled="" value="{{session('dataUsuario')['genero'] == "M" ? "Masculino" : "Femenino" }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Dirección</label>
                                                    <input type="text" class="form-control" disabled="" value="{{ucwords(session('dataUsuario')['direccion'])}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 px-1">
                                                <div class="form-group">
                                                    <label>Nacionalidad</label>
                                                    <input type="text" class="form-control" disabled="" value="{{ucwords(session('dataUsuario')['nacionalidad'])}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 pl-1">
                                                <div class="form-group">
                                                    <label>Provincia</label>
                                                    <input type="text" class="form-control" disabled="" value="{{ucwords(getPostalAddres(session('dataUsuario')['id_empleado'])->address1)}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 pr-1">
                                                <div class="form-group">
                                                    <label>Ciudad</label>
                                                    <input type="text" class="form-control" disabled="" value="{{ucwords(getPostalAddres(session('dataUsuario')['id_empleado'])->city)}}">
                                                </div>
                                            </div>
                                        </div>
                                        <label style="margin-top: 20px;font-size: 11pt">Datos de contacto</label>
                                        <hr style="margin-top: 0"/>
                                        <div class="row">
                                            <div class="col-md-4 px-1">
                                                <div class="form-group">
                                                    <label>Nombre</label>
                                                    <input type="text" class="form-control" disabled="" value="{{isset($dataContacto->first_name) ? ucwords($dataContacto->first_name) : ""}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 pl-1">
                                                <div class="form-group">
                                                    <label>Apellido</label>
                                                    <input type="text" class="form-control" disabled="" value="{{isset($dataContacto->last_name) ? ucwords($dataContacto->last_name) : ""}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 pr-1">
                                                <div class="form-group">
                                                    <label>Teléfono</label>
                                                    <input type="text" class="form-control" disabled="" value="{{isset($dataContacto->party_id) ? getTelecomNumber($dataContacto->party_id)->contact_number : ""}}">
                                                </div>
                                            </div>
                                        </div>
                                        <label style="margin-top: 20px;font-size: 11pt"> Vacaciones acumuladas hasta a la fecha</label>
                                        <hr style="margin-top: 0"/>
                                        <div class="row">
                                            <div class="col-md-4 px-1">
                                                <div class="form-group">
                                                    <label>Días</label>
                                                    <input type="text" class="form-control" disabled="" value="{{number_format($vacacionesAcumuladas->vacaciones,0)}}">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
