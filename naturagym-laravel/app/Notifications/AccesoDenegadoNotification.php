<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccesoDenegadoNotification extends Notification
{
    use Queueable;

    protected $uid;
    protected $usuario;
    protected $fecha;
    protected $punto;

    /**
     * @param  array  $datos  Clave: uid, usuario (model), punto (model), fecha (Carbon)
     */
    public function __construct(array $datos)
    {
        $this->uid     = $datos['uid'];
        $this->usuario = $datos['usuario'];
        $this->punto   = $datos['punto'];
        $this->fecha   = $datos['fecha'];
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject("Acceso DENEGADO: UID {$this->uid}")
                    ->greeting('¡Atención, administrador!')
                    ->line("Se ha denegado un intento de acceso en **{$this->punto->nombre}**.")
                    ->line("**Detalles del intento:**")
                    ->line("- **UID**: {$this->uid}")
                    ->line("- **Usuario**: {$this->usuario->nombre} {$this->usuario->apellido} (ID: {$this->usuario->id})")
                    ->line("- **Email**: {$this->usuario->email}")
                    ->line("- **Fecha / Hora**: {$this->fecha->format('Y-m-d H:i:s')}")
                    ->action('Ver registros', url("/admin/usuarios/{$this->usuario->id}/logs"))
                    ->salutation('Saludos,<br>Tu sistema NaturaGym');
    }
}
