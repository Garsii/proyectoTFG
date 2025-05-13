<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tarjeta;
use App\Models\Registro;
use Carbon\Carbon;

class UsuarioController extends Controller
{
    public function index(Request $request)
{
    // 1) "Ahora" en Madrid
    $now = Carbon::now('Europe/Madrid');

    // 2) REVOCAR masivamente a los sin suscripción o expirados
    User::where(function($q) use ($now) {
            $q->whereNull('subscription_expires_at')
              ->orWhere('subscription_expires_at', '<', $now);
        })
        ->where('estado', '!=', 'revocado')
        ->update(['estado' => 'revocado']);

    // 3) BÚSQUEDA de usuarios
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

    // 4) CARGAR usuarios ya con el estado corregido
    $usuarios        = $query->with('tarjeta')->orderBy('id')->get();
    $uidsDisponibles = Tarjeta::whereNull('usuario_id')->pluck('uid');

    // 5) Métricas (últimas 5 horas)
    $horas        = [];
    $aforoPorHora = [];
    for ($i = 4; $i >= 0; $i--) {
        $inicio = $now->copy()->subHours($i)->startOfHour();
        $fin    = $inicio->copy()->addHour();
        $aforo  = Registro::whereBetween('fecha', [$inicio, $fin])
                          ->distinct('usuario_id')
                          ->count('usuario_id');
        $horas[]        = $i === 0 ? 'Ahora' : "Hace {$i}h";
        $aforoPorHora[] = $aforo;
    }

    // 6) Total clientes (rol = usuario)
    $totalClientes = User::where('rol', 'usuario')->count();

    // 7) Un solo return
    return view('admin.usuarios.index', compact(
        'usuarios',
        'uidsDisponibles',
        'horas',
        'aforoPorHora',
        'totalClientes'
    ));
}


    /** Bulk-update desde el formulario único */
    public function bulkUpdate(Request $r)
    {
        // 1) Validamos, incluyendo el flag "renovar"
        $data = $r->validate([
            'usuarios'               => 'required|array',
            'usuarios.*.nombre'      => 'required|string|max:50',
            'usuarios.*.apellido'    => 'required|string|max:50',
            'usuarios.*.rol'         => 'required|in:usuario,admin',
            'usuarios.*.estado'      => 'required|in:activo,revocado',
            'usuarios.*.uid'         => 'nullable|string|exists:tarjetas,uid',
            'usuarios.*.renovar'     => 'sometimes|boolean',
        ]);

        foreach ($data['usuarios'] as $id => $attrs) {
            $u = User::findOrFail($id);

            // 2) Actualizamos los campos básicos
            $u->nombre   = $attrs['nombre'];
            $u->apellido = $attrs['apellido'];
            $u->rol      = $attrs['rol'];
            $u->estado   = $attrs['estado'];
            $u->save();

            // 3) Tarjeta: desvinculamos y volvemos a vincular si viene UID
            Tarjeta::where('usuario_id', $u->id)
                   ->update(['usuario_id' => null]);

            if (!empty($attrs['uid'])) {
                Tarjeta::where('uid', $attrs['uid'])
                       ->update(['usuario_id' => $u->id]);
            }

            // 4) Procesamos la casilla "renovar"
            if (!empty($attrs['renovar'])) {
                $ahora   = Carbon::now('Europe/Madrid');
                $vigente = $u->subscription_expires_at
                           ? Carbon::parse($u->subscription_expires_at)
                           : $ahora;

                // Si ya venció, renovamos desde hoy; si no, desde la fecha actual de expiración
                $base = $vigente->isFuture() ? $vigente : $ahora;
                $u->subscription_expires_at = $base->addDays(30);
                $u->save();
            }
        }

        return back()->with('success', 'Cambios guardados y suscripciones renovadas correctamente.');
    }
    /** Edit individual (optional) */
    public function edit($id)
    {
        $u = User::findOrFail($id);
        $tarjetasDisponibles = Tarjeta::whereNull('usuario_id')
            ->orWhere('usuario_id',$u->id)->get();

        return view('admin.usuarios.edit', compact('u','tarjetasDisponibles'));
    }

    /** Update individual (optional) */
    public function update(Request $r,$id)
    {
        $u = User::findOrFail($id);
        $u->nombre   = $r->nombre;
        $u->apellido = $r->apellido;
        $u->rol      = $r->rol;
        $u->estado   = $r->estado;
        $u->save();

        Tarjeta::where('usuario_id',$u->id)->update(['usuario_id'=>null]);
        if($r->uid){
            Tarjeta::where('uid',$r->uid)->update(['usuario_id'=>$u->id]);
        }

        return redirect()->route('admin.usuarios.index')
                         ->with('success','Usuario actualizado.');
    }

    public function renovarSuscripcion($id)
{
    // Encuentra al usuario
    $usuario = User::find($id);

    // Si el usuario no existe, manejar el error
    if (!$usuario) {
        return redirect()->route('admin.usuarios.index')->with('error', 'Usuario no encontrado');
    }

    // Renovar la suscripción +30 días
    $usuario->subscription_expires_at = now()->addDays(30);  // Agregar 30 días a la fecha actual
    $usuario->save();

    // Redirigir de vuelta a la vista de usuarios con un mensaje de éxito
    return redirect()->route('admin.usuarios.index')->with('success', 'Suscripción renovada con éxito');
}


    /** Logs de acceso de un usuario */
    public function logs($id)
    {
        $u = User::findOrFail($id);
        $regs = Registro::where('usuario_id',$u->id)
                        ->with('tarjeta','puntoAcceso')
                        ->orderByDesc('fecha')->get();
        return view('admin.usuarios.logs',compact('u','regs'));
    }

    /** Eliminar usuario */
    public function destroy($id)
    {
        Tarjeta::where('usuario_id',$id)->update(['usuario_id'=>null]);
        Registro::where('usuario_id',$id)->delete();
        User::findOrFail($id)->delete();

        return redirect()->route('admin.usuarios.index')
                         ->with('success','Usuario eliminado.');
    }
}
