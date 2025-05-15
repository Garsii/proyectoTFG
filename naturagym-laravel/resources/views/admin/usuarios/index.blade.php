@extends('layouts.app')

@section('content')
<style>
  /* Oculta la flechita de los <select> cuando tienen clase appearance-none */
  select.appearance-none {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: none !important;
  }
</style>

<div class="p-4 bg-white text-gray-800 text-sm">
  <div class="flex justify-between items-center mb-4">
    <h1 class="font-bold text-lg">Usuarios</h1>
    <a href="{{ route('admin.usuarios.create') }}"
       class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded text-xs">
      Crear usuario
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 text-green-600 text-xs">{{ session('success') }}</div>
  @endif

  {{-- Búsqueda --}}
  <form method="GET" action="{{ route('admin.usuarios.index') }}" class="mb-4">
    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
           placeholder="Buscar por ID, nombre, correo..."
           class="w-full md:w-1/3 px-4 py-2 rounded border text-xs bg-white"/>
  </form>

  {{-- Bulk-update --}}
  <form id="bulk-form" method="POST" action="{{ route('admin.usuarios.bulkUpdate') }}">
    @csrf
    @method('PATCH')

    <table class="w-full border-collapse bg-white text-gray-800 text-xs">
      <thead>
        <tr class="bg-gray-100">
          <th class="px-2 py-1">ID</th>
          <th class="px-2 py-1">Nombre</th>
          <th class="px-2 py-1">Apellido</th>
          <th class="px-2 py-1">Email</th>
          <th class="px-2 py-1">Rol</th>
          <th class="px-2 py-1">Estado</th>
          <th class="px-2 py-1">UID</th>
          <th class="px-2 py-1">Renovar (días)</th>
          <th class="px-2 py-1">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($usuarios as $usuario)
          @php
            // Toma el valor enviado en la validación o, si no, el UID de la BD
            $selectedUid = old('usuarios.' . $usuario->id . '.uid', optional($usuario->tarjeta)->uid);
          @endphp
          <tr class="border-t hover:bg-gray-50">
            <td class="px-2 py-1 text-center">{{ $usuario->id }}</td>
            <td class="px-2 py-1">
              <input type="text"
                     name="usuarios[{{ $usuario->id }}][nombre]"
                     form="bulk-form"
                     value="{{ old('usuarios.' . $usuario->id . '.nombre', $usuario->nombre) }}"
                     class="w-full rounded border px-2 py-1 text-xs bg-white"/>
            </td>
            <td class="px-2 py-1">
              <input type="text"
                     name="usuarios[{{ $usuario->id }}][apellido]"
                     form="bulk-form"
                     value="{{ old('usuarios.' . $usuario->id . '.apellido', $usuario->apellido) }}"
                     class="w-full rounded border px-2 py-1 text-xs bg-white"/>
            </td>
            <td class="px-2 py-1">{{ $usuario->email }}</td>
            <td class="px-2 py-1">
              <select name="usuarios[{{ $usuario->id }}][rol]" form="bulk-form"
                      class="rounded border px-2 py-1 text-xs bg-white">
                <option value="usuario"
                        @selected(old('usuarios.' . $usuario->id . '.rol', $usuario->rol) === 'usuario')>
                  Usuario
                </option>
                <option value="admin"
                        @selected(old('usuarios.' . $usuario->id . '.rol', $usuario->rol) === 'admin')>
                  Admin
                </option>
              </select>
            </td>
            <td class="px-2 py-1">
              <select name="usuarios[{{ $usuario->id }}][estado]" form="bulk-form"
                      class="rounded border px-2 py-1 text-xs bg-white">
                <option value="activo"
                        @selected(old('usuarios.' . $usuario->id . '.estado', $usuario->estado) === 'activo')>
                  Activo
                </option>
                <option value="revocado"
                        @selected(old('usuarios.' . $usuario->id . '.estado', $usuario->estado) === 'revocado')>
                  Revocado
                </option>
              </select>
            </td>
            <td class="px-2 py-1">
              <select name="usuarios[{{ $usuario->id }}][uid]" form="bulk-form"
                      class="rounded border px-2 py-1 text-xs bg-white {{ $selectedUid ? 'appearance-none pr-4' : '' }}">
                <option value="" @selected(!$selectedUid)>-- Seleccionar UID --</option>
                @foreach($uidsDisponibles as $uid)
                  <option value="{{ $uid }}"
                          @selected((string) $selectedUid === (string) $uid)>
                    {{ $uid }}
                  </option>
                @endforeach
              </select>
            </td>
            <td class="px-2 py-1 text-center">
              <select name="usuarios[{{ $usuario->id }}][renovar]" form="bulk-form"
                      class="rounded border px-2 py-1 text-xs bg-white">
                <option value="" @selected(! old('usuarios.' . $usuario->id . '.renovar'))>--</option>
                <option value="30" @selected(old('usuarios.' . $usuario->id . '.renovar') == '30')>30</option>
                <option value="90" @selected(old('usuarios.' . $usuario->id . '.renovar') == '90')>90</option>
                <option value="365" @selected(old('usuarios.' . $usuario->id . '.renovar') == '365')>365</option>
              </select>
              <div class="text-2xs mt-1">
                @if($usuario->subscription_expires_at)
                  {{ \Carbon\Carbon::parse($usuario->subscription_expires_at)->format('d/m/Y') }}
                @else
                  <span class="text-red-600">Sin suscripción</span>
                @endif
              </div>
            </td>
            <td class="px-2 py-1 text-center space-x-1">
              <a href="{{ route('admin.usuarios.logs', $usuario->id) }}"
                 class="bg-gray-500 hover:bg-gray-700 text-white px-2 py-1 rounded text-2xs">
                Logs
              </a>
              <button type="button"
                      onclick="document.getElementById('delete-{{ $usuario->id }}').submit();"
                      class="bg-red-600 hover:bg-red-800 text-white px-2 py-1 rounded text-2xs">
                Eliminar
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="text-center my-4">
      <button type="submit"
              class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs">
        Guardar cambios
      </button>
    </div>
  </form>

  {{-- Formularios DELETE ocultos --}}
  @foreach($usuarios as $usuario)
    <form id="delete-{{ $usuario->id }}"
          action="{{ route('admin.usuarios.destroy', $usuario->id) }}"
          method="POST" style="display: none;">
      @csrf
      @method('DELETE')
    </form>
  @endforeach

  {{-- Gráfico métricas --}}
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
