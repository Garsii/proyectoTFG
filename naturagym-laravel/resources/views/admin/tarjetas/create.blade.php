@extends('layouts.app')

@section('content')
<div class="p-4">
  <h1 class="text-xl font-bold mb-4">Nueva Tarjeta NFC</h1>
  <form action="{{ route('tarjetas.store') }}" method="POST">
    @csrf
    <div class="mb-4">
      <label class="block mb-1">UID</label>
      <input type="text" name="uid" class="w-full border px-2 py-1" required>
      @error('uid')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Guardar</button>
  </form>
</div>
@endsection
