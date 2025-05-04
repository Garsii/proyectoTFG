@extends('layouts.app')

@section('content')
<div class="p-4">
  <h1 class="text-xl font-bold mb-4">Usuarios</h1>

  @if(session('success'))
    <div class="mb-4 text-green-600">{{ session('success') }}</div>
  @endif

  {{-- Barra de búsqueda --}}
  <form method="GET" action="{{ route('admin.usuarios.index') }}" class="mb-4">
    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
           placeholder="Buscar por ID, nombre, correo..."
           class="w-full md:w-1/3 px-4 py-2 rounded border">
  </form>

  {{-- Formulario bulk-update --}}
  <form method="POST" action="{{ route('admin.usuarios.bulkUpdate') }}">
    @csrf
    @method('PATCH')

    <div class="overflow-x-auto bg-white shadow rounded-lg p-4 mb-4">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Nombre</th>
            <th class="px-4 py-2">Apellido</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Rol</th>
            <th class="px-4 py-2">Estado</th>
            <th class="px-4 py-2">UID</th>
            <th class="px-4 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($usuarios as $usuario)
            <tr>
              <td class="px-4 py-2 text-center">{{ $usuario->id }}</td>
              <td class="px-4 py-2">
                <input type="text" name="usuarios[{{ $usuario->id }}][nombre]" 
                       value="{{ $usuario->nombre }}" 
                       class="w-full rounded border px-2 py-1">
              </td>
              <td class="px-4 py-2">
                <input type="text" name="usuarios[{{ $usuario->id }}][apellido]" 
                       value="{{ $usuario->apellido }}" 
                       class="w-full rounded border px-2 py-1">
              </td>
              <td class="px-4 py-2">{{ $usuario->email }}</td>
              <td class="px-4 py-2">
                <select name="usuarios[{{ $usuario->id }}][rol]" class="rounded border px-7 py-1">
                  <option value="usuario" @selected($usuario->rol==='usuario')>Usuario</option>
                  <option value="admin"   @selected($usuario->rol==='admin')>Admin</option>
                </select>
              </td>
              <td class="px-4 py-2">
                <select name="usuarios[{{ $usuario->id }}][estado]" class="rounded border px-2 py-1">
                  <option value="activo"  @selected($usuario->estado==='activo')>Activo</option>
                  <option value="revocado" @selected($usuario->estado==='revocado')>Revocado</option>
                </select>
              </td>
              <td class="px-4 py-2">
                <select name="usuarios[{{ $usuario->id }}][uid]" class="rounded border px-7 py-1">
                  <option value="">Quitar UID</option>
                  @if($usuario->tarjeta)
                    <option value="{{ $usuario->tarjeta->uid }}" selected>
                      {{ $usuario->tarjeta->uid }}
                    </option>
                  @endif
                  @foreach($uidsDisponibles as $uid)
                    @if(optional($usuario->tarjeta)->uid !== $uid)
                      <option value="{{ $uid }}">{{ $uid }}</option>
                    @endif
                  @endforeach
                </select>
              </td>
              <td class="px-4 py-2 text-center space-x-2">
                {{-- Logs --}}
                <a href="{{ route('admin.usuarios.logs', $usuario->id) }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded inline-block">
                  Logs
                </a>
                {{-- Eliminar --}}
                <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" 
                      method="POST" class="inline-block" 
                      onsubmit="return confirm('¿Eliminar usuario #{{ $usuario->id }}?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                    class="bg-red-600 hover:bg-red-800 text-white px-3 py-1 rounded">
                    Eliminar
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Botón único “Guardar todos los cambios” --}}
    <div class="text-center mb-8">
      <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded">
        Guardar todos los cambios
      </button>
    </div>
  </form>

  {{-- Gráfico de métricas --}}
  <div class="max-w-xl mx-auto mb-6">
    <canvas id="aforoChart"></canvas>
  </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const horas        = @json($horas);
  const aforoData    = @json($aforoPorHora);
  const totalClients = @json($totalClientes);
  const maxAforo     = Math.max(...aforoData, totalClients);

  new Chart(document.getElementById('aforoChart'), {
    type: 'line',
    data: {
      labels: horas,
      datasets: [{
        label: 'Usuarios únicos',
        data: aforoData,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.15)',
        tension: 0.4,
        fill: true,
        pointRadius: 3,
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          max: maxAforo,
          ticks: { stepSize: 1 },
          title: { display: true, text: 'Usuarios únicos' }
        },
        x: {
          grid: { display: false },
          title: { display: true, text: 'Tiempo' }
        }
      }
    }
  });
</script>
@endsection
