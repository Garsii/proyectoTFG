@extends('layouts.app')

@section('content')
<div class="p-4 bg-white text-gray-800 text-sm max-w-md mx-auto">
  <h1 class="text-lg font-bold mb-4">Nuevo Usuario</h1>

  @if($errors->any())
    <div class="mb-4 text-red-600 text-xs">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4">
    @csrf

    <div>
      <label for="nombre" class="block text-2xs font-medium mb-1">Nombre</label>
      <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
             class="w-full px-3 py-2 border rounded text-xs" required>
    </div>

    <div>
      <label for="apellido" class="block text-2xs font-medium mb-1">Apellido</label>
      <input type="text" name="apellido" id="apellido" value="{{ old('apellido') }}"
             class="w-full px-3 py-2 border rounded text-xs" required>
    </div>

    <div>
      <label for="email" class="block text-2xs font-medium mb-1">Email</label>
      <input type="email" name="email" id="email" value="{{ old('email') }}"
             class="w-full px-3 py-2 border rounded text-xs" required>
    </div>

    <div>
      <label for="password_input" class="block text-2xs font-medium mb-1">Contraseña (opcional)</label>
      <input
        type="text"
        name="password_input"
        id="password_input"
        class="w-full px-3 py-2 border rounded text-xs"
        placeholder="Déjalo en blanco para generar una aleatoria"
        value="{{ old('password_input') }}"
      >
      <p class="text-2xs text-gray-500 mt-1">
        Si lo dejas vacío, se generará automáticamente.
      </p>
      @error('password_input')
        <p class="text-red-600 text-2xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div>
      <label for="rol" class="block text-2xs font-medium mb-1">Rol</label>
      <select name="rol" id="rol" class="w-full px-3 py-2 border rounded text-xs">
        @foreach($roles as $rol)
          <option value="{{ $rol }}" @selected(old('rol') == $rol)>
            {{ ucfirst($rol) }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label for="estado" class="block text-2xs font-medium mb-1">Estado</label>
      <select name="estado" id="estado" class="w-full px-3 py-2 border rounded text-xs">
        <option value="activo"   @selected(old('estado')=='activo')>Activo</option>
        <option value="revocado" @selected(old('estado')=='revocado')>Revocado</option>
      </select>
    </div>

    <div>
      <label for="puesto_id" class="block text-2xs font-medium mb-1">Puesto</label>
      <select name="puesto_id" id="puesto_id" class="w-full px-3 py-2 border rounded text-xs">
        <option value="">-- Ninguno --</option>
        @foreach($puestos as $p)
          <option value="{{ $p->id }}" @selected(old('puesto_id') == $p->id)>
            {{ $p->nombre }}
          </option>
        @endforeach
      </select>
      @error('puesto_id')
        <p class="text-red-600 text-2xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="flex justify-end space-x-2 mt-6">
      <a href="{{ route('admin.usuarios.index') }}"
         class="text-gray-600 text-2xs hover:underline">Cancelar</a>
      <button type="submit"
              class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded text-xs">
        Guardar
      </button>
    </div>
  </form>
</div>
@endsection
