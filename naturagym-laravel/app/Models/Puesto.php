<?php
// app/Models/Puesto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    protected $table = 'puestos';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    // Relación inversa con Usuario
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'puesto_id');
    }
}
