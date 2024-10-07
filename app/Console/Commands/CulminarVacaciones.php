<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vacaciones;
use Carbon\Carbon;

class CulminarVacaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Culminar:Vacaciones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca las vacaciones que esten activas y verifica la fecha fin y si coincide con la fecha actual les cambia el estado a "3"(Cumplidas)';

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
        $dataVacaciones = Vacaciones::where('estado',1)->get();

        if(count($dataVacaciones) > 0){

            foreach ($dataVacaciones as $vacaciones){

                if($vacaciones->fecha_fin == now()->toDateString() || $vacaciones->fecha_fin < now()->toDateString()){

                    $v = Vacaciones::find($vacaciones->id_vacaciones);
                    $v->estado = 3;

                    if($v->save()){
                        info("Vacaciones cumplidas con exito");
                    }else{
                        info("Hubo un error al anular las vacaciones");
                    }

                }
            }

        }

    }
}
