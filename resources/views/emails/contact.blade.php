<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nuevo mensaje de contacto</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #004896; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .field { margin-bottom: 15px; }
        .field strong { display: inline-block; width: 100px; }
        .message { background: white; padding: 15px; border-left: 4px solid #004896; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nuevo mensaje de contacto</h1>
        </div>
        
        <div class="content">
            <div class="field">
                <strong>Nombre:</strong> {{ $data['nombre'] }}
            </div>
            
            <div class="field">
                <strong>Empresa:</strong> {{ $data['empresa'] }}
            </div>
            
            <div class="field">
                <strong>Email:</strong> {{ $data['email'] }}
            </div>
            
            <div class="field">
                <strong>Celular:</strong> {{ $data['celular'] }}
            </div>
            
            <div class="field">
                <strong>Mensaje:</strong>
            </div>
            
            <div class="message">
                {{ $data['mensaje'] }}
            </div>
        </div>
    </div>
</body>
</html>