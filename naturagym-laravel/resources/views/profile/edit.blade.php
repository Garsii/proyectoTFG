@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-4">
  <h1 class="text-2xl mb-4">Editar Perfil</h1>

  @if(session('status') === 'profile-updated')
    <div class="mb-4 text-green-600">Perfil actualizado correctamente.</div>
  @endif

  <form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    <div class="mb-4">
      <x-input-label for="nombre" :value="__('Nombre')" />
      <x-text-input id="nombre" name="nombre" class="block mt-1 w-full"
                    :value="old('nombre', $user->nombre)" required autofocus />
      <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
    </div>

    <div class="mb-4">
      <x-input-label for="apellido" :value="__('Apellido')" />
      <x-text-input id="apellido" name="apellido" class="block mt-1 w-full"
                    :value="old('apellido', $user->apellido)" required />
      <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
    </div>

    <div class="mb-4">
      <x-input-label for="email" :value="__('Email')" />
      <x-text-input id="email" name="email" class="block mt-1 w-full"
                    type="email" :value="old('email', $user->email)" required />
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end mt-6">
      <x-primary-button>
        {{ __('Guardar cambios') }}
      </x-primary-button>
    </div>
  </form>

  <!-- Formulario para borrar cuenta -->
  <form method="POST" action="{{ route('profile.destroy') }}" class="mt-6">
    @csrf
    @method('DELETE')

    <div class="text-red-600">
      <button type="submit" class="underline text-sm">
        {{ __('Eliminar mi cuenta') }}
      </button>
    </div>
  </form>
</div>
@endsection
