<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Administración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <ul class="space-y-2">
                    <li><a href="{{ route('usuarios.index') }}" class="text-blue-500 hover:underline">Gestionar Usuarios</a></li>
                    <li><a href="{{ route('tarjetas.index') }}" class="text-blue-500 hover:underline">Asignar Tarjetas NFC</a></li>
                    <li><a href="{{ route('registros.index') }}" class="text-blue-500 hover:underline">Ver Registros</a></li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
