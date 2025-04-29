@extends('layouts.app')

@section('content')
<div class="p-4">
  <h1 class="text-xl font-bold mb-4">Editar Tarjeta NFC</h1>
  <form action="{{ route('tarjetas.update', $tarjeta) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-4">
      <label class="block mb-1">UID</label>
      <input type="text" name="uid" value="{{ old('uid', $tarjeta->uid) }}" class="w-full border px-2 py-1" required>
      @error('uid')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Actualizar</button>
  </form>
</div>
@endsection
