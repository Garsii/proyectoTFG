@component('mail::message')
# Acceso Denegado

Se ha producido un intento de acceso **denegado** en el punto **{{ $punto->nombre }}**.

@component('mail::panel')
- **UID**: {{ $uid }}  
- **Usuario**: {{ $usuario->nombre }} {{ $usuario->apellido }} (ID: {{ $usuario->id }})  
- **Email**: {{ $usuario->email }}  
- **Fecha / Hora**: {{ $fecha->format('Y-m-d H:i:s') }}  
@endcomponent

@component('mail::button', ['url' => url("/admin/usuarios/{$usuario->id}/logs")])
Ver Logs del Usuario
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
