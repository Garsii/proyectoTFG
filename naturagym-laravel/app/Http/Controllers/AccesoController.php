<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Models\Tarjeta;
use App\Models\Registro;
use App\Models\PuntoAcceso;
use App\Notifications\AccesoDenegadoNotification;
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
        $uid   = strtoupper(trim($data['uid']));               // normalizamos
        $punto = PuntoAcceso::find($data['punto_id']);         // modelo de punto

        try {
            // 1) Buscar o crear Tarjeta
            $tarjeta = Tarjeta::firstOrCreate(
                ['uid' => $uid],
                ['usuario_id' => null]
            );
            Log::info("Tarjeta leída/creada: {$tarjeta->uid}");

            // 2) Determinar permiso
            $permitido = ! is_null($tarjeta->usuario_id);

            // 3) Crear Registro
            $registro = Registro::create([
                'usuario_id'      => $tarjeta->usuario_id,
                'tarjeta_id'      => $tarjeta->id,
                'punto_acceso_id' => $punto->id,
                'fecha'           => $ahora,
                'acceso'          => $permitido ? 'permitido' : 'denegado',
            ]);

            // 4) Si se deniega, notificar al admin
            if (! $permitido) {
                Notification::route('mail', 'admin@naturagym.com')
                    ->notify(new AccesoDenegadoNotification([
                        'uid'     => $uid,
                        'usuario' => $tarjeta->usuario,   // null‑safe, si no hay usuario mostrará —
                        'punto'   => $punto,
                        'fecha'   => $ahora,
                    ]));
                Log::warning("Acceso DENEGADO para UID {$uid} en punto {$punto->nombre}");
            }

            // 5) Responder al lector
            return response()->json([
                'status'      => $permitido ? 'OK' : 'DENIED',
                'registro_id' => $registro->id,
                'timestamp'   => $ahora->toDateTimeString(),
            ], $permitido ? 200 : 403);

        } catch (\Exception $e) {
            Log::error("Error al registrar acceso: " . $e->getMessage());
            return response()->json(['error' => 'Error interno'], 500);
        }
    }
}
