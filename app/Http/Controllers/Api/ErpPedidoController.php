<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Vendedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class ErpPedidoController extends Controller
{
    private const HARD_LIMIT = 500;
    private const DEFAULT_LIMIT = 100;

    public const ESTADO_PENDIENTE = 0;
    public const ESTADO_ENVIADO   = 1;
    public const ESTADO_ANULADO   = 2;

    public function health(Request $request): JsonResponse|Response
    {
        return $this->respond($request, [
            'status'   => 'ok',
            'time'     => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
        ], 'Health');
    }

    /**
     * GET /api/erp/pedidos
     * Listado read-only de todos los pedidos. No marca nada.
     */
    public function index(Request $request): JsonResponse|Response
    {
        $request->validate([
            'estado' => 'nullable|in:pendiente,enviado,anulado',
            'desde'  => 'nullable|date',
            'hasta'  => 'nullable|date',
            'limit'  => 'nullable|integer|min:1|max:' . self::HARD_LIMIT,
            'page'   => 'nullable|integer|min:1',
            'format' => 'nullable|in:json,xml',
        ]);

        $limit = (int) $request->input('limit', self::DEFAULT_LIMIT);
        $page  = (int) $request->input('page', 1);
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $estado = $request->input('estado');

        $query = Pedido::query()
            ->with(['cliente.vendedor', 'vendedor', 'vendedorOperativo', 'items.producto'])
            ->when($desde, fn ($q) => $q->whereDate('fecha_compra', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha_compra', '<=', $hasta))
            ->when($estado === 'pendiente', fn ($q) => $q->where('anulado', false)->whereNull('enviado_erp_at'))
            ->when($estado === 'enviado',   fn ($q) => $q->where('anulado', false)->whereNotNull('enviado_erp_at'))
            ->when($estado === 'anulado',   fn ($q) => $q->where('anulado', true))
            ->orderByDesc('created_at');

        $total = $query->count();
        $pedidos = $query->forPage($page, $limit)->get();

        return $this->respond($request, [
            'Pedidos' => $pedidos->map(fn (Pedido $p) => $this->serializePedido($p))->values()->all(),
            'Meta'    => [
                'Total'       => $total,
                'Limit'       => $limit,
                'Page'        => $page,
                'LastPage'    => $total > 0 ? (int) ceil($total / $limit) : 0,
                'GeneratedAt' => now()->toIso8601String(),
                'Note'        => 'Read-only endpoint. No marca pedidos como enviados.',
            ],
        ], 'PedidosResponse');
    }

    /**
     * GET /api/erp/pedidos/pendientes
     * Devuelve PENDIENTES y los marca como ENVIADO en una sola operación atómica.
     */
    public function pendientes(Request $request): JsonResponse|Response
    {
        $request->validate([
            'limit'  => 'nullable|integer|min:1|max:' . self::HARD_LIMIT,
            'desde'  => 'nullable|date',
            'hasta'  => 'nullable|date',
            'format' => 'nullable|in:json,xml',
        ]);

        $limit = (int) $request->input('limit', self::DEFAULT_LIMIT);
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $resultado = DB::transaction(function () use ($limit, $desde, $hasta, $request) {
            $pedidos = Pedido::query()
                ->with(['cliente.vendedor', 'vendedor', 'vendedorOperativo', 'items.producto'])
                ->where('anulado', false)
                ->whereNull('enviado_erp_at')
                // Solo pedidos cuyo cliente está vinculado al ERP (tiene pk_externa).
                // Los pedidos de clientes huérfanos quedan retenidos del lado del B2B
                // hasta que el cliente sea dado de alta en el ERP, así no rompemos las
                // reglas de integridad del lado de ellos al recibir ClientesPK vacío.
                ->whereHas('cliente', fn ($q) => $q->whereNotNull('pk_externa'))
                ->when($desde, fn ($q) => $q->whereDate('fecha_compra', '>=', $desde))
                ->when($hasta, fn ($q) => $q->whereDate('fecha_compra', '<=', $hasta))
                ->orderBy('created_at')
                ->limit($limit)
                ->lockForUpdate()
                ->get();

            if ($pedidos->isEmpty()) {
                return ['pedidos' => $pedidos, 'marcados' => 0];
            }

            $ahora = now();
            Pedido::whereIn('id', $pedidos->pluck('id'))->update([
                'enviado_erp_at' => $ahora,
                'erp_ip'         => $request->ip(),
            ]);

            $pedidos->each(function (Pedido $p) use ($ahora, $request) {
                $p->enviado_erp_at = $ahora;
                $p->erp_ip         = $request->ip();
            });

            return ['pedidos' => $pedidos, 'marcados' => $pedidos->count()];
        });

        return $this->respond($request, [
            'Pedidos' => $resultado['pedidos']->map(fn (Pedido $p) => $this->serializePedido($p))->values()->all(),
            'Meta'    => [
                'Total'              => $resultado['marcados'],
                'Limit'              => $limit,
                'MarcadosComoEnviado' => $resultado['marcados'],
                'GeneratedAt'        => now()->toIso8601String(),
            ],
        ], 'PedidosResponse');
    }

    /**
     * GET /api/erp/pedidos/{numero}
     * Lee un pedido por número. Read-only.
     */
    public function show(Request $request, string $numero): JsonResponse|Response
    {
        $request->validate(['format' => 'nullable|in:json,xml']);

        $pedido = Pedido::with(['cliente.vendedor', 'vendedor', 'vendedorOperativo', 'items.producto'])
            ->where('numero_pedido', $numero)
            ->first();

        if (!$pedido) {
            return $this->respond($request, [
                'Error'   => 'not_found',
                'Message' => "Pedido {$numero} no existe.",
            ], 'Error', 404);
        }

        return $this->respond($request, [
            'Pedido' => $this->serializePedido($pedido),
        ], 'PedidoResponse');
    }

    /** =============================================================
     *  Serialización al schema Nota_Pedido_Cabecera + Nota_Pedido_Detalle
     *  ============================================================= */

    private function serializePedido(Pedido $p): array
    {
        return [
            'Cabecera' => $this->serializeCabecera($p),
            'Detalle'  => [
                'Item' => $p->items->map(fn ($i) => $this->serializeDetalle($i, $p))->values()->all(),
            ],
        ];
    }

    private function serializeCabecera(Pedido $p): array
    {
        $cliente           = $p->cliente;
        $vendedor          = $p->vendedor;
        $vendedorOperativo = $p->vendedorOperativo;

        // Fallback de vendedor: si el pedido no tiene vendedor asignado (caso típico:
        // el cliente compró sin login de vendedor), usamos el vendedor que el ERP
        // tiene asignado al cliente. Así VendedoresPK SIEMPRE viaja poblado al ERP.
        // Preferimos pk_externa (el PersonalPK del ERP) y caemos a codigo_externo
        // como compat con vendedores cargados a mano.
        $vendedorPkParaXml = $vendedor?->pk_externa
            ?? $vendedor?->codigo_externo
            ?? $cliente?->vendedor?->pk_externa
            ?? $cliente?->vendedor?->codigo_externo;

        // Vendedor operativo: solo se incluye cuando el vendedor logueado opera en
        // nombre de otro (opera_como). Es el que físicamente cargó el pedido.
        $vendedorOperativoPk = $vendedorOperativo?->pk_externa
            ?? $vendedorOperativo?->codigo_externo;

        // Origen del pedido (siempre presente):
        //  - CLIENTE             → el cliente compró sin login de vendedor.
        //  - VENDEDOR            → un vendedor titular se logueó y cargó el pedido.
        //  - VENDEDOR_OPERATIVO  → un vendedor cargó el pedido en nombre de otro (opera_como).
        $origen = $this->origenPedido($p);

        $cabecera = [
            'NPCabeceraPK'          => (int) $p->id,
            'NPNumero'              => (int) ltrim($p->numero_pedido, '0') ?: 0,
            'NPEstado'              => $this->mapEstado($p),
            'Fecha'                 => optional($p->fecha_compra)->toDateString(),
            'Hora'                  => optional($p->created_at)->format('H:i'),
            // Todas las *PK son SIEMPRE ints (o null si no hay match con el ERP).
            // Nunca se manda el nombre/descripción — eso rompe el contrato del ERP.
            'ClientesPK'            => $this->pkInt($cliente?->pk_externa),
            'VendedoresPK'          => $this->pkInt($vendedorPkParaXml),
            'CanalesPK'             => $this->pkInt($cliente?->canales_pk_externa),
            'CondicionesVentasPK'   => $this->pkInt($cliente?->condiciones_ventas_pk_externa),
            'Origen'                => $origen,
            'Observaciones'         => $p->mensaje ?? '',
            'ImporteTotal'          => $this->money($p->total),
            'Neto1'                 => $this->money($p->subtotal),
            'Neto2'                 => 0.00,
            'Neto3'                 => 0.00,
            'IVA1'                  => $this->money($p->iva),
            'IVA2'                  => 0.00,
            'IVA3'                  => 0.00,
            'DescuentoCliente'      => $this->money($p->descuentos),
        ];

        // Tag opcional: solo aparece cuando hay vendedor operativo distinto del titular.
        $operativoPkInt = $this->pkInt($vendedorOperativoPk);
        if ($operativoPkInt !== null) {
            $cabecera['VendedorOperativoPK'] = $operativoPkInt;
        }

        return $cabecera;
    }

    /**
     * Deriva el origen del pedido a partir de quién lo cargó.
     * Lo decide la presencia/ausencia de vendedor_id y vendedor_operativo_id
     * en el pedido, no el fallback que viaja en VendedoresPK.
     */
    private function origenPedido(Pedido $p): string
    {
        if ($p->vendedor_operativo_id !== null) {
            return 'VENDEDOR_OPERATIVO';
        }
        if ($p->vendedor_id !== null) {
            return 'VENDEDOR';
        }
        return 'CLIENTE';
    }

    private function serializeDetalle($item, Pedido $p): array
    {
        $producto = $item->producto;

        return [
            'NPDetallePK'        => (int) $item->id,
            'NPCabeceraPK'       => (int) $p->id,
            // ProductosPK siempre es int del ERP (o null si el producto no está vinculado)
            'ProductosPK'        => $this->pkInt($producto?->pk_externa),
            'Cantidad'           => $this->money($item->cantidad),
            'Importe'            => $this->money($item->subtotal),
            'DescuentoProducto'  => $this->money($item->descuento_base_porcentaje),
            'Descuento'          => $this->money($item->descuento_personalizado_porcentaje),
            'Observaciones'      => '',
            'IVA1'               => $this->money($item->subtotal * ($p->porcentaje_iva / 100)),
            'IVA2'               => 0.00,
            'IVA3'               => 0.00,
        ];
    }

    /**
     * Asegura que las *PK siempre sean int o null — nunca un string descriptivo.
     * Garantiza el contrato: "si hay match con el ERP, mando su PK; si no, mando null".
     */
    private function pkInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        return is_numeric($value) ? (int) $value : null;
    }

    private function mapEstado(Pedido $p): int
    {
        if ($p->anulado) return self::ESTADO_ANULADO;
        if ($p->enviado_erp_at !== null) return self::ESTADO_ENVIADO;
        return self::ESTADO_PENDIENTE;
    }

    private function money($value): float
    {
        return round((float) ($value ?? 0), 2);
    }

    /** =============================================================
     *  Format negotiation (JSON / XML)
     *  ============================================================= */

    private function respond(Request $request, array $payload, string $rootElement = 'Response', int $status = 200): JsonResponse|Response
    {
        $format = strtolower((string) $request->input('format', 'json'));

        if ($format === 'xml') {
            return response($this->toXml($payload, $rootElement), $status)
                ->header('Content-Type', 'application/xml; charset=UTF-8');
        }

        return response()->json($payload, $status);
    }

    private function toXml(array $data, string $rootElement): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $rootElement . '/>');
        $this->arrayToXml($data, $xml);

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    private function arrayToXml(array $data, SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            $key = $this->sanitizeXmlKey($key);

            if (is_array($value)) {
                if ($this->isList($value)) {
                    // Si la clave es plural (termina en "s"), envolvemos en un container
                    // con la clave plural, y cada item lleva la versión singular.
                    $itemName = $this->singularize($key);
                    if ($itemName !== $key) {
                        $container = $xml->addChild($key);
                        $this->appendListItems($container, $itemName, $value);
                    } else {
                        $this->appendListItems($xml, $key, $value);
                    }
                } else {
                    $child = $xml->addChild($key);
                    $this->arrayToXml($value, $child);
                }
            } else {
                $xml->addChild($key, $this->xmlSafe($value));
            }
        }
    }

    private function appendListItems(SimpleXMLElement $parent, string $itemName, array $list): void
    {
        foreach ($list as $item) {
            $child = $parent->addChild($itemName);
            if (is_array($item)) {
                $this->arrayToXml($item, $child);
            } else {
                $child[0] = $this->xmlSafe($item);
            }
        }
    }

    private function singularize(string $word): string
    {
        if (mb_strlen($word) > 1 && str_ends_with($word, 's')) {
            return mb_substr($word, 0, -1);
        }
        return $word;
    }

    private function isList(array $arr): bool
    {
        if ($arr === []) return false;
        return array_keys($arr) === range(0, count($arr) - 1);
    }

    private function sanitizeXmlKey(string $key): string
    {
        if (is_numeric($key[0] ?? '')) {
            $key = 'n' . $key;
        }
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $key);
    }

    private function xmlSafe($value): string
    {
        if ($value === null) return '';
        if (is_bool($value)) return $value ? 'true' : 'false';
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
