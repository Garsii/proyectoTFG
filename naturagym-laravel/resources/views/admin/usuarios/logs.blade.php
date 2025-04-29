@extends('layouts.app')

@section('content')
<div class="p-4">
  <h1 class="text-xl font-bold mb-4">Logs de {{ $usuario->nombre }} {{ $usuario->apellido }}</h1>

  <a href="{{ route('admin.usuarios.index') }}" class="underline mb-4 inline-block">
    ← Volver al listado de usuarios
  </a>

  <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg p-4">
    @if($registros->isEmpty())
      <p class="text-gray-600">No hay registros de acceso para este usuario.</p>
    @else
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead>
          <tr>
            <th class="px-4 py-2">Fecha / Hora</th>
            <th class="px-4 py-2">UID Tarjeta</th>
            <th class="px-4 py-2">Acceso</th>
            <th class="px-4 py-2">Punto de Acceso</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
          @foreach($registros as $registro)
            <tr>
              <td class="px-4 py-2">{{ $registro->fecha->format('Y-m-d H:i:s') }}</td>
              <td class="px-4 py-2">{{ $registro->tarjeta->uid ?? '—' }}</td>
              <td class="px-4 py-2">{{ ucfirst($registro->acceso) }}</td>
              <td class="px-4 py-2">{{ $registro->puntoAcceso->nombre ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection
