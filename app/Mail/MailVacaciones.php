<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ConfiguracionEmpresa;

class MailVacaciones extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $status;
    public $desde;
    public $hasta;
    public $reincorporacion;
    public $message1;
    public $mailEmpleado;

    public function __construct($status,$desde,$hasta,$reincorporacion,$message1,$mailEmpleado)
    {
        $this->status          = $status;
        $this->desde           = $desde;
        $this->hasta           = $hasta;
        $this->reincorporacion = $reincorporacion;
        $this->message1         = $message1;
        $this->mailEmpleado    = $mailEmpleado;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $mailFrom = ConfiguracionEmpresa::select('correo_empresa')->first();
        return $this->from($mailFrom->correo_empresa)
            ->view('layouts.views.mails.vacaciones');
    }
}
