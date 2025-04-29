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

    // Corregimos el rango para las últimas 5 horas (incluyendo la actual)
    for ($i = 5; $i >= 1; $i--) {
        $inicio = $now->copy()->subHours($i)->startOfHour();
        $fin = $inicio->copy()->addHour();

        $aforo = Registro::whereBetween('fecha', [$inicio, $fin])
            ->distinct('usuario_id')  // Más eficiente que groupBy
            ->count('usuario_id');

        // Etiquetas tipo "Hace 5h", "Hace 4h"... "Ahora"
        $horas[] = ($i === 1) ? 'Ahora' : "Hace {$i}h";
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

        // Validar campos
        $data = $request->validate([
            'nombre'    => 'required|string|max:50',
            'apellido'  => 'required|string|max:50',
            'rol'       => 'required|in:usuario,admin',
            'estado'    => 'required|in:activo,revocado',
            'tarjeta_id'=> 'nullable|exists:tarjetas,id',
        ]);

        // Guardar cambios básicos
        $usuario->nombre   = $data['nombre'];
        $usuario->apellido = $data['apellido'];
        $usuario->rol      = $data['rol'];
        $usuario->estado   = $data['estado'];
        $usuario->save();

        // Desvincular cualquier tarjeta previa
        Tarjeta::where('usuario_id', $usuario->id)
               ->update(['usuario_id' => null]);

        // Vincular nueva tarjeta si se seleccionó
        if (! empty($data['tarjeta_id'])) {
            $tarjeta = Tarjeta::find($data['tarjeta_id']);
            $tarjeta->usuario_id = $usuario->id;
            $tarjeta->save();
        }

        return redirect()->route('admin.usuarios.index')
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
