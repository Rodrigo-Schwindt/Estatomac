<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nuevo Pedido</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #E4002B; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .section { background: white; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .section-title { color: #E4002B; font-size: 18px; font-weight: bold; margin-bottom: 10px; border-bottom: 2px solid #E4002B; padding-bottom: 5px; }
        .field { margin-bottom: 10px; }
        .field strong { display: inline-block; min-width: 150px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th { background: #333; color: white; padding: 10px; text-align: left; }
        table td { padding: 10px; border-bottom: 1px solid #ddd; }
        .total-row { background: #f0f0f0; font-weight: bold; }
        .grand-total { background: #E4002B; color: white; font-size: 18px; }
        .mensaje-box { background: #f0f0f0; padding: 15px; border-left: 4px solid #E4002B; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nuevo Pedido Recibido</h1>
        </div>
        
        <div class="content">
            <!-- Información del Cliente -->
            <div class="section">
                <div class="section-title">Datos del Cliente</div>
                <div class="field">
                    <strong>Nombre:</strong> {{ $data['cliente']['nombre'] }}
                </div>
                <div class="field">
                    <strong>Email:</strong> {{ $data['cliente']['email'] }}
                </div>
                @if(!empty($data['cliente']['telefono']))
                <div class="field">
                    <strong>Teléfono:</strong> {{ $data['cliente']['telefono'] }}
                </div>
                @endif
                @if(!empty($data['cliente']['domicilio']))
                <div class="field">
                    <strong>Domicilio:</strong> {{ $data['cliente']['domicilio'] }}
                </div>
                @endif
                @if(!empty($data['cliente']['localidad']))
                <div class="field">
                    <strong>Localidad:</strong> {{ $data['cliente']['localidad'] }}
                </div>
                @endif
                @if(!empty($data['cliente']['provincia']))
                <div class="field">
                    <strong>Provincia:</strong> {{ $data['cliente']['provincia'] }}
                </div>
                @endif
            </div>

            <!-- Forma de Pago -->
            <div class="section">
                <div class="section-title">Forma de Pago</div>
                <div class="field">
                    <strong>Método seleccionado:</strong> 
                    @if($data['forma_pago'] === 'contado')
                        Contado
                    @elseif($data['forma_pago'] === 'transferencia')
                        Transferencia
                    @else
                        Cuenta Corriente
                    @endif
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="section">
                <div class="section-title">Productos</div>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th style="text-align: right;">Precio Unit.</th>
                            <th style="text-align: center;">Cantidad</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['productos'] as $producto)
                        <tr>
                            <td>{{ $producto['codigo'] }}</td>
                            <td>{{ $producto['nombre'] }}</td>
                            <td style="text-align: right;">${{ number_format($producto['precio_unitario'], 2, ',', '.') }}</td>
                            <td style="text-align: center;">{{ $producto['cantidad'] }}</td>
                            <td style="text-align: right;">${{ number_format($producto['subtotal'], 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Resumen del Pedido -->
            <div class="section">
                <div class="section-title">Resumen del Pedido</div>
                <table>
                    <tr>
                        <td><strong>Subtotal sin descuento:</strong></td>
                        <td style="text-align: right;">${{ number_format($data['resumen']['subtotal_sin_descuento'], 2, ',', '.') }}</td>
                    </tr>
                    @if($data['resumen']['descuentos'] > 0)
                    <tr style="color: #007600;">
                        <td><strong>Descuentos ({{ number_format($data['resumen']['porcentaje_descuento'], 2) }}%):</strong></td>
                        <td style="text-align: right;">-${{ number_format($data['resumen']['descuentos'], 2, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td style="text-align: right;">${{ number_format($data['resumen']['subtotal'], 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>IVA ({{ number_format($data['resumen']['porcentaje_iva'], 2) }}%):</strong></td>
                        <td style="text-align: right;">${{ number_format($data['resumen']['iva'], 2, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td><strong>TOTAL:</strong></td>
                        <td style="text-align: right;"><strong>${{ number_format($data['resumen']['total'], 2, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Mensaje del Cliente -->
            @if(!empty($data['mensaje']))
            <div class="section">
                <div class="section-title">Mensaje del Cliente</div>
                <div class="mensaje-box">
                    {{ $data['mensaje'] }}
                </div>
            </div>
            @endif

            <!-- Archivo Adjunto -->
            @if(!empty($data['archivo_nombre']))
            <div class="section">
                <div class="section-title">Archivo Adjunto</div>
                <div class="field">
                    <strong>Nombre del archivo:</strong> {{ $data['archivo_nombre'] }}
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>