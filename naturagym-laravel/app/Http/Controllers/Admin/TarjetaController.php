<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tarjeta;
use Illuminate\Http\Request;

class TarjetaController extends Controller
{
	public function procesar(Request $request)
    {
        // Obtener el UID de la tarjeta desde la solicitud POST
        $uid = $request->input('uid');

        // Validar el UID (aquí puedes hacer lo que sea necesario, por ejemplo, buscar en la base de datos)
        $tarjeta = Tarjeta::where('uid', $uid)->first();

        if ($tarjeta) {
            // Si se encuentra la tarjeta, devolver una respuesta adecuada
            return response()->json(['status' => 'permitido', 'mensaje' => 'Acceso permitido']);
        } else {
            // Si no se encuentra la tarjeta
            return response()->json(['status' => 'denegado', 'mensaje' => 'Acceso denegado']);
        }
    }
}
