<?php
// app/Models/Dieta.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dieta extends Model
{
    protected $table = 'dietas';
    public $timestamps = false;

    protected $fillable = ['titulo', 'descripcion', 'calorias', 'recomendaciones', 'fecha_registro'];
}
