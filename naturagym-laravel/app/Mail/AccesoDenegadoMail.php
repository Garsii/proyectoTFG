<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Tarjeta;
use App\Models\Registro;

class AccesoDenegadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Tarjeta $tarjeta;
    public Registro $registro;

    public function __construct(Tarjeta $tarjeta, Registro $registro)
    {
        $this->tarjeta  = $tarjeta;
        $this->registro = $registro;
    }

    public function build()
    {
        return $this
            ->subject('⚠️ Acceso Denegado en NaturaGym')
            ->view('emails.acceso_denegado')
            ->with([
                'uid'     => $this->tarjeta->uid,
                'fecha'   => $this->registro->fecha,
                'punto'   => $this->registro->puntoAcceso->nombre ?? '–',
            ]);
    }
}
