<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #{{ $pedido->numero_pedido }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 30px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-info h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-number {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .section-title {
            background-color: #000;
            color: #fff;
            padding: 10px;
            margin: 20px 0 10px 0;
            font-weight: bold;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-box {
            padding: 15px;
            border: 1px solid #ddd;
        }
        
        .info-box p {
            margin-bottom: 5px;
        }
        
        .info-box strong {
            display: inline-block;
            width: 120px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background-color: #f5f5f5;
        }
        
        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        table th {
            font-weight: bold;
        }
        
        table td.text-right,
        table th.text-right {
            text-align: right;
        }
        
        table td.text-center,
        table th.text-center {
            text-align: center;
        }
        
        .totals {
            margin-left: auto;
            width: 300px;
            margin-top: 20px;
        }
        
        .totals table {
            width: 100%;
        }
        
        .totals .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 14px;
        }
        
        .discount-row {
            color: #308C05;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .status-entregado {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h1>TU EMPRESA</h1>
            <p>Dirección de tu empresa</p>
            <p>Teléfono: (000) 000-0000</p>
            <p>Email: info@tuempresa.com</p>
        </div>
        <div class="invoice-info">
            <div class="invoice-number">FACTURA</div>
            <p><strong>Nº:</strong> {{ $pedido->numero_pedido }}</p>
            <p><strong>Fecha:</strong> {{ $pedido->fecha_compra->format('d/m/Y') }}</p>
            <p>
                <span class="status-badge {{ $pedido->entregado ? 'status-entregado' : 'status-pendiente' }}">
                    {{ $pedido->entregado ? 'ENTREGADO' : 'PENDIENTE' }}
                </span>
            </p>
        </div>
    </div>

    <div class="section-title">DATOS DEL CLIENTE</div>
    <div class="info-grid">
        <div class="info-box">
            <p><strong>Nombre:</strong> {{ $pedido->cliente->nombre }}</p>
            <p><strong>Email:</strong> {{ $pedido->cliente->email }}</p>
            <p><strong>Teléfono:</strong> {{ $pedido->cliente->telefono }}</p>
        </div>
        <div class="info-box">
            <p><strong>Domicilio:</strong> {{ $pedido->cliente->domicilio }}</p>
            <p><strong>Localidad:</strong> {{ $pedido->cliente->localidad }}</p>
            <p><strong>Provincia:</strong> {{ $pedido->cliente->provincia }}</p>
        </div>
    </div>

    <div class="section-title">DETALLES DEL PEDIDO</div>
    <div class="info-box" style="margin-bottom: 20px;">
        <p><strong>Forma de pago:</strong> <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $pedido->forma_pago) }}</span></p>
        <p><strong>Fecha de entrega:</strong> {{ $pedido->fecha_entrega->format('d/m/Y') }}</p>
        @if($pedido->entregado && $pedido->fecha_entregado)
        <p><strong>Entregado el:</strong> {{ $pedido->fecha_entregado->format('d/m/Y') }}</p>
        @endif
        @if($pedido->mensaje)
        <p style="margin-top: 10px;"><strong>Mensaje:</strong></p>
        <p style="font-style: italic; color: #666;">{{ $pedido->mensaje }}</p>
        @endif
    </div>

    <div class="section-title">PRODUCTOS</div>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->items as $item)
            <tr>
                <td>{{ $item->codigo_producto }}</td>
                <td>{{ $item->nombre_producto }}</td>
                <td class="text-right">
                    ${{ number_format($item->precio_unitario - $item->descuento_unitario, 2, ',', '.') }}
                    @if($item->descuento_unitario > 0)
                    <br><small style="color: #999; text-decoration: line-through;">
                        ${{ number_format($item->precio_unitario, 2, ',', '.') }}
                    </small>
                    @endif
                </td>
                <td class="text-center">{{ $item->cantidad }}</td>
                <td class="text-right">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal sin descuento:</td>
                <td class="text-right">${{ number_format($pedido->subtotal_sin_descuento, 2, ',', '.') }}</td>
            </tr>
            @if($pedido->descuentos > 0)
            <tr class="discount-row">
                <td>Descuentos ({{ number_format($pedido->porcentaje_descuento, 2) }}%):</td>
                <td class="text-right">-${{ number_format($pedido->descuentos, 2, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">${{ number_format($pedido->subtotal, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>IVA ({{ number_format($pedido->porcentaje_iva, 2) }}%):</td>
                <td class="text-right">${{ number_format($pedido->iva, 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL:</td>
                <td class="text-right">${{ number_format($pedido->total, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Factura generada el {{ now()->format('d/m/Y H:i') }}</p>
        <p>Gracias por su compra</p>
    </div>
</body>
</html>