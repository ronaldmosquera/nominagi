<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ConfiguracionEmpresa;

class MailDescuentos extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mailEmpleado;
    public $cantidad;
    public $fechaDescuento;
    public $descripcion;
    public $conceptoDescuento;

    public function __construct($mailEmpleado,$cantidad,$fechaDescuento,$conceptoDescuento)
    {
        $this->mailEmpleado     = $mailEmpleado;
        $this->dataEmpresa      = ConfiguracionEmpresa::select('correo_empresa')->first();
        $this->cantidad         = $cantidad;
        $this->fechaDescuento    = $fechaDescuento;
        $this->conceptoDescuento = $conceptoDescuento;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->dataEmpresa->correo_empresa)
            ->view('layouts.views.mails.descuentos');
    }
}
