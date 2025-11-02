<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recuperación de Contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px;">
    <div style="background: white; padding: 20px; border-radius: 10px;">
        <h2 style="color: #f97316;">Hola {{ $nombre }} 👋</h2>
        <p>Has solicitado restablecer tu contraseña. Usa el siguiente código para continuar:</p>
        <h1 style="text-align:center; letter-spacing: 5px; color:#f97316;">{{ $codigo }}</h1>
        <p>Este código expira en 10 minutos.</p>
        <p>Si no solicitaste este cambio, ignora este mensaje.</p>
        <br>
        <p style="color:#6c757d;">© {{ date('Y') }} Taquillería del Sol | Todos los derechos reservados.</p>
    </div>
</body>
</html>
