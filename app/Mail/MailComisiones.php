<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ConfiguracionEmpresa;

class MailComisiones extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mailEmpleado;
    public $cantidad;
    public $fechaComision;
    public $descripcion;
    public $conceptoComision;

    public function __construct($mailEmpleado,$cantidad,$fechaComision,$descripcion,$conceptoComision)
    {
        $this->mailEmpleado     = $mailEmpleado;
        $this->dataEmpresa      = ConfiguracionEmpresa::select('correo_empresa')->first();
        $this->cantidad         = $cantidad;
        $this->fechaComision    = $fechaComision;
        $this->descripcion      = $descripcion;
        $this->conceptoComision = $conceptoComision;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->dataEmpresa->correo_empresa)
            ->view('layouts.views.mails.comisiones');
    }
}
