<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
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

    $ahora = Carbon::now('Europe/Madrid');
    $uid = strtoupper(trim($data['uid'])); // Normaliza el UID

    try {
        // Busca o CREA la tarjeta (con el UID)
        $tarjeta = Tarjeta::firstOrCreate(
            ['uid' => $uid], // Busca por UID
            ['usuario_id' => null] // Campos al crear
        );

        Log::info("Tarjeta procesada: " . $tarjeta->uid); // Verifica en logs

        $permitido = !is_null($tarjeta->usuario_id);

        // Crea el registro de acceso
        $registro = Registro::create([
            'usuario_id'      => $tarjeta->usuario_id,
            'tarjeta_id'      => $tarjeta->id,
            'punto_acceso_id' => $data['punto_id'],
            'fecha'           => $ahora,
            'acceso'          => $permitido ? 'permitido' : 'denegado',
        ]);

        return response()->json([
            'status'      => $permitido ? 'OK' : 'DENIED',
            'registro_id' => $registro->id,
            'timestamp'   => $ahora->toDateTimeString(),
        ], $permitido ? 200 : 403);

    } catch (\Exception $e) {
        Log::error("Error al registrar acceso: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
