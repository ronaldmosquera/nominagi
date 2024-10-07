<li class="treeview">
    <a href="javascript:void(0)"><i class="fa fa-users"></i> <span>Administración empleados</span>
        <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{route('empleados.index')}}"><i class="fa fa-circle-o"></i>Empleados</a></li>
        <li><a href="{{route('horarios.index')}}"><i class="fa fa-circle-o"></i>Horarios de empleados</a></li>
        <li><a href="{{route('otros-descuentos.index')}}"><i class="fa fa-circle-o"></i>Otros descuentos</a></li>
    </ul>
</li>
<li class="treeview">
    <a href="javascript:void(0)"><i class="fa fa-cogs"></i> <span>Configuraciones</span>
        <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{route('cargos.index')}}"><i class="fa fa-circle-o"></i>Cargos</a></li>
        <li><a href="{{route('contrato.index')}}"><i class="fa fa-circle-o"></i>Contratos</a></li>
        <li><a href="{{route('documentos.index')}}"><i class="fa fa-circle-o"></i>Documentos</a></li>
        <li><a href="{{route('configuracion-empresa.index')}}"><i class="fa fa-circle-o"></i>Empresa</a></li>
        <li><a href="{{route('iva.index')}}"><i class="fa fa-circle-o"></i>Iva</a></li>
        <li><a href="{{route('anulacion-contrato.index')}}"><i class="fa fa-circle-o"></i>Motivos de terminación</a></li>
        {{--<li><a href="{{route('productos.index')}}"><i class="fa fa-circle-o"></i>Productos</a></li>--}}
        <li><a href="{{route('tipo-contrato.index')}}"><i class="fa fa-circle-o"></i>Tipo de contratos</a></li>
        <li><a href="{{route('tipo-comisiones.index')}}"><i class="fa fa-circle-o"></i>Tipo de comisiones</a></li>
        <li><a href="{{url('permisos')}}"><i class="fa fa-circle-o"></i>Permisos</a></li>
    </ul>
</li>
<li>
    <a href="{{route('comisiones.index')}}">
        <i class="fa fa-money"></i>
        <span>Comisiones</span>
    </a>
</li>
<li>
    <a href="{{route('contrataciones.index')}}">
        <i class="fa fa-id-card-o"></i>
        <span>Contrataciones</span>
    </a>
</li>
<li class="treeview">
    <a href="javascript:void(0)"><i class="fa fa-cubes"></i> <span>Solicitudes</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{route('vista.admin_anticipos')}}"><i class="fa fa-circle-o"></i>
                <span>Anticipos</span>
                <span class="pull-right-container">
                    <small class="label pull-right bg-yellow">{{\App\Models\Anticipos::where('estado',0)->count()}}</small>
                    </span>
            </a>

            <a href="{{route('vista.list_horas_extras_admin')}}"><i class="fa fa-circle-o"></i>
                <span>Horas extras</span>
                <span class="pull-right-container">
                    @php
                        $empleadosActivos = \App\Models\ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
                                                        ->where('contrataciones.estado',1)->select('p.party_id')->distinct()->pluck('p.party_id');
                        $cantHE = \App\Models\HorasExtra::whereIn('id_empleado',$empleadosActivos->toArray())->where('estado',0)->count();
                    @endphp
                    <small class="label pull-right bg-yellow">{{$cantHE}}</small>
                </span>
            </a>
            <a href="{{route('vista.list_vacaciones_admin')}}"><i class="fa fa-circle-o"></i>
                <span>Vacaciones</span>
                <span class="pull-right-container">
                    <small class="label pull-right bg-yellow">{{\App\Models\Vacaciones::where('estado',0)->count()}}</small>
                    </span>
            </a>
            <a href="{{route('vista.admin_consumos')}}"><i class="fa fa-circle-o"></i>
                <span>Consumos</span>
                <span class="pull-right-container">
                    <small class="label pull-right bg-yellow"></small>
                    </span>
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="javascript:void(0)"><i class="fa fa-calculator"></i> <span>Nómina</span>
        <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
    </a>
    <ul class="treeview-menu">
       {{-- @if(\App\Models\Nomina::count() === 0)
        <li style="cursor: pointer;"><a onclick="form_nivelar_nomina()"><i class="fa fa-circle-o"></i>Nivelar nómina</a></li>
        @else
            <li ><a  href="{{route('nomina.index',["store"=> 0])}}"><i class="fa fa-circle-o"></i>Ver nómina</a></li>
        @endif--}}
        <li><a href="{{route('vista.listado_nomina')}}"><i class="fa fa-circle-o"></i>Generar nómina</a></li>
        <li><a href="{{route('informe.nomina')}}"><i class="fa fa-circle-o"></i>Informe de nómina</a></li>
        <li><a href="{{route('vista.roles-pago',['estado'=>0])}}"><i class="fa fa-circle-o"></i>Roles de nómina</a></li>
       {{--<li><a href="{{route('proyeccion-nomina.index')}}"><i class="fa fa-circle-o"></i>Proyección nómina</a></li>--}}
    </ul>
</li>




