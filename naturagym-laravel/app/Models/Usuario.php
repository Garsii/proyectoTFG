<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';         // le decimos que use la tabla `usuarios`
    protected $primaryKey = 'id';
    public $timestamps = false;            // la tabla usa solo `fecha_registro`

    protected $fillable = [
        'nombre', 'apellido', 'email', 'password', 'rol', 'estado', 'puesto_id'
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
