<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Tarjeta;  // ← ¡Asegúrate de importar el modelo!

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'rol',
        'estado',
        'puesto_id',
        'subscription_expires_at',  // si lo rellenas via mass-assignment
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ⚡ Aquí el cast:
    protected $casts = [
        'subscription_expires_at' => 'datetime',
    ];

    public $timestamps = true;

    public function tarjeta()
    {
        return $this->hasOne(Tarjeta::class, 'usuario_id');
    }
}
