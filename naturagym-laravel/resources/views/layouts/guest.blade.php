<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative font-sans antialiased">

  {{-- 1) Fondo difuminado --}}
  <div class="absolute inset-0 bg-cover bg-center blur-md" 
       style="background-image: url('{{ asset('images/fondo.jpg') }}');">
  </div>

  {{-- 2) Capa de overlay para atenuar un poco --}}
  <div class="absolute inset-0 bg-black opacity-25"></div>

  {{-- 3) Contenedor principal elevado --}}
  <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

    {{-- Logo ampliado --}}
    <div>
      <a href="{{ route('dashboard') }}">
        <img src="{{ asset('images/logo.png') }}"
             alt="MiGimnasio"
             class="h-40 w-auto transform scale-110"/>
      </a>
    </div>

    {{-- Formulario con fondo semitransparente --}}
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white bg-opacity-80 dark:bg-gray-800 dark:bg-opacity-80 shadow-lg sm:rounded-lg backdrop-blur-sm">
      {{ $slot }}
    </div>
  </div>

</body>
</html>
