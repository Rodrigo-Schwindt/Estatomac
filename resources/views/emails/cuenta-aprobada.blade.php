<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cuenta Aprobada</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #E4002B; color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { background: #f9f9f9; padding: 30px 20px; }
        .section { background: white; padding: 25px; margin-bottom: 20px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .welcome-message { font-size: 18px; color: #333; margin-bottom: 20px; line-height: 1.8; }
        .success-box { background: #d4edda; border-left: 4px solid #28a745; padding: 20px; margin: 20px 0; color: #155724; }
        .success-box strong { font-size: 20px; display: block; margin-bottom: 10px; }
        .info-list { margin: 20px 0; padding-left: 20px; }
        .info-list li { margin-bottom: 10px; }
        .btn-container { text-align: center; margin: 30px 0; }
        .btn { display: inline-block; background: #E4002B; color: white; padding: 15px 40px; text-decoration: none; border-radius: 4px; font-size: 16px; font-weight: bold; }
        .btn:hover { background: #B30034; }
        .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 14px; }
        .credentials-box { background: #f8f9fa; border: 2px solid #E4002B; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .credentials-box .field { margin: 10px 0; font-size: 16px; }
        .credentials-box strong { color: #E4002B; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 ¡Bienvenido a Todotex!</h1>
        </div>
        
        <div class="content">
            <div class="success-box">
                <strong>✅ Tu cuenta ha sido aprobada exitosamente</strong>
                Ya puedes acceder a nuestra plataforma y comenzar a realizar tus pedidos.
            </div>

            <div class="section">
                <p class="welcome-message">
                    Hola <strong>{{ $data['nombre'] }}</strong>,
                </p>
                
                <p class="welcome-message">
                    Nos complace informarte que tu cuenta ha sido aprobada por nuestro equipo. 
                    Ahora tienes acceso completo a nuestro catálogo de productos y puedes comenzar 
                    a realizar pedidos de manera inmediata.
                </p>

                <div class="credentials-box">
                    <h3 style="color: #E4002B; margin-top: 0;">Tus datos de acceso:</h3>
                    <div class="field">
                        <strong>Usuario:</strong> {{ $data['usuario'] }}
                    </div>
                    <div class="field">
                        <strong>Email:</strong> {{ $data['email'] }}
                    </div>
                </div>

                <div class="btn-container">
                    <a href="{{ $data['url_login'] }}" class="btn">
                        INICIAR SESIÓN AHORA
                    </a>
                </div>
            </div>

            <div class="section">
                <h3 style="color: #E4002B; margin-top: 0;">¿Qué puedes hacer ahora?</h3>
                <ul class="info-list">
                    <li>✓ Explorar nuestro catálogo completo de productos</li>
                    <li>✓ Realizar pedidos online de forma rápida y segura</li>
                    <li>✓ Consultar precios y disponibilidad en tiempo real</li>
                    <li>✓ Acceder a tus descuentos especiales</li>
                    <li>✓ Ver el historial de tus pedidos</li>
                    <li>✓ Gestionar tus datos de facturación y envío</li>
                </ul>
            </div>

            @if(!empty($data['descuento']) && $data['descuento'] > 0)
            <div class="section" style="background: #fff3cd; border-left: 4px solid #ffc107;">
                <h3 style="color: #856404; margin-top: 0;">🎁 Descuento Especial</h3>
                <p style="color: #856404; font-size: 16px;">
                    Tu cuenta cuenta con un <strong>{{ $data['descuento'] }}% de descuento</strong> en todos tus pedidos.
                </p>
            </div>
            @endif

            <div class="section">
                <h3 style="color: #E4002B; margin-top: 0;">¿Necesitas ayuda?</h3>
                <p>
                    Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos:
                </p>
                <ul class="info-list">
                    <li>📧 Email: {{ $data['contacto_email'] ?? 'info@Todotex.com' }}</li>
                    @if(!empty($data['contacto_telefono']))
                    <li>📞 Teléfono: {{ $data['contacto_telefono'] }}</li>
                    @endif
                    @if(!empty($data['contacto_whatsapp']))
                    <li>💬 WhatsApp: {{ $data['contacto_whatsapp'] }}</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Todotex. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>