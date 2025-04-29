<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    // Permitir asignar estos campos vía create([...])
    protected $fillable = [
        'usuario_id',
        'tarjeta_id',
        'punto_acceso_id',
        'fecha',
	'acceso',
    ];

    // Si tu tabla no usa created_at / updated_at, descomenta:
    // public $timestamps = false;
}
