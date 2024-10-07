<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <a href="/">
                <div class="pull-left image">
                    @if(App\Models\ConfiguracionEmpresa::count()>0)
                        {!! "<img src='".asset("config_empresa/".getConfiguracionEmpresa()->imagen_empresa)."' class='img-circle' alt='User Image' style='height:45px'>" !!}
                    @else
                        {!! "<i class='fa fa-3x fa-user-circle' style='color: white;' aria-hidden='true'></i>" !!}
                    @endif
                </div>
                <div class="pull-left info">
                    @if(App\Models\ConfiguracionEmpresa::count()>0)
                        <p><b>{{mb_strtoupper(App\Models\ConfiguracionEmpresa::first()->nombre_empresa)}}</b></p>
                        <!-- Status -->
                    @else
                        <p>{{"Sistema"}}</p>
                    @endif
                </div>
            </a>
        </div>
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MENÚ</li>
            <!-- Optionally, you can add icons to the links -->
            @if(in_array('ADMIN',session('dataUsuario')['user_type']))
                @include('layouts.partials.items_menu.items_menu_administrador')
            @endif
            @if(in_array('EMPLOYEE',session('dataUsuario')['user_type']) || in_array('SUPERVISOR',session('dataUsuario')['user_type']) || in_array('NOMINA_SUPERVISOR_HE',session('dataUsuario')['user_type']) || in_array('SUPERVISOR_HORARIO',session('dataUsuario')['user_type']))
                @include('layouts.partials.items_menu.items_menu_empleado')
            @endif
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
