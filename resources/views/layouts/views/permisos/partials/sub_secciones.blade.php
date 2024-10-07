<div class="col-md-12">
    <div class="box">
    <div class="box-header">
        <h3 class="box-title">Sub menú</h3>
    </div>
    <div class="panel-group " id="accordion" role="tablist" aria-multiselectable="true">
        <input type="hidden" id="id_seccion_menu" name="id_seccion_menu" value="{{isset($id_seccion_menu) ? $id_seccion_menu : ""}}">
        @foreach($subSecciones as $x => $subSeccion)
            <div class="panel panel-default">
                <div class="panel-heading sub_menu" role="tab" id="heading_{{$x+1}}">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{$vista === "sub_seccion_menu_edit" ? "edit" : ""}}_{{$x+1}}"
                           aria-expanded="true" aria-controls="collapse_{{$x+1}}">
                            <i class="fa fa-circle-o" ></i> {{$subSeccion->nombre}}
                        </a>
                        @if($vista === "sub_seccion_menu")
                            <span class="pull-right" title="Marcar todos los items">
                                <input type="checkbox" id="check_master_{{$x+1}}" name="check_master_{{$x+1}}" onclick="check_general(this)">
                            </span>
                        @else
                            {{--<button type="button" class="btn btn-danger" onclick="eliminar_permiso_general('{{$subSeccion->id_sub_seccion_menu}}')">
                                <i class="fa fa-trash"></i>
                            </button>--}}
                        @endif
                    </h4>
                </div>
                <div id="collapse_{{$vista === "sub_seccion_menu_edit" ? "edit" : ""}}_{{$x+1}}" class="panel-collapse collapse {{($x == 0) ? "in" : ""}} " role="tabpanel"
                     aria-labelledby="heading_{{$x+1}}">
                    <div class="panel-body">
                        <table class="table table_check_individual table-striped check_master_{{$x+1}}">
                            <tr>
                                <th class="text-center">Descripcion</th>
                                <th class="text-center">Ruta</th>
                                <th class="text-center">{{$vista=== "sub_seccion_menu_edit" ? "Eliminar" : "Agregar"}}</th>
                            </tr>
                            @foreach($subSeccion->ruta_sub_seccion_menu as $y => $rutaSubSeccinoMenu)
                                <tr class="tr_check_individual">
                                    <th class="text-center">{{$rutaSubSeccinoMenu->nombre}}</th>
                                    <th class="text-center">{{$rutaSubSeccinoMenu->url}}</th>
                                    <th class="text-center" style="vertical-align: middle;">
                                        @if($vista === "sub_seccion_menu")
                                            <input type="checkbox" id="check_individual" class="check_individual"
                                                    {{--{{(in_array($rutaSubSeccinoMenu->id_ruta_sub_seccion_menu,$permisoSeccionMenu)) ? 'checked' : ''}}--}}
                                                   name="check_individual" value="{{$rutaSubSeccinoMenu->id_ruta_sub_seccion_menu}}">
                                        @else
                                            @if(in_array($rutaSubSeccinoMenu->id_ruta_sub_seccion_menu,$permisoSeccionMenu))
                                                <button type="button" class="btn btn-danger" onclick="eliminar_permiso('{{$rutaSubSeccinoMenu->id_ruta_sub_seccion_menu}}')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </th>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
    @if($vista === "sub_seccion_menu")
        <div class="text-center" style="margin-bottom: 20px;">
            <button class="btn btn-primary" onclick="store_configuracion_menu('{{$subSeccion->seccion_menu->id_seccion_menu}}')">
                <i class="fa fa-floppy-o"></i> Guardar
            </button>
        </div>
    @endif
</div>

