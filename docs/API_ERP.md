# API B2B Todotex → ERP

Documentación de los endpoints REST que expone el sistema B2B de Todotex para que el ERP consuma los pedidos generados por clientes y vendedores.

- **Base URL:** `https://todotex.osole.com.ar/api/erp`
- **Formato:** JSON (UTF-8)
- **Método:** todos los endpoints son `GET`
- **Autenticación:** *momentáneamente DESACTIVADA* para que puedan hacer pruebas prácticas. Cuando se defina y pase a producción se va a requerir un header `X-API-Key: <key>` que se va a entregar por canal seguro.

---

## Endpoints disponibles

| # | Endpoint | Efecto | Para qué sirve |
|---|---|---|---|
| 1 | `GET /health` | nada | Verificar que la API está arriba |
| 2 | `GET /pedidos` | **read-only** | Listar todos los pedidos con filtros (debug / auditoría) |
| 3 | `GET /pedidos/pendientes` | **marca como ENVIADO** | Ciclo normal: trae pedidos nuevos y los marca como enviados |
| 4 | `GET /pedidos/{numero}` | read-only | Consultar un pedido puntual |

---

## 1) Health — verificar que la API responde

**URL:** https://todotex.osole.com.ar/api/erp/health

Útil antes de empezar el ciclo de polling o como monitor de uptime. No retorna pedidos, no toca la base.

### Ejemplo de respuesta (200)

```json
{
  "status": "ok",
  "time": "2026-05-27T09:00:00-03:00",
  "timezone": "America/Argentina/Buenos_Aires"
}
```

---

## 2) Listado completo — ver todos los pedidos (lectura)

**URL:** https://todotex.osole.com.ar/api/erp/pedidos

Devuelve **todos** los pedidos independientemente de su estado. Es **solo lectura** — no marca nada, no cambia estados. Sirve para debug, reconciliación o auditoría.

### Filtros opcionales

| Parámetro | Valores | Default | Ejemplo |
|---|---|---|---|
| `estado` | `pendiente` \| `enviado` \| `anulado` | (todos) | `?estado=enviado` |
| `desde` | fecha `YYYY-MM-DD` | sin filtro | `?desde=2026-05-01` |
| `hasta` | fecha `YYYY-MM-DD` | sin filtro | `?hasta=2026-05-31` |
| `limit` | 1 a 500 | 100 | `?limit=10` |
| `page` | 1, 2, 3... | 1 | `?page=2` |

### Ejemplos

- https://todotex.osole.com.ar/api/erp/pedidos
- https://todotex.osole.com.ar/api/erp/pedidos?limit=5
- https://todotex.osole.com.ar/api/erp/pedidos?estado=enviado
- https://todotex.osole.com.ar/api/erp/pedidos?desde=2026-05-01&hasta=2026-05-31

### Ejemplo de respuesta (200)

