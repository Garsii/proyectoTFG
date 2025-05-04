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
        // 1) BÚSQUEDA de usuarios
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

        // 2) CARGA usuarios y UIDs libres
        $usuarios        = $query->with('tarjeta')->orderBy('id')->get();
        $uidsDisponibles = Tarjeta::whereNull('usuario_id')->pluck('uid');

        // 3) CÁLCULO de métricas (últimas 5 horas)
        date_default_timezone_set('Europe/Madrid');
        $now          = Carbon::now('Europe/Madrid');
        $horas        = [];
        $aforoPorHora = [];
        for ($i = 4; $i >= 0; $i--) {
            $inicio = $now->copy()->subHours($i)->startOfHour();
            $fin    = $inicio->copy()->addHour();

            $aforo = Registro::whereBetween('fecha', [$inicio, $fin])
                             ->distinct('usuario_id')
                             ->count('usuario_id');

            $horas[]        = $i === 0 ? 'Ahora' : "Hace {$i}h";
            $aforoPorHora[] = $aforo;
        }

        // 4) Total de clientes (rol = usuario) para escalar el eje Y si quieres
        $totalClientes = User::where('rol','usuario')->count();

        // 5) ¡UN SOLO RETURN! pasamos TODO a la vista
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
        $data = $r->validate([
            'usuarios'             => 'required|array',
            'usuarios.*.nombre'    => 'required|string|max:50',
            'usuarios.*.apellido'  => 'required|string|max:50',
            'usuarios.*.rol'       => 'required|in:usuario,admin',
            'usuarios.*.estado'    => 'required|in:activo,revocado',
            'usuarios.*.uid'       => 'nullable|string|exists:tarjetas,uid',
        ]);

        foreach($data['usuarios'] as $id=>$attrs){
            $u = User::find($id);
            $u->nombre   = $attrs['nombre'];
            $u->apellido = $attrs['apellido'];
            $u->rol      = $attrs['rol'];
            $u->estado   = $attrs['estado'];
            $u->save();

            // desvincular tarjeta previa
            Tarjeta::where('usuario_id',$u->id)
                   ->update(['usuario_id'=>null]);

            // asignar nueva
            if($attrs['uid']){
                Tarjeta::where('uid',$attrs['uid'])
                       ->update(['usuario_id'=>$u->id]);
            }
        }

        return back()->with('success','Cambios guardados correctamente.');
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
