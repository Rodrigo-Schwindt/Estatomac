<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectText ?? 'Newsletter' }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; background: #fff; margin: auto; border-radius: 10px; padding: 20px;">
        <h2 style="color: #4F46E5; text-align: center;">Textil Nguilleu News 📨</h2>
        <p style="font-size: 16px; line-height: 1.5; color: #333;">
            {!! nl2br(e($bodyText)) !!}
        </p>
        <hr>
        <p style="font-size: 12px; color: #999; text-align: center;">
            Estás recibiendo este correo porque estás suscripto a nuestro newsletter.
        </p>
    </div>
</body>
</html>
