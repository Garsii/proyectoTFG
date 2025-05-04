<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registro extends Model
{
    protected $fillable = [
        'usuario_id', 'tarjeta_id', 'punto_acceso_id', 'fecha', 'acceso'
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function tarjeta(): BelongsTo
    {
        return $this->belongsTo(Tarjeta::class, 'tarjeta_id');
    }

    public function puntoAcceso(): BelongsTo
    {
        return $this->belongsTo(PuntoAcceso::class, 'punto_acceso_id');
    }
}
