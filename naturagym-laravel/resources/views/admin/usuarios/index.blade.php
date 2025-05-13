@extends('layouts.app')

@section('content')
<div class="p-4">
  <h1 class="text-xl font-bold mb-4">Usuarios</h1>

  @if(session('success'))
    <div class="mb-4 text-green-600">{{ session('success') }}</div>
  @endif

  {{-- Barra de búsqueda --}}
  <form method="GET" action="{{ route('admin.usuarios.index') }}" class="mb-4">
    <input type="text"
           name="busqueda"
           value="{{ request('busqueda') }}"
           placeholder="Buscar por ID, nombre, correo..."
           class="w-full md:w-1/3 px-4 py-2 rounded border">
  </form>

  {{-- Bulk-update form --}}
  <form id="bulk-form" method="POST" action="{{ route('admin.usuarios.bulkUpdate') }}">
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
            <th class="px-4 py-2">Renovar Suscripción<br></th>
            <th class="px-4 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($usuarios as $usuario)
            <tr>
              <td class="px-4 py-2 text-center">{{ $usuario->id }}</td>
              <td class="px-4 py-2">
                <input type="text"
                       name="usuarios[{{ $usuario->id }}][nombre]"
                       value="{{ $usuario->nombre }}"
                       form="bulk-form"
                       class="w-full rounded border px-2 py-1">
              </td>
              <td class="px-4 py-2">
                <input type="text"
                       name="usuarios[{{ $usuario->id }}][apellido]"
                       value="{{ $usuario->apellido }}"
                       form="bulk-form"
                       class="w-full rounded border px-2 py-1">
              </td>
              <td class="px-4 py-2">{{ $usuario->email }}</td>
              <td class="px-4 py-2">
                <select name="usuarios[{{ $usuario->id }}][rol]"
                        form="bulk-form"
                        class="rounded border px-7 py-1">
                  <option value="usuario" @selected($usuario->rol==='usuario')>Usuario</option>
                  <option value="admin"   @selected($usuario->rol==='admin')>Admin</option>
                </select>
              </td>
              <td class="px-4 py-2">
                <select name="usuarios[{{ $usuario->id }}][estado]"
                        form="bulk-form"
                        class="rounded border px-2 py-1">
                  <option value="activo"  @selected($usuario->estado==='activo')>Activo</option>
                  <option value="revocado" @selected($usuario->estado==='revocado')>Revocado</option>
                </select>
              </td>
              <td class="px-4 py-2">
                <select name="usuarios[{{ $usuario->id }}][uid]"
                        form="bulk-form"
                        class="rounded border px-7 py-1">
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

              {{-- Renovar Suscripción --}}
              <td class="px-4 py-2 text-center">
                <input type="checkbox"
                       name="usuarios[{{ $usuario->id }}][renovar]"
                       value="1"
                       form="bulk-form"
                       class="form-checkbox renew-checkbox" />
                <div class="text-xs mt-1">
                  @if($usuario->subscription_expires_at && \Carbon\Carbon::parse($usuario->subscription_expires_at)->isFuture())
                    Expira {{ \Carbon\Carbon::parse($usuario->subscription_expires_at)->format('d/m/Y') }}
                  @else
                    <span class="text-red-600">Sin suscripción</span>
                  @endif
                </div>
              </td>

              {{-- Acciones: el botón delete será type="button" --}}
              <td class="px-4 py-2 text-center space-x-2">
                <a href="{{ route('admin.usuarios.logs', $usuario->id) }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded">
                  Logs
                </a>
                <button type="button"
                        onclick="event.preventDefault(); document.getElementById('delete-{{ $usuario->id }}').submit();"
                        class="bg-red-600 hover:bg-red-800 text-white px-3 py-1 rounded">
                  Eliminar
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Botón único “Guardar todos los cambios” --}}
    <div class="text-center mb-8">
      <button type="submit"
              class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded">
        Guardar todos los cambios
      </button>
    </div>
  </form> {{-- ← bulk-form cerrado aquí --}}

  {{-- Formularios DELETE individuales, fuera del bulk-form --}}
  @foreach($usuarios as $usuario)
    <form id="delete-{{ $usuario->id }}"
          action="{{ route('admin.usuarios.destroy', $usuario->id) }}"
          method="POST"
          style="display: none;">
      @csrf
      @method('DELETE')
    </form>
  @endforeach
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Select/Deselect all “Renovar Suscripción”
    const selectAll = document.getElementById('select-all');
    const boxes    = document.querySelectorAll('.renew-checkbox');
    selectAll.addEventListener('change', () => {
      boxes.forEach(cb => cb.checked = selectAll.checked);
    });
  });
</script>
@endpush

@endsection

