<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'rol',
        'estado',
        'puesto_id',
        'subscription_expires_at',  // asegúrate de incluirlo si lo rellenas vía formulario/mass assignment
    ];

    // 👉 Aquí añadimos el casteo:
    protected $casts = [
        'subscription_expires_at' => 'datetime',
    ];

    // Relación con puesto
    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }

    // Relación con reservas
    public function reservas()
    {
        return $this->hasMany(ReservaClase::class, 'usuario_id');
    }
}
