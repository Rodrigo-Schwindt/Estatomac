<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido {{ $pedido->numero_pedido }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            padding: 40px;
        }
        
        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #E40044;
        }
        
        .header h1 {
            color: #E40044;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px 5px 0;
            width: 150px;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .items-table thead {
            background-color: #000;
            color: #fff;
        }
        
        .items-table th {
            padding: 12px;
            text-align: left;
            font-weight: normal;
        }
        
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #E5E5E5;
        }
        
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .totals {
            margin-top: 30px;
            float: right;
            width: 300px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        
        .totals-row.total {
            border-top: 2px solid #E40044;
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
            padding-top: 15px;
        }
        
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #E5E5E5;
            text-align: center;
            color: #666;
            font-size: 10px;
            clear: both;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-entregado {
            background-color: #4CAF50;
            color: white;
        }
        
        .status-pendiente {
            background-color: #FFC107;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PEDIDO #{{ $pedido->numero_pedido }}</h1>
            <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Información del Cliente -->
        <div class="info-section">
            <h2 style="margin-bottom: 15px; font-size: 16px;">Información del Cliente</h2>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Cliente:</div>
                    <div class="info-value">{{ $pedido->cliente->nombre }} {{ $pedido->cliente->apellido }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $pedido->cliente->email }}</div>
                </div>
                @if($pedido->cliente->telefono)
                <div class="info-row">
                    <div class="info-label">Teléfono:</div>
                    <div class="info-value">{{ $pedido->cliente->telefono }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Información del Pedido -->
        <div class="info-section">
            <h2 style="margin-bottom: 15px; font-size: 16px;">Detalles del Pedido</h2>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Fecha de Compra:</div>
                    <div class="info-value">{{ $pedido->fecha_compra->format('d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Entrega:</div>
                    <div class="info-value">{{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('d/m/Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estado:</div>
                    <div class="info-value">
                        <span class="status-badge {{ $pedido->entregado ? 'status-entregado' : 'status-pendiente' }}">
                            {{ $pedido->entregado ? 'ENTREGADO' : 'PENDIENTE' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <h2 style="margin-bottom: 15px; font-size: 16px;">Productos</h2>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->items as $item)
                <tr>
                    <td>{{ $item->producto->nombre }}</td>
                    <td class="text-right">{{ $item->cantidad }}</td>
                    <td class="text-right">${{ number_format($item->precio_unitario, 2, ',', '.') }}</td>
                    <td class="text-right">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>${{ number_format($pedido->total, 2, ',', '.') }}</span>
            </div>
            <div class="totals-row total">
                <span>TOTAL:</span>
                <span>${{ number_format($pedido->total, 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Este documento es una representación impresa de un pedido electrónico</p>
            <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>