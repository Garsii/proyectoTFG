<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarjeta;
use App\Models\Registro;
use Carbon\Carbon;

class AccesoController extends Controller
{
    /**
     * Registra un intento de acceso vía NFC.
     */
    public function registrar(Request $req)
    {
        $data = $req->validate([
            'uid'      => 'required|string',
            'punto_id' => 'required|integer|exists:puntos_acceso,id',
        ]);

        $ahora = Carbon::now('Europe/Madrid');

        // Comprueba si la tarjeta existe y está asignada
        $tarjeta = Tarjeta::where('uid', $data['uid'])->first();
        $permitido = $tarjeta && $tarjeta->usuario_id;

        try {
            $reg = Registro::create([
                'usuario_id'      => $tarjeta?->usuario_id,
                'tarjeta_id'      => $tarjeta?->id,
                'punto_acceso_id' => $data['punto_id'],
                'fecha'           => $ahora,
                'acceso'          => $permitido ? 'permitido' : 'denegado',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status'      => $permitido ? 'OK' : 'DENIED',
            'registro_id' => $reg->id,
            'timestamp'   => $ahora->toDateTimeString(),
        ], $permitido ? 200 : 403);
    }
}
