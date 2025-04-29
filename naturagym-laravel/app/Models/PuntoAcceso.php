<?php
// app/Models/PuntoAcceso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoAcceso extends Model
{
    protected $table = 'puntos_acceso';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    // Relación con registros
    public function registros()
    {
        return $this->hasMany(Registro::class, 'punto_id');
    }
}
