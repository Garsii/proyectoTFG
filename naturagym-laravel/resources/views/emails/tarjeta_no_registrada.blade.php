<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Tarjeta No Registrada</title></head>
<body>
  <h2>🔑 Tarjeta No Registrada</h2>
  <p>Se ha detectado una tarjeta no registrada intentando acceder:</p>
  <ul>
    <li><strong>UID:</strong> {{ $uid }}</li>
    <li><strong>Hora:</strong> {{ $fecha->format('Y-m-d H:i:s') }}</li>
  </ul>
  <p>Por favor, verifique y registre esta tarjeta si corresponde.</p>
</body>
</html>
