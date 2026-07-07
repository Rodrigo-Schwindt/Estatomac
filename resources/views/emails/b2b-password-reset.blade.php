<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px;">
    <div style="max-width: 540px; margin: 0 auto; background: #fff; padding: 32px; border-radius: 8px;">
        <h1 style="color: #23378C; font-size: 20px; margin-top: 0;">Restablecer contraseña</h1>
        <p>Hola {{ $nombre }},</p>
        <p>Recibimos una solicitud para restablecer tu contraseña de la Zona Privada de Todotex.</p>
        <p style="text-align: center; margin: 28px 0;">
            <a href="{{ $resetUrl }}" style="background:#23378C;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:600;display:inline-block;">
                Restablecer contraseña
            </a>
        </p>
        <p style="font-size: 13px; color: #666;">Este enlace expira en 60 minutos. Si no solicitaste este cambio, podés ignorar este mensaje.</p>
        <p style="font-size: 12px; color: #999; word-break: break-all;">{{ $resetUrl }}</p>
    </div>
</body>
</html>
