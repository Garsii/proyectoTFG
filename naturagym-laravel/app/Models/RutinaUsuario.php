<?php
// app/Models/RutinaUsuario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutinaUsuario extends Model
{
    protected $table = 'rutinas_usuario';
    public $timestamps = false; // usamos fecha_modificacion

    protected $fillable = ['usuario_id', 'rutina_id', 'titulo', 'descripcion', 'duracion', 'nivel', 'url_video', 'fecha_modificacion'];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function rutina()
    {
        return $this->belongsTo(Rutina::class, 'rutina_id');
    }
}
