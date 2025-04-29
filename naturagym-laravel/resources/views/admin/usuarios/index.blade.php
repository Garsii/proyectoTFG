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
           class="w-full md:w-1/3 px-4 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
  </form>

  <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg p-4">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
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
      <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach($usuarios as $usuario)
          <tr>
            <form method="POST" action="{{ route('admin.usuarios.update', $usuario->id) }}">
              @csrf
              <td class="px-4 py-2 text-center">{{ $usuario->id }}</td>
              <td class="px-4 py-2">
                <input type="text" name="nombre" value="{{ $usuario->nombre }}" class="w-full rounded border px-2 py-1">
              </td>
              <td class="px-4 py-2">
                <input type="text" name="apellido" value="{{ $usuario->apellido }}" class="w-full rounded border px-2 py-1">
              </td>
              <td class="px-4 py-2">{{ $usuario->email }}</td>
              <td class="px-4 py-2">
                <select name="rol" class="rounded border px-7 py-1">
                  <option value="usuario" @selected($usuario->rol === 'usuario')>Usuario</option>
                  <option value="admin" @selected($usuario->rol === 'admin')>Admin</option>
                </select>
              </td>
              <td class="px-4 py-2">
                <select name="estado" class="rounded border px-2 py-1">
                  <option value="activo" @selected($usuario->estado === 'activo')>Activo</option>
                  <option value="revocado" @selected($usuario->estado === 'revocado')>Revocado</option>
                </select>
              </td>
              <td class="px-4 py-2">
                <select name="uid" class="rounded border px-7 py-1">
                  <option value="">Quitar UID</option>
                  @foreach($uidsDisponibles as $uid)
                    <option value="{{ $uid }}" @selected(optional($usuario->tarjeta)->uid === $uid)>{{ $uid }}</option>
                  @endforeach
                </select>
              </td>
              <td class="px-4 py-2 text-center space-x-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-1 rounded">
                  Guardar
                </button>
                <a href="{{ route('admin.usuarios.logs', $usuario->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-1 rounded">
                  Logs
                </a>
              </td>
            </form>
          </tr>
        @endforeach
      </tbody>
    </table>
<!-- En tu archivo resources/views/admin/usuarios/index.blade.php -->
<div style="max-width: 800px; margin: 20px auto;">
    <canvas id="aforoChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('aforoChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($horas),
            datasets: [{
                label: 'Accesos por hora',
                data: @json($aforoPorHora),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Usuarios únicos'
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>

  </div>
</div>
@endsection
