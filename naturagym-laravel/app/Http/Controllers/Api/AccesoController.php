<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarjeta;
use App\Models\Registro;
use Carbon\Carbon;

class AccesoController extends Controller 
{
    public function registrar(Request $req)
    {
        $data = $req->validate([
            'uid'      => 'required|string',
            'punto_id' => 'required|integer|exists:puntos_acceso,id',
        ]);

        $ahora   = Carbon::now('Europe/Madrid');
        // Si la tarjeta no existe, la creamos. Si existe, la recuperamos.
        $tarjeta = Tarjeta::firstOrCreate(['uid' => $data['uid']]);

        // Permitido sólo si ya tenía usuario asignado
        $permitido = $tarjeta->usuario_id !== null;

        // Creamos el registro (sin punto_acceso_id, pues no existe la columna)
        $reg = Registro::create([
            'usuario_id' => $tarjeta->usuario_id,
            'tarjeta_id' => $tarjeta->id,
            'fecha'      => $ahora,
            'acceso'     => $permitido ? 'permitido' : 'denegado',
        ]);

        // Devolvemos JSON y código HTTP según permiso
        return response()->json(
            [
                'status'      => $permitido ? 'OK' : 'DENIED',
                'registro_id' => $reg->id,
                'timestamp'   => $ahora->toDateTimeString(),
            ],
            $permitido ? 200 : 403
        );
    }
}
