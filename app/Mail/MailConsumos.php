<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ConfiguracionEmpresa;
use App\Models\ConfiguracionVariablesEmpresa;

class MailConsumos extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $message1;
    public $mailEmpleado;
    public $estado;
    public $dataConsumo;
    public $nombreEmpleado;
    public $iva;

    public function __construct($estado,$message1,$mailEmpleado,$nombreEmpleado,$dataConsumo,$iva)
    {
        $this->message1        = $message1;
        $this->mailEmpleado    = $mailEmpleado;
        $this->estado          = $estado;
        $this->dataConsumo     = $dataConsumo;
        $this->dataEmpresa     = ConfiguracionEmpresa::select('correo_empresa')->first();
        $this->nombreEmpleado  = $nombreEmpleado;
        $this->dataEmpresaV    = ConfiguracionVariablesEmpresa::select('iva')->first();
        $this->iva             = $iva;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->dataEmpresa->correo_empresa)
            ->view('layouts.views.mails.consumos');
    }
}
