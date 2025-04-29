<?php
// app/Models/ReservaClase.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservaClase extends Model
{
    protected $table = 'reservas_clases';
    public $timestamps = false; // usamos fecha_registro

    protected $fillable = ['usuario_id', 'clase_id', 'fecha_registro'];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function clase()
    {
        return $this->belongsTo(Clase::class, 'clase_id');
    }
}
