<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Tarjeta;
use App\Models\Registro;

class AccesoPermitidoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Tarjeta  $tarjeta;
    public Registro $registro;

    public function __construct(Tarjeta $tarjeta, Registro $registro)
    {
        $this->tarjeta  = $tarjeta;
        $this->registro = $registro->load('puntoAcceso'); 
        // cargamos la relación para poder usar $registro->puntoAcceso en la vista
    }

    public function build()
    {
        return $this
            ->subject('Acceso Permitido en NaturaGym')
            ->view('emails.acceso_permitido')
            ->with([
                'uid'       => $this->tarjeta->uid,
                'usuario'   => $this->tarjeta->usuario,   
                'fecha'     => $this->registro->fecha->format('Y-m-d H:i:s'),
                'punto'     => $this->registro->puntoAcceso->nombre,  // ← aquí
            ]);
    }
}
