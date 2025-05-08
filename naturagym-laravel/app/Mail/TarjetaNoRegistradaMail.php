<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Tarjeta;
use App\Models\Registro;

class TarjetaNoRegistradaMail extends Mailable
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
            ->subject('🔔 Tarjeta No Registrada en NaturaGym')
            ->view('emails.tarjeta_no_registrada')
            ->with([
                'uid'   => $this->tarjeta->uid,
                'fecha' => $this->registro->fecha,
            ]);
    }
}