```json
{
  "pedidos": [
    {
      "numero_pedido": "00000003",
      "estado": "ENVIADO",
      "fecha_compra": "2026-05-12",
      "fecha_entrega": null,
      "created_at": "2026-05-12T14:15:53+00:00",
      "enviado_erp_at": "2026-05-26T18:05:37+00:00",
      "cliente": {
        "id": 514,
        "codigo": "141",
        "nombre": "Aut.Americano del Embalaje S.R.L.",
        "nombre_fantasia": "A.A.E",
        "cuit": "30-66390033-8",
        "cuil": null,
        "condicion_iva": "Resp. Inscripto",
        "condicion_venta": "Cuenta Corriente",
        "tipo_operacion": "All Black",
        "canal": "Imposible",
        "descuento_canal": 30,
        "descuento": 0,
        "rubro_cliente": "Big Paper",
        "tipo_lista": "Grupo 1",
        "transporte": "Tres Lomas",
        "domicilio": "Av Mitre 1474",
        "localidad": "Florida",
        "codigo_postal": null,
        "provincia": "G.B.A.",
        "telefono": "4760-9509 - 4760-9509",
        "celular": null,
        "whatsapp": null,
        "email": "novoaalexis@gmail.com"
      },
      "vendedor": {
        "id": 5,
        "codigo": 5,
        "nombre": "Gabriel Degiamma",
        "email": "gabriel.degiamma@todotexhilos.com",
        "comision": 0,
        "opera_como": null
      },
      "items": [
        {
          "codigo_producto": "316",
          "nombre_producto": "<p>Soga de Sisal Todotex de Ø 16 mm</p>",
          "cantidad": 1,
          "precio_unitario": 85284.41,
          "descuento_unitario": 25585.32,
          "descuento_base_pct": 0,
          "descuento_extra_pct": 0,
          "subtotal": 59699.09,
          "producto": {
            "id": 12,
            "codigo": "316",
            "titulo": "Soga de Sisal Todotex de Ø 16 mm",
            "presentacion": "<p>Rollo de 50 mts.</p>",
            "codigo_color": null,
            "nombre_color": null,
            "bulto": "1 Rollo.",
            "bulto_cantidad": 1
          }
        }
      ],
      "forma_pago": "contado",
      "forma_entrega": "entrega_1",
      "costo_entrega": 0,
      "mensaje": null,
      "totales": {
        "subtotal_sin_descuento": 85284.41,
        "descuentos": 31555.23,
        "porcentaje_descuento": 37,
        "subtotal": 53729.18,
        "porcentaje_iva": 21,
        "iva": 11283.13,
        "costo_entrega": 0,
        "total": 65012.31
      }
    }
  ],
  "meta": {
    "total": 3,
    "limit": 1,
    "page": 1,
    "last_page": 3,
    "filtros": [],
    "generated_at": "2026-05-26T18:36:24+00:00",
    "note": "Read-only endpoint. No marca pedidos como enviados."
  }
}
```

---

## 3) Pedidos pendientes — el endpoint del ciclo normal ⚠️

**URL:** https://todotex.osole.com.ar/api/erp/pedidos/pendientes

> ⚠️ **Comportamiento clave:** este endpoint **devuelve los pedidos en estado PENDIENTE y al mismo tiempo los marca como ENVIADO** en una sola operación atómica. Si lo recargás una segunda vez, vas a ver array vacío porque los pedidos ya cambiaron de estado.
>
> Este es el endpoint que el ERP tiene que consumir en su ciclo de trabajo (ej: cada 5 minutos). Una vez consumidos, esos pedidos quedan en "purgatorio" del ERP — no se pueden anular más desde el B2B y no vuelven a aparecer acá.

### Filtros opcionales

| Parámetro | Valores | Default | Ejemplo |
|---|---|---|---|
| `limit` | 1 a 500 | 100 | `?limit=50` |
| `desde` | fecha `YYYY-MM-DD` | sin filtro | `?desde=2026-05-01` |
| `hasta` | fecha `YYYY-MM-DD` | sin filtro | `?hasta=2026-05-31` |

### Ejemplo de respuesta cuando hay pedidos para enviar (200)

