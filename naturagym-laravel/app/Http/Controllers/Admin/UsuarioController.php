<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Puesto;
use App\Models\Tarjeta;
use App\Models\Registro;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    /** 1) INDEX: revocar expirados, lista y métricas */
    public function index(Request $request)
    {
        $now = Carbon::now('Europe/Madrid');

        // Revocar masivamente
        User::where(function($q) use ($now) {
                $q->whereNull('subscription_expires_at')
                  ->orWhere('subscription_expires_at', '<', $now);
            })
            ->where('estado', '!=', 'revocado')
            ->update(['estado' => 'revocado']);

        // Búsqueda
        $query = User::query();
        if ($bus = $request->input('busqueda')) {
            $query->where(function($q) use ($bus) {
                $q->where('id', $bus)
                  ->orWhere('nombre', 'like', "%{$bus}%")
                  ->orWhere('apellido', 'like', "%{$bus}%")
                  ->orWhere('email', 'like', "%{$bus}%");
            });
        }

	$usuarios = $query->with('tarjeta')->orderBy('id')->get();

    // Obtén la lista de IDs de los usuarios que vas a mostrar
    $userIds = $usuarios->pluck('id')->toArray();

    // Ahora trae los UIDs que o bien están libres, o bien asignados a alguno de esos usuarios
    $uidsDisponibles = Tarjeta::where(function($q) use ($userIds) {
            $q->whereNull('usuario_id')
              ->orWhereIn('usuario_id', $userIds);
        })
        ->pluck('uid');

        // Métricas últimas 5 horas
        $horas = $aforoPorHora = [];
        for ($i = 4; $i >= 0; $i--) {
            $inicio = $now->copy()->subHours($i)->startOfHour();
            $fin    = $inicio->copy()->addHour();
            $aforo  = Registro::whereBetween('fecha', [$inicio, $fin])
                              ->distinct('usuario_id')
                              ->count('usuario_id');
            $horas[]        = $i === 0 ? 'Ahora' : "Hace {$i}h";
            $aforoPorHora[] = $aforo;
        }
        $totalClientes = User::where('rol', 'usuario')->count();

        return view('admin.usuarios.index', compact(
            'usuarios',
            'uidsDisponibles',
            'horas',
            'aforoPorHora',
            'totalClientes'
        ));
    }

    /** 2) CREATE */
    public function create()
    {
        return view('admin.usuarios.create', [
            'roles'   => ['usuario','admin'],
            'estados'=> ['activo','revocado'],
            'puestos'=> Puesto::all(),
        ]);
    }

    /** 3) STORE */
    public function store(Request $r)
    {
        // 1) Validación
        $data = $r->validate([
            'nombre'          => 'required|string|max:50',
            'apellido'        => 'required|string|max:50',
            'email'           => 'required|email|unique:usuarios,email',
            'rol'             => 'required|in:usuario,admin',
            'estado'          => 'required|in:activo,revocado',
            'puesto_id'       => 'nullable|exists:puestos,id',
            'password_input'  => 'nullable|string|min:8',
        ]);

        // 2) Generar o usar la proporcionada
        if (!empty($data['password_input'])) {
            $plainPassword = $data['password_input'];
        } else {
            $plainPassword = Str::random(10);
        }

        // 3) Hashear para guardar
        $data['password'] = bcrypt($plainPassword);

        // 4) Fecha de expiración por defecto (p. ej., 30 días)
        $data['subscription_expires_at'] = Carbon::now()->addDays(30);

        // 5) Crear usuario
        $user = User::create($data);

        // 6) Redirigir con flash message que incluye la contraseña
        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', "Usuario creado. Contraseña: <strong>{$plainPassword}</strong>");
    }

    /** 4) EDIT */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $tarjetasDisponibles = Tarjeta::whereNull('usuario_id')
            ->orWhere('usuario_id', $id)
            ->pluck('uid');

        return view('admin.usuarios.edit', compact('user','tarjetasDisponibles'));
    }

    /** 5) UPDATE */
    public function update(Request $r, $id)
    {
        $data = $r->validate([
            'nombre'   => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'rol'      => 'required|in:usuario,admin',
            'estado'   => 'required|in:activo,revocado',
            'uid'      => 'nullable|string|exists:tarjetas,uid',
        ]);

        $u = User::findOrFail($id);
        $u->update($data);

        Tarjeta::where('usuario_id',$id)->update(['usuario_id'=>null]);
        if (!empty($data['uid'])) {
            Tarjeta::where('uid',$data['uid'])
                   ->update(['usuario_id'=>$id]);
        }

        return redirect()->route('admin.usuarios.index')
                         ->with('success','Usuario actualizado.');
    }

    /** 6) BULK UPDATE + desplegable renovar */
    public function bulkUpdate(Request $r)
    {
        $data = $r->validate([
            'usuarios'               => 'required|array',
            'usuarios.*.nombre'      => 'required|string|max:50',
            'usuarios.*.apellido'    => 'required|string|max:50',
            'usuarios.*.rol'         => 'required|in:usuario,admin',
            'usuarios.*.estado'      => 'required|in:activo,revocado',
            'usuarios.*.uid'         => 'nullable|string|exists:tarjetas,uid',
            'usuarios.*.renovar'     => 'nullable|in:30,90,365',
        ]);

        foreach ($data['usuarios'] as $id => $attrs) {
            $u = User::findOrFail($id);
            $u->update([
                'nombre'   => $attrs['nombre'],
                'apellido' => $attrs['apellido'],
                'rol'      => $attrs['rol'],
                'estado'   => $attrs['estado'],
            ]);

            Tarjeta::where('usuario_id',$id)->update(['usuario_id'=>null]);
            if (!empty($attrs['uid'])) {
                Tarjeta::where('uid',$attrs['uid'])
                       ->update(['usuario_id'=>$id]);
            }

            if (!empty($attrs['renovar'])) {
                $ahora   = Carbon::now('Europe/Madrid');
                $vigente = $u->subscription_expires_at
                           ? Carbon::parse($u->subscription_expires_at)
                           : $ahora;
                $base = $vigente->isFuture() ? $vigente : $ahora;
                $u->subscription_expires_at = $base->addDays((int)$attrs['renovar']);
                $u->save();
            }
        }

        return back()->with('success','Cambios guardados y suscripciones actualizadas.');
    }

    /** 7) RENOVAR individual */
    public function renovarSuscripcion($id)
    {
        $u = User::findOrFail($id);
        $base = ($u->subscription_expires_at && Carbon::parse($u->subscription_expires_at)->isFuture())
                ? Carbon::parse($u->subscription_expires_at)
                : Carbon::now('Europe/Madrid');
        $u->subscription_expires_at = $base->addDays(30);
        $u->save();

        return back()->with('success','Suscripción renovada.');
    }

    /** 8) LOGS */
    public function logs($id)
    {
        $u = User::findOrFail($id);
        $regs = Registro::where('usuario_id',$id)
                        ->with('tarjeta','puntoAcceso')
                        ->orderByDesc('fecha')
                        ->get();

        return view('admin.usuarios.logs', compact('u','regs'));
    }

    /** 9) DESTROY */
    public function destroy($id)
    {
        Tarjeta::where('usuario_id',$id)->update(['usuario_id'=>null]);
        Registro::where('usuario_id',$id)->delete();
        User::findOrFail($id)->delete();

        return back()->with('success','Usuario eliminado.');
    }
}
