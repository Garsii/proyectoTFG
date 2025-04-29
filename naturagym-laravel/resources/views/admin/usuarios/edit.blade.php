@extends('layouts.app')

@section('content')
<div class="p-4">
  <h1 class="text-xl font-bold mb-4">Editar Usuario</h1>
  <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="block mb-1">Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" class="w-full border px-2 py-1" required>
        @error('nombre')<div class="text-red-600">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="block mb-1">Apellido</label>
        <input type="text" name="apellido" value="{{ old('apellido', $usuario->apellido) }}" class="w-full border px-2 py-1" required>
        @error('apellido')<div class="text-red-600">{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="mb-4">
      <label class="block mb-1">Email</label>
      <input type="email" name="email" value="{{ old('email', $usuario->email) }}" class="w-full border px-2 py-1" required>
      @error('email')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>
    <div class="mb-4">
      <label class="block mb-1">Rol</label>
      <select name="rol" class="w-full border px-2 py-1">
        <option value="usuario" {{ $usuario->rol=='usuario'? 'selected':'' }}>Usuario</option>
        <option value="admin" {{ $usuario->rol=='admin'? 'selected':'' }}>Admin</option>
      </select>
    </div>
    <div class="mb-4">
      <label class="block mb-1">Puesto</label>
      <select name="puesto_id" class="w-full border px-2 py-1">
        <option value="">-- Ninguno --</option>
        @foreach($puestos as $p)
          <option value="{{ $p->id }}" {{ $usuario->puesto_id==$p->id? 'selected':'' }}>{{ $p->nombre }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Actualizar</button>
  </form>
</div>
