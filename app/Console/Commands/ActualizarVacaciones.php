<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrataciones;
use App\Models\TipoContratos;
use App\Models\ConfiguracionVariablesEmpresa;
use App\Models\DetalleContratacion;
use Carbon\Carbon;

class ActualizarVacaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Actualizar:vacaciones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando añade tiempo de vacaciones a las contrataciones que esten bajo relación de dependecia según transcurren los días';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dataContrataciones = Contrataciones::where([
            ['contrataciones.id_tipo_contrato_descripcion',2],
            ['contrataciones.estado',1],
        ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')->get();

        foreach ($dataContrataciones as $contrataciones){

            $tipoContrato = TipoContratos::where('id_tipo_contrato',$contrataciones->id_tipo_contrato)->select('relacion_dependencia')->first();

            if($tipoContrato->relacion_dependencia){

                $diasVacaciones = ConfiguracionVariablesEmpresa::select('vacaciones_dias_entre_semana','vacaciones_dias_fines_semana')->first();
                $diasVacaciones = $diasVacaciones->vacaciones_dias_entre_semana + $diasVacaciones->vacaciones_dias_fines_semana;
                $diasVacacionesDiarias = $diasVacaciones/360;

                $objDetalleContratacion = DetalleContratacion::find($contrataciones->id_detalle_contrataciones);
                $objDetalleContratacion->vacaciones += $diasVacacionesDiarias;
                $objDetalleContratacion->save();

            }

        }
    }
}
