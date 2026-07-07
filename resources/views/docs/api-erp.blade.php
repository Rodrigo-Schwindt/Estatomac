<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>API B2B Todotex → ERP</title>
<style>
    @page { margin: 30px 40px; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; line-height: 1.5; color: #1f2937; }
    h1 { color: #23378C; font-size: 22px; border-bottom: 2px solid #23378C; padding-bottom: 6px; margin: 0 0 12px; }
    h2 { color: #23378C; font-size: 15px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; margin-top: 22px; }
    h3 { color: #374151; font-size: 12.5px; margin-top: 14px; margin-bottom: 6px; }
    h4 { color: #4b5563; font-size: 11.5px; margin: 10px 0 4px; }
    p  { margin: 6px 0; }
    a  { color: #1d4ed8; text-decoration: none; word-break: break-all; }
    code { background: #f3f4f6; padding: 1px 4px; border-radius: 3px; font-family: DejaVu Sans Mono, monospace; font-size: 10px; }
    pre { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px; font-family: DejaVu Sans Mono, monospace; font-size: 9.5px; line-height: 1.4; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; }
    table { width: 100%; border-collapse: collapse; margin: 8px 0 14px; }
    th, td { border: 1px solid #e5e7eb; padding: 5px 8px; text-align: left; vertical-align: top; font-size: 10px; }
    th { background: #f3f4f6; font-weight: 600; color: #374151; }
    tr:nth-child(even) td { background: #fafafa; }
    .meta { color: #6b7280; font-size: 10px; margin-bottom: 16px; }
    .callout { border-left: 4px solid #f59e0b; background: #fffbeb; padding: 8px 12px; margin: 10px 0; font-size: 10.5px; }
    .ok      { border-left: 4px solid #10b981; background: #ecfdf5; padding: 8px 12px; margin: 10px 0; font-size: 10.5px; }
    .info    { border-left: 4px solid #3b82f6; background: #eff6ff; padding: 8px 12px; margin: 10px 0; font-size: 10.5px; }
    .endpoint-url { background: #1f2937; color: #fff; padding: 8px 10px; border-radius: 4px; font-family: DejaVu Sans Mono, monospace; font-size: 10px; margin: 6px 0 10px; word-break: break-all; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 9px; font-weight: 600; }
    .badge-read { background: #dbeafe; color: #1e40af; }
    .badge-mut  { background: #fef3c7; color: #92400e; }
    .pagebreak { page-break-after: always; }
</style>
</head>
<body>

<h1>API B2B Todotex → ERP</h1>
<p class="meta">
    Documentación de los endpoints REST que expone el sistema B2B de Todotex para que el ERP consuma los pedidos generados por clientes y vendedores.<br>
    Schema de salida alineado a las tablas <strong>Nota_Pedido_Cabecera</strong> y <strong>Nota_Pedido_Detalle</strong> del descriptivo.<br>
    <strong>Generado el {{ now()->format('d/m/Y H:i') }}</strong>
</p>

<table>
    <tr><th style="width:30%">Base URL</th><td><code>https://todotex.osole.com.ar/api/erp</code></td></tr>
    <tr><th>Formato</th><td><strong>JSON</strong> (default) o <strong>XML</strong> según query param <code>?format=xml</code></td></tr>
    <tr><th>Encoding</th><td>UTF-8</td></tr>
    <tr><th>Método</th><td>Todos los endpoints son <code>GET</code></td></tr>
    <tr><th>Autenticación</th><td><strong>Momentáneamente DESACTIVADA</strong> para pruebas. En producción se va a requerir header <code>X-API-Key: &lt;key&gt;</code> entregado por canal seguro.</td></tr>
</table>

<div class="info">
    <strong>Soporte de XML:</strong> agregando <code>?format=xml</code> a cualquier endpoint, la respuesta viene en XML en lugar de JSON. Mismo contenido, mismos campos, mismo schema.
    Ejemplo: <code>https://todotex.osole.com.ar/api/erp/pedidos/pendientes?format=xml</code>
</div>

<h2>Endpoints disponibles</h2>
<table>
    <thead><tr><th>#</th><th>Endpoint</th><th>Efecto</th><th>Para qué sirve</th></tr></thead>
    <tbody>
        <tr><td>1</td><td><code>GET /health</code></td><td><span class="badge badge-read">nada</span></td><td>Verificar que la API está arriba</td></tr>
        <tr><td>2</td><td><code>GET /pedidos</code></td><td><span class="badge badge-read">read-only</span></td><td>Listar todos los pedidos con filtros</td></tr>
        <tr><td>3</td><td><code>GET /pedidos/pendientes</code></td><td><span class="badge badge-mut">marca como ENVIADO</span></td><td>Ciclo normal del ERP</td></tr>
        <tr><td>4</td><td><code>GET /pedidos/{numero}</code></td><td><span class="badge badge-read">read-only</span></td><td>Consultar un pedido puntual</td></tr>
    </tbody>
</table>

<div class="pagebreak"></div>

{{-- ============================================================ --}}
<h2>Schema del pedido (Nota_Pedido_Cabecera / Nota_Pedido_Detalle)</h2>

<p>Cada pedido se entrega con la estructura definida en el descriptivo, dividida en <strong>Cabecera</strong> y <strong>Detalle</strong> (array de Items).</p>

<h3>Nota_Pedido_Cabecera</h3>
<table>
    <thead><tr><th>Campo</th><th>Tipo</th><th>Descripción / Mapeo</th></tr></thead>
    <tbody>
        <tr><td><code>NPCabeceraPK</code></td><td>int</td><td>PK secuencial del B2B (identifica el pedido en mi sistema)</td></tr>
        <tr><td><code>NPNumero</code></td><td>int</td><td>Número de pedido secuencial</td></tr>
        <tr><td><code>NPEstado</code></td><td>int</td><td><strong>0</strong> = Pendiente, <strong>1</strong> = Enviado, <strong>2</strong> = Anulado</td></tr>
        <tr><td><code>Fecha</code></td><td>date</td><td>Fecha de la compra (<code>YYYY-MM-DD</code>)</td></tr>
        <tr><td><code>Hora</code></td><td>varchar(5)</td><td>Hora de creación del pedido (<code>HH:MM</code>)</td></tr>
        <tr><td><code>ClientesPK</code></td><td>FK (int)</td><td><strong>PK numérica del cliente</strong> tal como existe en su ERP (la que mandan en <code>Clientes.xlsx → ClientesPK</code>). El B2B la guarda al importar y la devuelve idéntica.</td></tr>
        <tr><td><code>VendedoresPK</code></td><td>FK (int)</td><td><strong>PK numérica del vendedor</strong> tal como existe en su ERP.</td></tr>
        <tr><td><code>CanalesPK</code></td><td>FK (int)</td><td><strong>PK numérica del canal</strong> tal como existe en su ERP (<code>Canales.xlsx → CanalesPK</code>).</td></tr>
        <tr><td><code>CondicionesVentasPK</code></td><td>FK (int)</td><td>PK numérica de la condición de venta tal como existe en su ERP.</td></tr>
        <tr><td><code>Observaciones</code></td><td>varchar(120)</td><td>Mensaje/notas que dejó quien armó el pedido</td></tr>
        <tr><td><code>ImporteTotal</code></td><td>decimal(18,2)</td><td>Total final del pedido</td></tr>
        <tr><td><code>Neto1</code></td><td>decimal(18,2)</td><td>Subtotal neto (con el % de IVA configurado en el carrito al momento del pedido — típicamente 21%)</td></tr>
        <tr><td><code>Neto2</code></td><td>decimal(18,2)</td><td><strong>0</strong> — reservado para alícuota secundaria (10,5%, etc.) cuando se implemente diferenciación</td></tr>
        <tr><td><code>Neto3</code></td><td>decimal(18,2)</td><td><strong>0</strong> — reservado para alícuota terciaria (27%, etc.)</td></tr>
        <tr><td><code>IVA1</code></td><td>decimal(18,2)</td><td>Monto total de IVA del pedido (snapshot del % vigente al momento de crear el pedido)</td></tr>
        <tr><td><code>IVA2</code></td><td>decimal(18,2)</td><td><strong>0</strong> — reservado</td></tr>
        <tr><td><code>IVA3</code></td><td>decimal(18,2)</td><td><strong>0</strong> — reservado</td></tr>
        <tr><td><code>DescuentoCliente</code></td><td>decimal(18,2)</td><td>Monto total de descuentos aplicados a nivel cabecera</td></tr>
    </tbody>
</table>

<h3>Nota_Pedido_Detalle (un Item por producto)</h3>
<table>
    <thead><tr><th>Campo</th><th>Tipo</th><th>Descripción / Mapeo</th></tr></thead>
    <tbody>
        <tr><td><code>NPDetallePK</code></td><td>int</td><td>PK secuencial del renglón</td></tr>
        <tr><td><code>NPCabeceraPK</code></td><td>FK</td><td>FK a la cabecera (mismo valor que el NPCabeceraPK del pedido)</td></tr>
        <tr><td><code>ProductosPK</code></td><td>FK (int)</td><td><strong>PK numérica del producto</strong> tal como existe en su ERP (<code>Productos.xlsx → ProductosPK</code>). El B2B la guarda al importar y la devuelve idéntica.</td></tr>
        <tr><td><code>Cantidad</code></td><td>decimal(18,2)</td><td>Cantidad pedida</td></tr>
        <tr><td><code>Importe</code></td><td>decimal(18,2)</td><td>Subtotal del renglón (cantidad × precio neto)</td></tr>
        <tr><td><code>DescuentoProducto</code></td><td>decimal(18,2)</td><td>% de descuento base que viene de la lista de precios (canal + cliente)</td></tr>
        <tr><td><code>Descuento</code></td><td>decimal(18,2)</td><td>% de descuento adicional aplicado por el vendedor</td></tr>
        <tr><td><code>Observaciones</code></td><td>varchar(120)</td><td>Observación particular del renglón (puede venir vacía)</td></tr>
        <tr><td><code>IVA1</code></td><td>decimal(18,2)</td><td>IVA del renglón calculado con el % vigente al momento del pedido</td></tr>
        <tr><td><code>IVA2</code></td><td>decimal(18,2)</td><td><strong>0</strong> — reservado</td></tr>
        <tr><td><code>IVA3</code></td><td>decimal(18,2)</td><td><strong>0</strong> — reservado</td></tr>
    </tbody>
</table>

<div class="info">
    <strong>Sobre las PKs:</strong> los campos terminados en <code>PK</code> (ClientesPK, VendedoresPK, ProductosPK, CanalesPK, etc.) llevan la <strong>PK numérica</strong> que ustedes tienen en su ERP, idéntica a la que enviaron por FTP en la carga de maestros. Cuando reciben un pedido pueden hacer <code>SELECT * FROM Clientes WHERE ClientesPK = X</code> directo, sin necesidad de cruzar por Codigo.
</div>

<div class="pagebreak"></div>

{{-- ============================================================ --}}
<h2>1) Health</h2>
<div class="endpoint-url">https://todotex.osole.com.ar/api/erp/health</div>
<p>Útil antes de empezar el ciclo de polling o como monitor de uptime. No retorna pedidos, no toca la base.</p>

<h4>JSON</h4>
<pre>{
  "status": "ok",
  "time": "2026-05-27T09:00:00-03:00",
  "timezone": "America/Argentina/Buenos_Aires"
}</pre>

<h4>XML — agregando <code>?format=xml</code></h4>
<pre>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;Health&gt;
  &lt;status&gt;ok&lt;/status&gt;
  &lt;time&gt;2026-05-27T09:00:00-03:00&lt;/time&gt;
  &lt;timezone&gt;America/Argentina/Buenos_Aires&lt;/timezone&gt;
&lt;/Health&gt;</pre>

{{-- ============================================================ --}}
<h2>2) Listado completo de pedidos</h2>
<div class="endpoint-url">https://todotex.osole.com.ar/api/erp/pedidos</div>
<p>Devuelve <strong>todos</strong> los pedidos independientemente de su estado. Read-only — no marca nada, no cambia estados.</p>

<h4>Filtros opcionales</h4>
<table>
    <thead><tr><th>Parámetro</th><th>Valores</th><th>Default</th></tr></thead>
    <tbody>
        <tr><td><code>estado</code></td><td><code>pendiente</code> | <code>enviado</code> | <code>anulado</code></td><td>(todos)</td></tr>
        <tr><td><code>desde</code></td><td>fecha <code>YYYY-MM-DD</code></td><td>sin filtro</td></tr>
        <tr><td><code>hasta</code></td><td>fecha <code>YYYY-MM-DD</code></td><td>sin filtro</td></tr>
        <tr><td><code>limit</code></td><td>1 a 500</td><td>100</td></tr>
        <tr><td><code>page</code></td><td>1, 2, 3...</td><td>1</td></tr>
        <tr><td><code>format</code></td><td><code>json</code> | <code>xml</code></td><td><code>json</code></td></tr>
    </tbody>
</table>

<h4>Ejemplo JSON</h4>
<pre>{
  "Pedidos": [
    {
      "Cabecera": {
        "NPCabeceraPK": 3,
        "NPNumero": 3,
        "NPEstado": 1,
        "Fecha": "2026-05-12",
        "Hora": "14:15",
        "ClientesPK": 141,
        "VendedoresPK": 5,
        "CanalesPK": 2,
        "CondicionesVentasPK": 7,
        "Observaciones": "",
        "ImporteTotal": 65012.31,
        "Neto1": 53729.18,
        "Neto2": 0,
        "Neto3": 0,
        "IVA1": 11283.13,
        "IVA2": 0,
        "IVA3": 0,
        "DescuentoCliente": 31555.23
      },
      "Detalle": {
        "Item": [
          {
            "NPDetallePK": 4,
            "NPCabeceraPK": 3,
            "ProductosPK": 316,
            "Cantidad": 1,
            "Importe": 59699.09,
            "DescuentoProducto": 0,
            "Descuento": 0,
            "Observaciones": "",
            "IVA1": 12536.81,
            "IVA2": 0,
            "IVA3": 0
          }
        ]
      }
    }
  ],
  "Meta": {
    "Total": 4,
    "Limit": 100,
    "Page": 1,
    "LastPage": 1,
    "GeneratedAt": "2026-05-27T09:00:00-03:00",
    "Note": "Read-only endpoint. No marca pedidos como enviados."
  }
}</pre>

<div class="pagebreak"></div>

<h4>Ejemplo XML — agregando <code>?format=xml</code></h4>
<pre>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;PedidosResponse&gt;
  &lt;Pedidos&gt;
    &lt;Pedido&gt;
      &lt;Cabecera&gt;
        &lt;NPCabeceraPK&gt;3&lt;/NPCabeceraPK&gt;
        &lt;NPNumero&gt;3&lt;/NPNumero&gt;
        &lt;NPEstado&gt;1&lt;/NPEstado&gt;
        &lt;Fecha&gt;2026-05-12&lt;/Fecha&gt;
        &lt;Hora&gt;14:15&lt;/Hora&gt;
        &lt;ClientesPK&gt;141&lt;/ClientesPK&gt;
        &lt;VendedoresPK&gt;5&lt;/VendedoresPK&gt;
        &lt;CanalesPK&gt;2&lt;/CanalesPK&gt;
        &lt;CondicionesVentasPK&gt;7&lt;/CondicionesVentasPK&gt;
        &lt;Observaciones/&gt;
        &lt;ImporteTotal&gt;65012.31&lt;/ImporteTotal&gt;
        &lt;Neto1&gt;53729.18&lt;/Neto1&gt;
        &lt;Neto2&gt;0&lt;/Neto2&gt;
        &lt;Neto3&gt;0&lt;/Neto3&gt;
        &lt;IVA1&gt;11283.13&lt;/IVA1&gt;
        &lt;IVA2&gt;0&lt;/IVA2&gt;
        &lt;IVA3&gt;0&lt;/IVA3&gt;
        &lt;DescuentoCliente&gt;31555.23&lt;/DescuentoCliente&gt;
      &lt;/Cabecera&gt;
      &lt;Detalle&gt;
        &lt;Item&gt;
          &lt;NPDetallePK&gt;4&lt;/NPDetallePK&gt;
          &lt;NPCabeceraPK&gt;3&lt;/NPCabeceraPK&gt;
          &lt;ProductosPK&gt;316&lt;/ProductosPK&gt;
          &lt;Cantidad&gt;1&lt;/Cantidad&gt;
          &lt;Importe&gt;59699.09&lt;/Importe&gt;
          &lt;DescuentoProducto&gt;0&lt;/DescuentoProducto&gt;
          &lt;Descuento&gt;0&lt;/Descuento&gt;
          &lt;Observaciones/&gt;
          &lt;IVA1&gt;12536.81&lt;/IVA1&gt;
          &lt;IVA2&gt;0&lt;/IVA2&gt;
          &lt;IVA3&gt;0&lt;/IVA3&gt;
        &lt;/Item&gt;
      &lt;/Detalle&gt;
    &lt;/Pedido&gt;
  &lt;/Pedidos&gt;
  &lt;Meta&gt;
    &lt;Total&gt;4&lt;/Total&gt;
    &lt;Limit&gt;100&lt;/Limit&gt;
    &lt;Page&gt;1&lt;/Page&gt;
    &lt;LastPage&gt;1&lt;/LastPage&gt;
    &lt;GeneratedAt&gt;2026-05-27T09:00:00-03:00&lt;/GeneratedAt&gt;
    &lt;Note&gt;Read-only endpoint. No marca pedidos como enviados.&lt;/Note&gt;
  &lt;/Meta&gt;
&lt;/PedidosResponse&gt;</pre>

<div class="pagebreak"></div>

{{-- ============================================================ --}}
<h2>3) Pedidos pendientes — el endpoint del ciclo normal</h2>
<div class="endpoint-url">https://todotex.osole.com.ar/api/erp/pedidos/pendientes</div>

<div class="callout">
    <strong>⚠ Comportamiento clave:</strong> este endpoint devuelve los pedidos en estado <strong>PENDIENTE (NPEstado=0)</strong> y al mismo tiempo los marca como <strong>ENVIADO (NPEstado=1)</strong> en una sola operación atómica. Si lo recargás una segunda vez, vas a ver array/lista vacía porque los pedidos ya cambiaron de estado.<br><br>
    Este es el endpoint que el ERP tiene que consumir en su ciclo de trabajo (ej: cada 5 minutos). Una vez consumidos, esos pedidos quedan en "purgatorio" del ERP — no se pueden anular más desde el B2B y no vuelven a aparecer acá.
</div>

<h4>Filtros opcionales</h4>
<table>
    <thead><tr><th>Parámetro</th><th>Valores</th><th>Default</th></tr></thead>
    <tbody>
        <tr><td><code>limit</code></td><td>1 a 500</td><td>100</td></tr>
        <tr><td><code>desde</code></td><td>fecha <code>YYYY-MM-DD</code></td><td>sin filtro</td></tr>
        <tr><td><code>hasta</code></td><td>fecha <code>YYYY-MM-DD</code></td><td>sin filtro</td></tr>
        <tr><td><code>format</code></td><td><code>json</code> | <code>xml</code></td><td><code>json</code></td></tr>
    </tbody>
</table>

<h4>Ejemplo JSON cuando HAY pendientes</h4>
<pre>{
  "Pedidos": [
    {
      "Cabecera": {
        "NPCabeceraPK": 5,
        "NPNumero": 5,
        "NPEstado": 1,
        "Fecha": "2026-05-26",
        "Hora": "18:20",
        "ClientesPK": 141,
        "VendedoresPK": 5,
        "CanalesPK": 2,
        "CondicionesVentasPK": 7,
        "Observaciones": "",
        "ImporteTotal": 206388.27,
        "Neto1": 170568.82, "Neto2": 0, "Neto3": 0,
        "IVA1": 35819.45,   "IVA2": 0, "IVA3": 0,
        "DescuentoCliente": 0
      },
      "Detalle": {
        "Item": [
          {
            "NPDetallePK": 8,
            "NPCabeceraPK": 5,
            "ProductosPK": 316,
            "Cantidad": 2,
            "Importe": 170568.82,
            "DescuentoProducto": 0,
            "Descuento": 0,
            "Observaciones": "",
            "IVA1": 35819.45, "IVA2": 0, "IVA3": 0
          }
        ]
      }
    }
  ],
  "Meta": {
    "Total": 1,
    "Limit": 100,
    "MarcadosComoEnviado": 1,
    "GeneratedAt": "2026-05-27T09:00:00-03:00"
  }
}</pre>

<h4>Ejemplo JSON cuando NO hay pendientes (después del primer consumo)</h4>
<pre>{
  "Pedidos": [],
  "Meta": {
    "Total": 0,
    "Limit": 100,
    "MarcadosComoEnviado": 0,
    "GeneratedAt": "2026-05-27T09:00:01-03:00"
  }
}</pre>

<h4>Ejemplo XML — agregando <code>?format=xml</code></h4>
<pre>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;PedidosResponse&gt;
  &lt;Pedidos&gt;
    &lt;Pedido&gt;
      &lt;Cabecera&gt;
        &lt;NPCabeceraPK&gt;5&lt;/NPCabeceraPK&gt;
        &lt;NPNumero&gt;5&lt;/NPNumero&gt;
        &lt;NPEstado&gt;1&lt;/NPEstado&gt;
        &lt;Fecha&gt;2026-05-26&lt;/Fecha&gt;
        &lt;Hora&gt;18:20&lt;/Hora&gt;
        &lt;ClientesPK&gt;141&lt;/ClientesPK&gt;
        &lt;VendedoresPK&gt;5&lt;/VendedoresPK&gt;
        &lt;CanalesPK&gt;2&lt;/CanalesPK&gt;
        &lt;CondicionesVentasPK&gt;7&lt;/CondicionesVentasPK&gt;
        &lt;Observaciones/&gt;
        &lt;ImporteTotal&gt;206388.27&lt;/ImporteTotal&gt;
        &lt;Neto1&gt;170568.82&lt;/Neto1&gt;&lt;Neto2&gt;0&lt;/Neto2&gt;&lt;Neto3&gt;0&lt;/Neto3&gt;
        &lt;IVA1&gt;35819.45&lt;/IVA1&gt;&lt;IVA2&gt;0&lt;/IVA2&gt;&lt;IVA3&gt;0&lt;/IVA3&gt;
        &lt;DescuentoCliente&gt;0&lt;/DescuentoCliente&gt;
      &lt;/Cabecera&gt;
      &lt;Detalle&gt;
        &lt;Item&gt;
          &lt;NPDetallePK&gt;8&lt;/NPDetallePK&gt;
          &lt;NPCabeceraPK&gt;5&lt;/NPCabeceraPK&gt;
          &lt;ProductosPK&gt;316&lt;/ProductosPK&gt;
          &lt;Cantidad&gt;2&lt;/Cantidad&gt;
          &lt;Importe&gt;170568.82&lt;/Importe&gt;
          &lt;DescuentoProducto&gt;0&lt;/DescuentoProducto&gt;
          &lt;Descuento&gt;0&lt;/Descuento&gt;
          &lt;Observaciones/&gt;
          &lt;IVA1&gt;35819.45&lt;/IVA1&gt;&lt;IVA2&gt;0&lt;/IVA2&gt;&lt;IVA3&gt;0&lt;/IVA3&gt;
        &lt;/Item&gt;
      &lt;/Detalle&gt;
    &lt;/Pedido&gt;
  &lt;/Pedidos&gt;
  &lt;Meta&gt;
    &lt;Total&gt;1&lt;/Total&gt;
    &lt;Limit&gt;100&lt;/Limit&gt;
    &lt;MarcadosComoEnviado&gt;1&lt;/MarcadosComoEnviado&gt;
    &lt;GeneratedAt&gt;2026-05-27T09:00:00-03:00&lt;/GeneratedAt&gt;
  &lt;/Meta&gt;
&lt;/PedidosResponse&gt;</pre>

<div class="pagebreak"></div>

{{-- ============================================================ --}}
<h2>4) Pedido por número (lectura puntual)</h2>
<div class="endpoint-url">https://todotex.osole.com.ar/api/erp/pedidos/{numero}</div>
<p>Ejemplo: <code>https://todotex.osole.com.ar/api/erp/pedidos/00000003</code></p>
<p>Devuelve un solo pedido. Read-only — no importa el estado del pedido (Pendiente / Enviado / Anulado), lo retorna igual y no toca nada.</p>

<h4>Ejemplo JSON</h4>
<pre>{
  "Pedido": {
    "Cabecera": {
      "NPCabeceraPK": 3,
      "NPNumero": 3,
      "NPEstado": 1,
      "Fecha": "2026-05-12",
      "Hora": "14:15",
      "ClientesPK": 141,
      "VendedoresPK": 5,
      "CanalesPK": 2,
      "CondicionesVentasPK": 7,
      "Observaciones": "",
      "ImporteTotal": 65012.31,
      "Neto1": 53729.18, "Neto2": 0, "Neto3": 0,
      "IVA1": 11283.13,  "IVA2": 0, "IVA3": 0,
      "DescuentoCliente": 31555.23
    },
    "Detalle": {
      "Item": [
        {
          "NPDetallePK": 4,
          "NPCabeceraPK": 3,
          "ProductosPK": 316,
          "Cantidad": 1,
          "Importe": 59699.09,
          "DescuentoProducto": 0,
          "Descuento": 0,
          "Observaciones": "",
          "IVA1": 12536.81, "IVA2": 0, "IVA3": 0
        }
      ]
    }
  }
}</pre>

<h4>Ejemplo XML — agregando <code>?format=xml</code></h4>
<pre>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;PedidoResponse&gt;
  &lt;Pedido&gt;
    &lt;Cabecera&gt;
      &lt;NPCabeceraPK&gt;3&lt;/NPCabeceraPK&gt;
      &lt;NPNumero&gt;3&lt;/NPNumero&gt;
      &lt;NPEstado&gt;1&lt;/NPEstado&gt;
      &lt;Fecha&gt;2026-05-12&lt;/Fecha&gt;
      &lt;Hora&gt;14:15&lt;/Hora&gt;
      &lt;ClientesPK&gt;141&lt;/ClientesPK&gt;
      &lt;VendedoresPK&gt;5&lt;/VendedoresPK&gt;
      &lt;CanalesPK&gt;2&lt;/CanalesPK&gt;
      &lt;CondicionesVentasPK&gt;7&lt;/CondicionesVentasPK&gt;
      &lt;Observaciones/&gt;
      &lt;ImporteTotal&gt;65012.31&lt;/ImporteTotal&gt;
      &lt;Neto1&gt;53729.18&lt;/Neto1&gt;&lt;Neto2&gt;0&lt;/Neto2&gt;&lt;Neto3&gt;0&lt;/Neto3&gt;
      &lt;IVA1&gt;11283.13&lt;/IVA1&gt;&lt;IVA2&gt;0&lt;/IVA2&gt;&lt;IVA3&gt;0&lt;/IVA3&gt;
      &lt;DescuentoCliente&gt;31555.23&lt;/DescuentoCliente&gt;
    &lt;/Cabecera&gt;
    &lt;Detalle&gt;
      &lt;Item&gt;
        &lt;NPDetallePK&gt;4&lt;/NPDetallePK&gt;
        &lt;NPCabeceraPK&gt;3&lt;/NPCabeceraPK&gt;
        &lt;ProductosPK&gt;316&lt;/ProductosPK&gt;
        &lt;Cantidad&gt;1&lt;/Cantidad&gt;
        &lt;Importe&gt;59699.09&lt;/Importe&gt;
        &lt;DescuentoProducto&gt;0&lt;/DescuentoProducto&gt;
        &lt;Descuento&gt;0&lt;/Descuento&gt;
        &lt;Observaciones/&gt;
        &lt;IVA1&gt;12536.81&lt;/IVA1&gt;&lt;IVA2&gt;0&lt;/IVA2&gt;&lt;IVA3&gt;0&lt;/IVA3&gt;
      &lt;/Item&gt;
    &lt;/Detalle&gt;
  &lt;/Pedido&gt;
&lt;/PedidoResponse&gt;</pre>

<h4>Respuesta 404 cuando el número no existe</h4>
<pre>{
  "Error": "not_found",
  "Message": "Pedido 99999999 no existe."
}</pre>

<div class="pagebreak"></div>

{{-- ============================================================ --}}
<h2>Códigos HTTP de respuesta</h2>
<table>
    <thead><tr><th>Código</th><th>Cuándo</th></tr></thead>
    <tbody>
        <tr><td><strong>200</strong></td><td>Operación exitosa (puede traer lista vacía si no hay pendientes)</td></tr>
        <tr><td><strong>401</strong></td><td>Falta el header <code>X-API-Key</code> o es inválido (solo cuando la auth esté activa)</td></tr>
        <tr><td><strong>404</strong></td><td>El pedido solicitado no existe</td></tr>
        <tr><td><strong>422</strong></td><td>Parámetro de query inválido (ej: <code>?limit=99999</code>, <code>?format=yaml</code>)</td></tr>
        <tr><td><strong>500</strong></td><td>Error interno del servidor — contactar a Soporte</td></tr>
    </tbody>
</table>

<h2>Flujo recomendado del ciclo del ERP</h2>
<div class="ok">
<pre style="background: none; border: none; padding: 0;">1. GET /health
   → si responde 200, seguir

2. GET /pedidos/pendientes?format=xml   (o sin ?format= para JSON)
   → recibe N pedidos con Cabecera + Detalle
   → todos quedan automáticamente marcados como ENVIADO en el B2B (NPEstado pasa de 0 a 1)

3. Procesar los N pedidos en el ERP

4. Devolver al B2B la respuesta correspondiente por FTP
   (con número interno del ERP, status, etc.)

5. Esperar N minutos y volver al paso 1</pre>
</div>

<p>Si en algún momento necesitan releer un pedido puntual sin afectar nada:</p>
<pre>GET /pedidos/{numero}?format=xml</pre>

<p>Si necesitan revisar el estado general de todo:</p>
<pre>GET /pedidos?format=xml                              ← todos
GET /pedidos?estado=enviado&format=xml               ← solo enviados
GET /pedidos?estado=pendiente&format=xml             ← solo pendientes (sin marcar)
GET /pedidos?desde=2026-05-01&format=xml             ← desde una fecha</pre>

<h2>Notas técnicas</h2>
<ul>
    <li><strong>Encoding:</strong> tanto JSON como XML salen en UTF-8.</li>
    <li><strong>Decimales:</strong> los montos vienen con 2 decimales como <code>number</code> (en JSON) o como texto (en XML).</li>
    <li><strong>Pedidos anulados:</strong> una vez que un pedido pasa a Enviado (NPEstado=1), no se puede anular más desde el B2B. Solo los pedidos en estado Pendiente (NPEstado=0) pueden ser anulados.</li>
    <li><strong>Idempotencia:</strong> <code>GET /pedidos/pendientes</code> NO es idempotente (cambia estado). Los otros 3 endpoints sí lo son.</li>
    <li><strong>Sobre IVA1/IVA2/IVA3 y Neto1/Neto2/Neto3:</strong> el schema soporta hasta 3 alícuotas. En esta primera versión todos los productos están consolidados en una sola alícuota (la configurada en el carrito al momento del pedido, típicamente 21%) — por lo tanto <code>Neto1</code> y <code>IVA1</code> llevan el total, y los demás vienen en 0. El porcentaje de IVA es configurable desde el panel admin del carrito y queda como snapshot en cada pedido. Cuando necesiten manejar productos con alícuotas diferenciadas (10,5%, 27%, exento) se extiende el modelo.</li>
</ul>

<p style="margin-top: 30px; color: #6b7280; font-size: 9.5px; border-top: 1px solid #e5e7eb; padding-top: 10px;">
    <strong>Soporte:</strong> ante cualquier duda o problema, contactar al equipo técnico de Todotex.
</p>

</body>
</html>
