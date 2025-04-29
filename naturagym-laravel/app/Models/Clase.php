<?php
// app/Models/Clase.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    protected $table = 'clases';
    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'hora_inicio', 'hora_fin', 'instructor', 'cupo', 'fecha', 'fecha_registro'];

    // Relación con reservas
    public function reservas()
    {
        return $this->hasMany(ReservaClase::class, 'clase_id');
    }
}
