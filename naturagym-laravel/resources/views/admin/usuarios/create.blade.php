@extends('layouts.app')

@section('content')
<div class="p-4">
  <h1 class="text-xl font-bold mb-4">Nuevo Usuario</h1>
  <form action="{{ route('usuarios.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="block mb-1">Nombre</label>
        <input type="text" name="nombre" class="w-full border px-2 py-1" required>
        @error('nombre')<div class="text-red-600">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="block mb-1">Apellido</label>
        <input type="text" name="apellido" class="w-full border px-2 py-1" required>
        @error('apellido')<div class="text-red-600">{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="mb-4">
      <label class="block mb-1">Email</label>
      <input type="email" name="email" class="w-full border px-2 py-1" required>
      @error('email')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>
    <div class="mb-4">
      <label class="block mb-1">Password</label>
      <input type="password" name="password" class="w-full border px-2 py-1" required>
      @error('password')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>
    <div class="mb-4">
      <label class="block mb-1">Rol</label>
      <select name="rol" class="w-full border px-2 py-1">
        <option value="usuario">Usuario</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <div class="mb-4">
      <label class="block mb-1">Puesto</label>
      <select name="puesto_id" class="w-full border px-2 py-1">
        <option value="">-- Ninguno --</option>
        @foreach($puestos as $p)
          <option value="{{ $p->id }}">{{ $p->nombre }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Guardar</button>
  </form>
</div>
