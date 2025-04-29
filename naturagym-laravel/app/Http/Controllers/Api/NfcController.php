<?php
namespace App\Http\Controllers\Api;

use App\Models\Tarjeta;
use App\Models\Registro;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class NfcController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'uid'      => 'required|string',
            'punto_id' => 'required|integer|exists:puntos_acceso,id',
        ]);

        $ahora = Carbon::now('Europe/Madrid');
        $hora  = $ahora->format('H:i');
        $permitido = ($hora >= '08:00' && $hora < '14:00')
                  || ($hora >= '16:00' && $hora < '23:00');

        $tarjeta = Tarjeta::where('uid', $data['uid'])->first();

        if (!$tarjeta || !$tarjeta->usuario_id) {
            $permitido = false;
        }

        $reg = Registro::create([
            'usuario_id'      => $tarjeta?->usuario_id,  // null si no existe
            'tarjeta_id'      => $tarjeta?->id,
            'punto_acceso_id' => $data['punto_id'],
            'fecha'           => $ahora,
            'acceso'          => $permitido ? 'permitido' : 'denegado',
        ]);

        return response()->json([
            'status'      => $permitido ? 'OK' : 'DENIED',
            'registro_id' => $reg->id,
            'timestamp'   => $ahora->toDateTimeString(),
        ]);
    }
}
