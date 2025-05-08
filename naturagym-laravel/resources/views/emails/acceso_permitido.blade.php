<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Acceso Permitido</title></head>
<body>
  <h2>✅ Acceso Permitido</h2>
  <p>Se ha registrado un acceso permitido:</p>
  <ul>
    <li><strong>UID:</strong> {{ $uid }}</li>
    <li><strong>Usuario:</strong> {{ $usuario->nombre }} {{ $usuario->apellido }} (ID {{ $usuario->id }})</li>
    <li><strong>Hora:</strong> {{ $fecha->format('Y-m-d H:i:s') }}</li>
    <li><strong>Punto de acceso:</strong> {{ $punto }}</li>
  </ul>
</body>
</html>
