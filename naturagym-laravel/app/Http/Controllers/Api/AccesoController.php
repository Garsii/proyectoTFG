<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\Tarjeta;
use App\Models\Registro;
use App\Models\Usuario;
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
            'uid'             => 'required|string',
            'punto_acceso_id' => 'required|integer|exists:puntos_acceso,id',
        ]);

        try {
            $ahora = Carbon::now('Europe/Madrid');
            $uid   = strtoupper(trim($data['uid']));

            // 1) Busca o crea tarjeta
            $tarjeta = Tarjeta::firstOrCreate(
                ['uid' => $uid],
                ['usuario_id' => null]
            );

            $esNueva = $tarjeta->wasRecentlyCreated;
            $usuario = $tarjeta->usuario;              // Usuario asociado (o null)
            $tieneUsuario = $usuario !== null;
            $estaRevocado = $tieneUsuario && $usuario->estado === 'revocado';

            Log::info("AccesoController@registrar — UID={$uid} usuario_id={$tarjeta->usuario_id} nueva={$esNueva} revocado={$estaRevocado}");

            // 2) Lógica de acceso: revocado > no registrado > permitido
            if ($estaRevocado) {
                $acceso = 'denegado';
                $motivo = 'revocado';
            } elseif (! $tieneUsuario) {
                $acceso = 'denegado';
                $motivo = 'no_registrada';
            } else {
                $acceso = 'permitido';
                $motivo = 'ok';
            }

            // 3) Crear registro
            $reg = Registro::create([
                'usuario_id'      => $tarjeta->usuario_id,
                'tarjeta_id'      => $tarjeta->id,
                'punto_acceso_id' => $data['punto_acceso_id'],
                'fecha'           => $ahora,
                'acceso'          => $acceso,
                'motivo'          => $motivo,
            ]);

            Log::info("Registro creado id={$reg->id} acceso={$reg->acceso} motivo={$motivo}");

            // 4) Envío de notificaciones
            $admin = 'admin@tfgmail.alvaroasir.com';

            if ($esNueva) {
                Mail::to($admin)->send(new TarjetaNoRegistradaMail($tarjeta, $reg));
            } elseif ($estaRevocado) {
                Mail::to($admin)->send(new AccesoDenegadoMail($tarjeta, $reg));
            } elseif (! $tieneUsuario) {
                Mail::to($admin)->send(new AccesoDenegadoMail($tarjeta, $reg));
            } else {
                Mail::to($admin)->send(new AccesoPermitidoMail($tarjeta, $reg));
            }

            // 5) Devolver JSON con código HTTP según acceso
            $httpCode = $acceso === 'permitido' ? 200 : 403;
            $status   = $acceso === 'permitido' ? 'OK' : 'DENIED';

            return response()->json([
                'status'      => $status,
                'registro_id' => $reg->id,
                'timestamp'   => $ahora->toDateTimeString(),
            ], $httpCode);

        } catch (\Throwable $e) {
            Log::error("Error en registrar(): {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error'  => 'Error interno del servidor',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }
}