```json
{
  "pedidos": [
    {
      "numero_pedido": "00000005",
      "estado": "ENVIADO",
      "fecha_compra": "2026-05-26",
      "fecha_entrega": null,
      "created_at": "2026-05-26T18:20:11+00:00",
      "enviado_erp_at": "2026-05-26T18:36:24+00:00",
      "cliente": {
        "id": 514,
        "codigo": "141",
        "nombre": "Aut.Americano del Embalaje S.R.L.",
        "nombre_fantasia": "A.A.E",
        "cuit": "30-66390033-8",
        "cuil": null,
        "condicion_iva": "Resp. Inscripto",
        "condicion_venta": "Cuenta Corriente",
        "tipo_operacion": "All Black",
        "canal": "Imposible",
        "descuento_canal": 30,
        "descuento": 0,
        "rubro_cliente": "Big Paper",
        "tipo_lista": "Grupo 1",
        "transporte": "Tres Lomas",
        "domicilio": "Av Mitre 1474",
        "localidad": "Florida",
        "codigo_postal": null,
        "provincia": "G.B.A.",
        "telefono": "4760-9509 - 4760-9509",
        "celular": null,
        "whatsapp": null,
        "email": "novoaalexis@gmail.com"
      },
      "vendedor": {
        "id": 5,
        "codigo": 5,
        "nombre": "Gabriel Degiamma",
        "email": "gabriel.degiamma@todotexhilos.com",
        "comision": 0,
        "opera_como": null
      },
      "items": [
        {
          "codigo_producto": "316",
          "nombre_producto": "<p>Soga de Sisal Todotex de Ø 16 mm</p>",
          "cantidad": 2,
          "precio_unitario": 85284.41,
          "descuento_unitario": 0,
          "descuento_base_pct": 0,
          "descuento_extra_pct": 0,
          "subtotal": 170568.82,
          "producto": {
            "id": 12,
            "codigo": "316",
            "titulo": "Soga de Sisal Todotex de Ø 16 mm",
            "presentacion": "<p>Rollo de 50 mts.</p>",
            "codigo_color": null,
            "nombre_color": null,
            "bulto": "1 Rollo.",
            "bulto_cantidad": 1
          }
        }
      ],
      "forma_pago": "contado",
      "forma_entrega": "entrega_1",
      "costo_entrega": 0,
      "mensaje": null,
      "totales": {
        "subtotal_sin_descuento": 170568.82,
        "descuentos": 0,
        "porcentaje_descuento": 0,
        "subtotal": 170568.82,
        "porcentaje_iva": 21,
        "iva": 35819.45,
        "costo_entrega": 0,
        "total": 206388.27
      }
    }
  ],
  "meta": {
    "total": 1,
    "limit": 100,
    "marcados_como_enviado": 1,
    "generated_at": "2026-05-26T18:36:24+00:00"
  }
}
```

### Ejemplo de respuesta cuando no hay pendientes (200)

Esto es lo que verían al recargar inmediatamente después del primer GET — los pedidos ya pasaron a ENVIADO:

```json
{
  "pedidos": [],
  "meta": {
    "total": 0,
    "limit": 100,
    "marcados_como_enviado": 0,
    "generated_at": "2026-05-26T18:36:25+00:00"
  }
}
```

---

## 4) Pedido por número — consultar uno puntual

**URL:** `https://todotex.osole.com.ar/api/erp/pedidos/{numero}`

Ejemplo: https://todotex.osole.com.ar/api/erp/pedidos/00000003

Devuelve **un solo pedido** identificado por su número. Es solo lectura — no importa el estado del pedido (PENDIENTE / ENVIADO / ANULADO), lo retorna igual y no toca nada. Útil para reconciliación o consultas manuales.

### Ejemplo de respuesta (200)

```json
{
  "pedido": {
    "numero_pedido": "00000003",
    "estado": "ENVIADO",
    "fecha_compra": "2026-05-12",
    "fecha_entrega": null,
    "created_at": "2026-05-12T14:15:53+00:00",
    "enviado_erp_at": "2026-05-26T18:05:37+00:00",
    "cliente": {
      "id": 514,
      "codigo": "141",
      "nombre": "Aut.Americano del Embalaje S.R.L.",
      "nombre_fantasia": "A.A.E",
      "cuit": "30-66390033-8",
      "cuil": null,
      "condicion_iva": "Resp. Inscripto",
      "condicion_venta": "Cuenta Corriente",
      "tipo_operacion": "All Black",
      "canal": "Imposible",
      "descuento_canal": 30,
      "descuento": 0,
      "rubro_cliente": "Big Paper",
      "tipo_lista": "Grupo 1",
      "transporte": "Tres Lomas",
      "domicilio": "Av Mitre 1474",
      "localidad": "Florida",
      "codigo_postal": null,
      "provincia": "G.B.A.",
      "telefono": "4760-9509 - 4760-9509",
      "celular": null,
      "whatsapp": null,
      "email": "novoaalexis@gmail.com"
    },
    "vendedor": {
      "id": 5,
      "codigo": 5,
      "nombre": "Gabriel Degiamma",
      "email": "gabriel.degiamma@todotexhilos.com",
      "comision": 0,
      "opera_como": null
    },
    "items": [
      {
        "codigo_producto": "316",
        "nombre_producto": "<p>Soga de Sisal Todotex de Ø 16 mm</p>",
        "cantidad": 1,
        "precio_unitario": 85284.41,
        "descuento_unitario": 25585.32,
        "descuento_base_pct": 0,
        "descuento_extra_pct": 0,
        "subtotal": 59699.09,
        "producto": {
          "id": 12,
          "codigo": "316",
          "titulo": "Soga de Sisal Todotex de Ø 16 mm",
          "presentacion": "<p>Rollo de 50 mts.</p>",
          "codigo_color": null,
          "nombre_color": null,
          "bulto": "1 Rollo.",
          "bulto_cantidad": 1
        }
      }
    ],
    "forma_pago": "contado",
    "forma_entrega": "entrega_1",
    "costo_entrega": 0,
    "mensaje": null,
    "totales": {
      "subtotal_sin_descuento": 85284.41,
      "descuentos": 31555.23,
      "porcentaje_descuento": 37,
      "subtotal": 53729.18,
      "porcentaje_iva": 21,
      "iva": 11283.13,
      "costo_entrega": 0,
      "total": 65012.31
    }
  }
}
```

