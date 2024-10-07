<script src="{{asset('bower_components/jquery/dist/jquery.min.js')}}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<header class="main-header">
        <!-- Logo -->
        <a href="/" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>N</b>OM</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg">Nómina</span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <!--<img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
                            <i class="fa fa-user-circle" aria-hidden="true"></i>
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs">{{ session('dataUsuario')['primer_nombre'] ." ". session('dataUsuario')['medio_nombre'] ." ".  session('dataUsuario')['apellido'] }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <a href="/" >
                                        @php
                                            $imagenEmpresa = App\Models\ConfiguracionEmpresa::all();
                                            if(!empty($imagenEmpresa[0]->imagen_empresa)){
                                                echo "<img class='img-circle' style='width:98px' src='".asset("config_empresa/".$imagenEmpresa[0]->imagen_empresa)."' class='img-circle' alt='User Image'>";
                                            }else{
                                                echo "<i class='fa fa-3x fa-user-circle' style='color: white;' aria-hidden='true'></i>";
                                            }
                                        @endphp
                                    <p>
                                        {{ session('dataUsuario')['primer_nombre'] ." ". session('dataUsuario')['medio_nombre'] ." ".  session('dataUsuario')['apellido'] }}
                                    </p>
                                </a>
                            </li>
                            <!-- Menu Body -->
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="text-center">
                                    <a href="{{route('logout')}}" class="btn btn-default btn-flat">
                                        <i class="fa fa-sign-out"></i> Salir
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
