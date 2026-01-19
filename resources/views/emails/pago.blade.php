<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nuevo Comprobante de Pago</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #E4002B; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .section { background: white; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .section-title { color: #E4002B; font-size: 18px; font-weight: bold; margin-bottom: 10px; border-bottom: 2px solid #E4002B; padding-bottom: 5px; }
        .field { margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .field:last-child { border-bottom: none; }
        .field strong { display: inline-block; min-width: 180px; color: #555; }
        .field-value { color: #111; font-weight: 500; }
        .observaciones-box { background: #f8f8f8; padding: 15px; border-left: 4px solid #E4002B; margin-top: 10px; border-radius: 4px; }
        .highlight { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nuevo Comprobante de Pago Recibido</h1>
        </div>
        
        <div class="content">
            <!-- Información del Pago -->
            <div class="section">
                <div class="section-title">Datos del Pago</div>
                
                <div class="field">
                    <strong>Fecha:</strong> 
                    <span class="field-value">{{ \Carbon\Carbon::parse($data['fecha'])->format('d/m/Y') }}</span>
                </div>
                
                <div class="field">
                    <strong>Importe:</strong> 
                    <span class="field-value" style="color: #E4002B; font-size: 18px; font-weight: bold;">
                        ${{ number_format($data['importe'], 2, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Información Bancaria -->
            <div class="section">
                <div class="section-title">Información Bancaria</div>
                
                <div class="field">
                    <strong>Banco:</strong> 
                    <span class="field-value">{{ $data['banco'] }}</span>
                </div>
                
                <div class="field">
                    <strong>Sucursal:</strong> 
                    <span class="field-value">{{ $data['sucursal'] }}</span>
                </div>
                
                @if(!empty($data['facturas']))
                <div class="field">
                    <strong>Facturas canceladas:</strong> 
                    <span class="field-value">{{ $data['facturas'] }}</span>
                </div>
                @endif
            </div>

            <!-- Observaciones -->
            @if(!empty($data['observaciones']))
            <div class="section">
                <div class="section-title">Observaciones / Aclaraciones</div>
                <div class="observaciones-box">
                    {{ $data['observaciones'] }}
                </div>
            </div>
            @endif

            <!-- Archivo Adjunto -->
            <div class="section">
                <div class="section-title">Comprobante Adjunto</div>
                <div class="highlight">
                    <strong>📎 Nombre del archivo:</strong> {{ $data['archivo_nombre'] }}
                </div>
                <p style="color: #666; font-size: 14px; margin-top: 10px;">
                    El comprobante de pago se encuentra adjunto a este correo electrónico.
                </p>
            </div>

            <!-- Información del Cliente -->
            @if(!empty($data['cliente']))
            <div class="section">
                <div class="section-title">Datos del Cliente</div>
                
                <div class="field">
                    <strong>Nombre:</strong> 
                    <span class="field-value">{{ $data['cliente']['nombre'] }}</span>
                </div>
                
                <div class="field">
                    <strong>Email:</strong> 
                    <span class="field-value">{{ $data['cliente']['email'] }}</span>
                </div>
                
                @if(!empty($data['cliente']['telefono']))
                <div class="field">
                    <strong>Teléfono:</strong> 
                    <span class="field-value">{{ $data['cliente']['telefono'] }}</span>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</body>
</html>