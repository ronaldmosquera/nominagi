<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ConfiguracionEmpresa;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailAnticipos extends Mailable
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
    public $dataAnticipo;

    public function __construct($estado,$message1,$mailEmpleado,$dataAnticipo)
    {
        $this->message1        = $message1;
        $this->mailEmpleado    = $mailEmpleado;
        $this->estado          = $estado;
        $this->dataAnticipo    = $dataAnticipo;
        $this->dataEmpresa     = ConfiguracionEmpresa::select('correo_empresa')->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->dataEmpresa->correo_empresa)
            ->view('layouts.views.mails.anticipos');
    }
}
