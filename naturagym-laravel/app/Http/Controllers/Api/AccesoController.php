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
        $data = $req->validate([
            'uid'      => 'required|string',
            'punto_id' => 'required|integer',  // lo recibes pero no lo guardas en BD
        ]);

        try {
            $ahora = Carbon::now('Europe/Madrid');
            $uid   = strtoupper(trim($data['uid']));

            // Busca o crea la tarjeta
            $tarjeta = Tarjeta::firstOrCreate(
                ['uid' => $uid],
                ['usuario_id' => null]
            );

            $tieneUsuario = $tarjeta->usuario_id !== null;
            $esNueva       = $tarjeta->wasRecentlyCreated;

            Log::info("AccesoController@registrar — UID={$uid} usuario_id=" . var_export($tarjeta->usuario_id, true) . " nueva={$esNueva}");

            // Crea el registro SIN punto_acceso_id (columna no existe)
            $reg = Registro::create([
            'usuario_id'      => $tarjeta->usuario_id,
	    'tarjeta_id'      => $tarjeta->id,
	    'punto_acceso_id' => $data['punto_id'],    // ← aquí
	    'fecha'           => $ahora,
	    'acceso'          => $tieneUsuario ? 'permitido' : 'denegado',
	    ]);

            Log::info("Registro creado id={$reg->id} acceso={$reg->acceso}");

            // Envío de mails
            $admin = 'admin@tfgmail.alvaroasir.com';

            if ($esNueva) {
                Log::info("Enviando TarjetaNoRegistradaMail");
                Mail::to($admin)->send(new TarjetaNoRegistradaMail($tarjeta, $reg));
            }
            elseif ($tieneUsuario) {
                Log::info("Enviando AccesoPermitidoMail");
                Mail::to($admin)->send(new AccesoPermitidoMail($tarjeta, $reg));
            }
            else {
                Log::info("Enviando AccesoDenegadoMail");
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
