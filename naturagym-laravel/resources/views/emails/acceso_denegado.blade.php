<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Acceso Denegado</title></head>
<body>
  <h2>⛔ Acceso Denegado</h2>
  <p>Se ha intentado un acceso y fue denegado:</p>
  <ul>
    <li><strong>UID:</strong> {{ $uid }}</li>
    <li><strong>Hora:</strong> {{ $fecha->format('Y-m-d H:i:s') }}</li>
    <li><strong>Punto de Acceso:</strong> {{ $punto }}</li>
  </ul>
</body>
</html>