### Ejemplo de respuesta cuando el número no existe (404)

```json
{
  "error": "not_found",
  "message": "Pedido 99999999 no existe."
}
```

---

## Diccionario de campos

### `pedido`

| Campo | Tipo | Descripción |
|---|---|---|
| `numero_pedido` | string | Identificador del pedido, formato `00000000` (8 dígitos con padding) |
| `estado` | string | `PENDIENTE`, `ENVIADO` o `ANULADO` |
| `fecha_compra` | date | Fecha en que el cliente/vendedor armó el pedido |
| `fecha_entrega` | date \| null | Fecha de entrega prevista (puede ser null) |
| `created_at` | ISO 8601 | Timestamp exacto de creación |
| `enviado_erp_at` | ISO 8601 \| null | Timestamp en que el ERP consumió el pedido. `null` si todavía está PENDIENTE |
| `cliente` | object | Datos completos del cliente (ver abajo) |
| `vendedor` | object | Datos del vendedor que cargó el pedido (ver abajo) |
| `items` | array | Productos del pedido (ver abajo) |
| `forma_pago` | string | Forma de pago elegida |
| `forma_entrega` | string | Forma de entrega elegida |
| `costo_entrega` | number | Costo asociado a la entrega |
| `mensaje` | string \| null | Observaciones que dejó quien armó el pedido |
| `totales` | object | Resumen de importes (ver abajo) |

### `cliente`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | int | PK interno del B2B |
| `codigo` | string | Código del cliente en el sistema de gestión |
| `nombre` | string | Razón social |
| `nombre_fantasia` | string | Nombre de fantasía |
| `cuit` | string | CUIT formato `XX-XXXXXXXX-X` |
| `cuil` | string \| null | CUIL si aplica |
| `condicion_iva` | string | Ej: "Resp. Inscripto", "Monotributo", "Consumidor Final" |
| `condicion_venta` | string | Ej: "Cuenta Corriente", "Contado" |
| `tipo_operacion` | string | Tipo de operación comercial |
| `canal` | string | Canal de venta |
| `descuento_canal` | number | Descuento porcentual asociado al canal |
| `descuento` | number | Descuento porcentual particular del cliente |
| `rubro_cliente` | string | Rubro del cliente |
| `tipo_lista` | string | Tipo de lista de precios que aplica |
| `transporte` | string | Transporte habitual del cliente |
| `domicilio` | string | Dirección de entrega |
| `localidad`, `provincia`, `codigo_postal` | string | Datos de localización |
| `telefono`, `celular`, `whatsapp`, `email` | string \| null | Contactos |

