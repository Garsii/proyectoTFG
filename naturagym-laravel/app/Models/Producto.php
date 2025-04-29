<?php
// app/Models/Producto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    public $timestamps = false;

    protected $fillable = ['nombre', 'categoria', 'descripcion', 'precio', 'stock', 'fecha_registro'];
}
