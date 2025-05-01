<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;        // Modelo de usuarios
use App\Models\Tarjeta;     // Modelo de tarjetas NFC
use App\Models\Registro;    // Modelo de registros de acceso
use Carbon\Carbon;

class UsuarioController extends Controller
{
    /**
     * Mostrar listado de usuarios.
     */

public function index(Request $request)
{
    $query = User::query();

    if ($request->filled('busqueda')) {
        $busqueda = $request->input('busqueda');
        $query->where(function($q) use ($busqueda) {
            $q->where('id', $busqueda)
              ->orWhere('nombre', 'like', "%{$busqueda}%")
              ->orWhere('apellido', 'like', "%{$busqueda}%")
              ->orWhere('email', 'like', "%{$busqueda}%");
        });
    }

    $usuarios = $query->with('tarjeta')->orderBy('id')->get();
    $uidsDisponibles = Tarjeta::whereNull('usuario_id')->pluck('uid');

    // Calcular aforo por hora

    date_default_timezone_set('Europe/Madrid');
    $now = Carbon::now('Europe/Madrid');

    $horas = [];
    $aforoPorHora = [];

    for ($i = 4; $i >= 0; $i--) {
      $inicio = $now->copy()->subHours($i)->startOfHour();
      $fin    = $inicio->copy()->addHour();

      $aforo = Registro::whereBetween('fecha', [$inicio, $fin])
          ->distinct('usuario_id')
          ->count('usuario_id');

     $horas[] = ($i === 0) ? 'Ahora' : "Hace {$i}h";
     $aforoPorHora[] = $aforo;
     }


    return view('admin.usuarios.index', compact(
        'usuarios', 
        'uidsDisponibles',
        'horas',
        'aforoPorHora'
    ));
}
    /**
     * Mostrar formulario de edición (rol, estado, asignar tarjeta).
     */
    public function edit($id)
    {
        $usuario = User::findOrFail($id);

        // Tarjetas libres o ya asignada a este usuario
        $tarjetasDisponibles = Tarjeta::whereNull('usuario_id')
            ->orWhere('usuario_id', $usuario->id)
            ->get();

        return view('admin.usuarios.edit', compact('usuario', 'tarjetasDisponibles'));
    }

    /**
     * Procesar actualización de usuario.
     */
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // 1) Actualizar campos básicos
        $usuario->nombre   = $request->input('nombre');
        $usuario->apellido = $request->input('apellido');
        $usuario->rol      = $request->input('rol');
        $usuario->estado   = $request->input('estado');
        $usuario->save();

        // 2) Desvincular tarjeta anterior (si existe)
        Tarjeta::where('usuario_id', $usuario->id)
               ->update(['usuario_id' => null]);

        // 3) Asignar nueva tarjeta si se ha seleccionado UID
        $uid = $request->input('uid');
        if ($uid) {
            $tarjeta = Tarjeta::where('uid', $uid)->first();
            if ($tarjeta) {
                $tarjeta->usuario_id = $usuario->id;
                $tarjeta->save();
            }
        }

        return redirect()
               ->route('admin.usuarios.index')
               ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Mostrar logs de acceso del usuario.
     */
    public function logs($id)
    {
        $usuario = User::findOrFail($id);

        // Obtener registros con la tarjeta relacionada y punto de acceso
        $registros = Registro::where('usuario_id', $usuario->id)
                             ->with('tarjeta', 'puntoAcceso')
                             ->orderByDesc('fecha')
                             ->get();

        return view('admin.usuarios.logs', compact('usuario', 'registros'));
    }
}
