<?php
// app/Models/Aforo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aforo extends Model
{
    protected $table = 'aforo';
    public $timestamps = false;

    protected $fillable = ['fecha', 'hora', 'aforo', 'fecha_registro'];
}
