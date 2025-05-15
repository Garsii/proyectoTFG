<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\Tarjeta;
use App\Models\Registro;
use Carbon\Carbon;
use App\Mail\AccesoPermitidoMail;
use App\Mail\AccesoDenegadoMail;
use App\Mail\TarjetaNoRegistradaMail;

class AccesoController extends Controller
{
    public function registrar(Request $req)
    {
        // Validamos uid y punto_id
        $data = $req->validate([
            'uid'      => 'required|string',
            'punto_acceso_id' => 'required|integer|exists:puntos_acceso,id',
        ]);

        try {
            $ahora = Carbon::now('Europe/Madrid');
            $uid   = strtoupper(trim($data['uid']));

            // Busca o crea tarjeta
            $tarjeta = Tarjeta::firstOrCreate(
                ['uid' => $uid],
                ['usuario_id' => null]
            );
            $tieneUsuario = $tarjeta->usuario_id !== null;
            $esNueva       = $tarjeta->wasRecentlyCreated;

            Log::info("AccesoController@registrar — UID={$uid} usuario_id={$tarjeta->usuario_id} nueva={$esNueva}");

            // Crea registro usando punto_acceso_id
            $reg = Registro::create([
                'usuario_id'      => $tarjeta->usuario_id,
                'tarjeta_id'      => $tarjeta->id,
                'punto_acceso_id' => $data['punto_acceso_id'],
                'fecha'           => $ahora,
                'acceso'          => $tieneUsuario ? 'permitido' : 'denegado',
            ]);

            Log::info("Registro creado id={$reg->id} acceso={$reg->acceso}");

            // Envío de notificaciones
            $admin = 'admin@tfgmail.alvaroasir.com';
            if ($esNueva) {
                Mail::to($admin)->send(new TarjetaNoRegistradaMail($tarjeta, $reg));
            } elseif ($tieneUsuario) {
                Mail::to($admin)->send(new AccesoPermitidoMail($tarjeta, $reg));
            } else {
                Mail::to($admin)->send(new AccesoDenegadoMail($tarjeta, $reg));
            }

            return response()->json([
                'status'      => $tieneUsuario ? 'OK' : 'DENIED',
                'registro_id' => $reg->id,
                'timestamp'   => $ahora->toDateTimeString(),
            ], $tieneUsuario ? 200 : 403);

        } catch (\Throwable $e) {
            Log::error("Error en registrar(): {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error'  => 'Error interno del servidor',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }
}
