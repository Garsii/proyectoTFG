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
	    ->subject('Acceso Denegado')
            ->line('El acceso con el UID ' . $this->uid . ' ha sido denegado.')
            ->line('Fecha y hora: ' . $this->fecha)
            ->line('Punto de acceso: ' . $this->punto->nombre)
            ->line('Gracias por usar nuestra aplicación!');
    }
}
