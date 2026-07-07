<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nuevo Cliente Registrado</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #E4002B; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .section { background: white; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .section-title { color: #E4002B; font-size: 18px; font-weight: bold; margin-bottom: 10px; border-bottom: 2px solid #E4002B; padding-bottom: 5px; }
        .field { margin-bottom: 10px; padding: 8px; }
        .field strong { display: inline-block; min-width: 150px; color: #555; }
        .alert-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 10px; }
        .info-box { background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin-top: 10px; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Nuevo Cliente Registrado</h1>
        </div>
        
        <div class="content">
            <div class="alert-box">
                <strong>⚠️ Acción Requerida:</strong> Este cliente está pendiente de aprobación. Debes activar su cuenta desde el panel de administración.
            </div>

            <!-- Información del Cliente -->
            <div class="section">
                <div class="section-title">Datos del Cliente</div>
                
                <div class="field">
                    <strong>Nombre Completo:</strong> {{ $data['nombre'] }}
                </div>
                
                <div class="field">
                    <strong>Email:</strong> 
                    <a href="mailto:{{ $data['email'] }}" style="color: #E4002B;">{{ $data['email'] }}</a>
                </div>
                
                <div class="field">
                    <strong>Usuario:</strong> {{ $data['usuario'] }}
                </div>

                @if(!empty($data['cuil']))
                <div class="field">
                    <strong>CUIL:</strong> {{ $data['cuil'] }}
                </div>
                @endif

                @if(!empty($data['cuit']))
                <div class="field">
                    <strong>CUIT:</strong> {{ $data['cuit'] }}
                </div>
                @endif

                @if(!empty($data['telefono']))
                <div class="field">
                    <strong>Teléfono:</strong> {{ $data['telefono'] }}
                </div>
                @endif
            </div>

            <!-- Dirección -->
            @if(!empty($data['domicilio']) || !empty($data['localidad']) || !empty($data['provincia']))
            <div class="section">
                <div class="section-title">Dirección</div>
                
                @if(!empty($data['domicilio']))
                <div class="field">
                    <strong>Domicilio:</strong> {{ $data['domicilio'] }}
                </div>
                @endif

                @if(!empty($data['localidad']))
                <div class="field">
                    <strong>Localidad:</strong> {{ $data['localidad'] }}
                </div>
                @endif

                @if(!empty($data['provincia']))
                <div class="field">
                    <strong>Provincia:</strong> {{ $data['provincia'] }}
                </div>
                @endif
            </div>
            @endif

            <!-- Información adicional -->
            <div class="section">
                <div class="section-title">Estado de la Cuenta</div>
                
                <div class="field">
                    <strong>Estado:</strong> 
                    <span style="color: #ffc107; font-weight: bold;">⏳ PENDIENTE DE APROBACIÓN</span>
                </div>
                
                <div class="field">
                    <strong>Fecha de registro:</strong> {{ $data['fecha_registro'] }}
                </div>
            </div>

            <div class="info-box">
                <strong>📝 Próximos pasos:</strong><br>
                1. Revisa los datos del cliente<br>
                2. Accede al panel de administración<br>
                3. Activa la cuenta del cliente<br>
                4. El cliente recibirá una notificación por email automáticamente
            </div>
        </div>
    </div>
</body>
</html>