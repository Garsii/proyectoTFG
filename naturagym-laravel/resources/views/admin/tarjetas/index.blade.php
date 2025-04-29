@extends('layouts.app')

@section('content')
<div class="p-4">
  <h1 class="text-xl font-bold mb-4">Tarjetas NFC</h1>
  <a href="{{ route('tarjetas.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded">Nueva Tarjeta</a>
  <table class="w-full mt-4 table-auto border-collapse">
    <thead>
      <tr class="bg-gray-200">
        <th class="border px-4 py-2">ID</th>
        <th class="border px-4 py-2">UID</th>
        <th class="border px-4 py-2">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($tarjetas as $t)
      <tr>
        <td class="border px-4 py-2">{{ $t->id }}</td>
        <td class="border px-4 py-2">{{ $t->uid }}</td>
        <td class="border px-4 py-2">
          <a href="{{ route('tarjetas.edit', $t) }}" class="text-blue-600">Editar</a>
          <form action="{{ route('tarjetas.destroy', $t) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 ml-2">Borrar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
