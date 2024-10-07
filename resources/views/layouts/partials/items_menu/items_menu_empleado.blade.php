@php
    $contratacion = getContratacionByEmpleado(session('dataUsuario')['id_empleado']);
@endphp

@if(isset($contratacion))
<li>
    <a href="{{route('vista.solicitud_documentos')}}">
        <i class="fa fa-file-text" aria-hidden="true"></i>
        Documentos
    </a>
</li>
<li>
    <a href="{{route('ficha')}}">
        <i class="fa fa-id-badge" aria-hidden="true"></i>
        <span>Ficha empleado</span>
    </a>
</li>
<li class="treeview">
    <a href="javascript:void(0)"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span>Solicitudes</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{route('anticipos.index')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Anticipos</a></li>
        <li><a href="{{route('consumos.index')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Consumos</a></li>
        <li><a href="{{route('horas-extras.index')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Horas extras</a></li>
        <li><a href="{{route('vacaciones.index')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Vacaciones</a></li>
    </ul>
</li>
<li>
    <a href="{{route('vista.comisiones_empleado')}}">
        <i class="fa fa-money" aria-hidden="true"></i>
        Comisiones
    </a>
<li>
    <a href="{{route('vista.contrataciones')}}">
        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
        Contratos
    </a>
</li>
<li>
    <a href="{{route('vista.descuentos_empleados')}}">
        <i class="fa fa-money" aria-hidden="true"></i>
        Descuentos
    </a>
</li>
<li>
    <a href="{{route('vista.roles-pago-empleado')}}">
        <i class="fa fa-id-card-o" aria-hidden="true"></i>
        Roles de pago
    </a>
</li>
@endif
@if(!in_array('ADMIN',session('dataUsuario')['user_type']))
    @php $rutasAdmin = collect(session('dataUsuario')['rutas_disponibles']); @endphp
    @if($rutasAdmin->count() > 0)
        <li class="treeview">
            <a href="javascript:void(0)"><i class="fa fa-cogs" aria-hidden="true"></i> <span>Sección administrador</span>
                <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
            </a>
            <ul class="treeview-menu">
                @foreach($rutasAdmin as $ruta)
                    @if($ruta === "empleados")
                        <li><a href="{{route('empleados.index')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Empleados</a></li>
                    @endif
                    @if($ruta === "horarios")
                        <li><a href="{{route('horarios.index')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Horarios de empleados</a></li>
                    @endif
                    @if($ruta === "otros-descuentos")
                        <li><a href="{{route('otros-descuentos.index')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Otros descuentos</a></li>
                    @endif
                    @if($ruta === "comisiones")
                        <li><a href="{{route('comisiones.index')}}"><i class="fa fa-circle-o" aria-hidden="true"></i><span>Comisiones</span></a></li>
                    @endif
                    @if($ruta === "contrataciones")
                        <li><a href="{{route('contrataciones.index')}}"><i class="fa fa-circle-o" aria-hidden="true"></i><span>Contrataciones</span></a></li>
                    @endif
                    @if($ruta === "admin-anticipos")
                        <li>
                            <a href="{{route('vista.admin_anticipos')}}"><i class="fa fa-circle-o" aria-hidden="true"></i><span>Anticipos</span>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-yellow">{{\App\Models\Anticipos::where('estado',0)->count()}}</small>
                                </span>
                            </a>
                        </li>
                    @endif
                    @if($ruta === "administrar-horas-extras")
                        <li>
                            <a href="{{route('vista.list_horas_extras_admin')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>
                                <span>Horas extras</span>
                                @php
                                    $empleadosActivos = \App\Models\ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
                                    ->where('contrataciones.estado',1)->select('first_name','last_name','p.party_id')->distinct()->get();

                                    $idEmpleados =[];

                                    foreach ($empleadosActivos as $empleadoActivo)
                                        $idEmpleados[]=$empleadoActivo->party_id;

                                    $cantHorasExtras = \App\Models\HorasExtra::whereIn('id_empleado',$idEmpleados)->where('estado',0)->count();
                                @endphp
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-yellow">{{$cantHorasExtras}}</small>
                                </span>
                            </a>
                        </li>
                    @endif
                    @if($ruta === "administrar-vacaciones")
                        <li>
                            <a href="{{route('vista.list_vacaciones_admin')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>
                                <span>Vacaciones</span>
                                <span class="pull-right-container">
                                <small class="label pull-right bg-yellow">{{\App\Models\Vacaciones::where('estado',0)->count()}}</small>
                                </span>
                            </a>
                        </li>
                    @endif
                    @if($ruta === "admin-consumos")
                        <li>
                            <a href="{{route('vista.admin_consumos')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>
                                <span>Consumos</span>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-yellow"></small>
                                </span>
                            </a>
                        </li>
                    @endif
                    @if($ruta === "ver-nomina")
                        <li><a href="{{route('vista.listado_nomina')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Generar nómina</a></li>
                    @endif
                    @if($ruta === "roles-pago")
                    <li><a href="{{route('vista.roles-pago')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Roles de nómina</a></li>
                    @endif
                    @if($ruta === "informe-nomina")
                            <li><a href="{{route('informe.nomina')}}"><i class="fa fa-circle-o" aria-hidden="true"></i>Informe de nómina</a></li>
                        @endif
                @endforeach
            </ul>
    @endif
@endif

