<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ConfiguracionEmpresa;

class SolicitudVacaciones extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $person;
    public $fecha_inicio;
    public $fecha_fin;

    public function __construct($person,$fecha_inicio,$fecha_fin)
    {
       $this->person = $person;
       $this->fecha_inicio = $fecha_inicio;
       $this->fecha_fin = $fecha_fin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $config_empresa = ConfiguracionEmpresa::first();
        return $this->from($config_empresa->correo_empresa)
            ->view('layouts.views.mails.solicitud_vacaciones',[
                'person' => $this->person,
                'desde' => $this->fecha_inicio,
                'hasta' => $this->fecha_fin,
                'empresa' => $config_empresa
            ]);
    }
}
