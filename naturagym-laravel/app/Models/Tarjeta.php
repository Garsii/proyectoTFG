<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;  // ← opcional, si necesitas la relación inversa

class Tarjeta extends Model
{
    use HasFactory;

    protected $table = 'tarjetas';

    protected $fillable = ['uid', 'usuario_id'];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