### `vendedor`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | int | PK interno del B2B |
| `codigo` | string | Código del vendedor en el sistema de gestión |
| `nombre` | string | Nombre completo |
| `email` | string \| null | Email del vendedor |
| `comision` | number | Porcentaje de comisión |
| `opera_como` | object \| null | Si este vendedor está operando temporalmente en nombre de otro (vacaciones, reemplazo), acá viene el vendedor titular. Si opera con sus propios clientes, es `null`. |

### `items[]`

| Campo | Tipo | Descripción |
|---|---|---|
| `codigo_producto` | string | Código del producto en el sistema de gestión |
| `nombre_producto` | string | Nombre/descripción al momento de la compra (puede contener HTML porque viene del editor) |
| `cantidad` | int | Cantidad pedida |
| `precio_unitario` | number | Precio unitario aplicado |
| `descuento_unitario` | number | Descuento absoluto aplicado por unidad |
| `descuento_base_pct` | number | Porcentaje de descuento base (canal + cliente) |
| `descuento_extra_pct` | number | Porcentaje de descuento adicional (si el vendedor lo aplicó manualmente) |
| `subtotal` | number | Subtotal del ítem (cantidad × precio neto) |
| `producto` | object | Datos vigentes del producto (titulo, presentación, color, bulto). Puede estar `null` si el producto fue eliminado del catálogo |

### `totales`

| Campo | Tipo | Descripción |
|---|---|---|
| `subtotal_sin_descuento` | number | Suma de items sin aplicar descuentos |
| `descuentos` | number | Total de descuentos aplicados |
| `porcentaje_descuento` | number | Porcentaje promedio de descuento |
| `subtotal` | number | Subtotal después de descuentos (antes de IVA) |
| `porcentaje_iva` | number | Alícuota de IVA aplicada (típicamente 21) |
| `iva` | number | Monto de IVA |
| `costo_entrega` | number | Costo de entrega sumado al total |
| `total` | number | Total final del pedido |

---

## Códigos HTTP

| Código | Cuándo |
|---|---|
| `200` | Operación exitosa (puede traer array vacío si no hay pedidos pendientes) |
| `401` | Falta el header `X-API-Key` o es inválido (solo cuando la auth esté activa) |
| `404` | El pedido solicitado no existe |
| `422` | Parámetro de query inválido (ej: `?limit=99999`) |
| `500` | Error interno del servidor — contactar a Soporte |

---

## Flujo recomendado del ciclo de trabajo del ERP

```
1. GET /health
   → si responde 200, seguir

2. GET /pedidos/pendientes
   → recibe N pedidos
   → todos quedan automáticamente marcados como ENVIADO en el B2B

3. Procesar los N pedidos en el ERP

4. Devolver al B2B la respuesta correspondiente por FTP
   (con número interno del ERP, status, etc.)

5. Esperar N minutos y volver al paso 1
```

Si en algún momento necesitan releer un pedido puntual sin afectar nada:
```
GET /pedidos/{numero}
```

Si necesitan revisar el estado general de todo:
```
GET /pedidos                              ← todos
GET /pedidos?estado=enviado               ← solo enviados
GET /pedidos?estado=pendiente             ← solo pendientes (sin marcar)
GET /pedidos?desde=2026-05-01             ← desde una fecha
```

---

## Notas finales

- **Charset:** las respuestas vienen en UTF-8. Los nombres de productos pueden contener tags HTML (`<p>`, etc.) porque vienen del editor del admin — el ERP debería limpiarlos si los necesita en plano (`strip_tags()` o equivalente).
- **Timezone:** todos los timestamps con hora vienen en ISO 8601 con offset.
- **Decimales:** todos los montos vienen como `number` JSON (no string), con 2 decimales.
- **Pedidos anulados:** una vez que un pedido pasa a ENVIADO, **no se puede anular más desde el B2B**. Solo los pedidos en estado PENDIENTE pueden ser anulados por el cliente o vendedor.
- **Idempotencia:** `GET /pedidos/pendientes` **no es idempotente** (cambia estado). Los otros 3 endpoints sí lo son.

---

**Soporte:** ante cualquier duda o problema, contactar al equipo técnico de Todotex.
