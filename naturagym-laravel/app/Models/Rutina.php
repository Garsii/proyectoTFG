<?php
// app/Models/Rutina.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rutina extends Model
{
    protected $table = 'rutinas';
    public $timestamps = false;

    protected $fillable = ['titulo', 'descripcion', 'duracion', 'nivel', 'url_video', 'fecha_registro'];

    // Relación con rutinas de usuario
    public function usuarios()
    {
        return $this->hasMany(RutinaUsuario::class, 'rutina_id');
    }
}
