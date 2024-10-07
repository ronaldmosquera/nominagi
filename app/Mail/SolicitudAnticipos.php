<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ConfiguracionEmpresa;

class SolicitudAnticipos extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $person;
    public $fecha_entrega;
    public $fecha_descuento;
    public $monto;

    public function __construct($person,$fecha_entrega,$fecha_descuento,$monto)
    {
        $this->person = $person;
        $this->fecha_entrega = $fecha_entrega;
        $this->fecha_descuento = $fecha_descuento;
        $this->monto = $monto;
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
            ->view('layouts.views.mails.solicitud_anticipo',[
                'person' => $this->person,
                'entrega' => $this->fecha_entrega,
                'descuento' => $this->fecha_descuento,
                'monto' => $this->monto,
                'empresa' => $config_empresa
            ]);
    }
}
