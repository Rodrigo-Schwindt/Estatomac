<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendedor;

class Pedido extends Model
{
    use HasFactory;

    public const ESTADO_PENDIENTE = 'PENDIENTE';
    public const ESTADO_ENVIADO   = 'ENVIADO';
    public const ESTADO_ANULADO   = 'ANULADO';

    protected $fillable = [
        'numero_pedido',
        'cliente_id',
        'vendedor_id',
        'vendedor_operativo_id',
        'anulado',
        'fecha_anulado',
        'enviado_erp_at',
        'erp_ip',
        'forma_pago',
        'forma_entrega',
        'costo_entrega',
        'mensaje',
        'archivo_path',
        'archivo_nombre',
        'subtotal_sin_descuento',
        'descuentos',
        'porcentaje_descuento',
        'subtotal',
        'porcentaje_iva',
        'iva',
        'total',
        'fecha_compra',
        'fecha_entrega',
        'entregado',
        'fecha_entregado',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_entrega' => 'date',
        'fecha_entregado' => 'date',
        'entregado'    => 'boolean',
        'anulado'      => 'boolean',
        'fecha_anulado' => 'datetime',
        'enviado_erp_at' => 'datetime',
        'subtotal_sin_descuento' => 'decimal:2',
        'descuentos' => 'decimal:2',
        'porcentaje_descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'porcentaje_iva' => 'decimal:2',
        'iva' => 'decimal:2',
        'costo_entrega' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }

    public function vendedorOperativo()
    {
        return $this->belongsTo(Vendedor::class, 'vendedor_operativo_id');
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public static function generarNumeroPedido()
    {
        $ultimoPedido = static::latest('id')->first();
        $numero = $ultimoPedido ? intval(substr($ultimoPedido->numero_pedido, 0)) + 1 : 1;
        return str_pad($numero, 8, '0', STR_PAD_LEFT);
    }

    public function marcarComoEntregado()
    {
        $this->update([
            'entregado' => true,
            'fecha_entregado' => now(),
        ]);
    }

    public function getEstadoAttribute(): string
    {
        if ($this->anulado) {
            return self::ESTADO_ANULADO;
        }
        if ($this->enviado_erp_at !== null) {
            return self::ESTADO_ENVIADO;
        }
        return self::ESTADO_PENDIENTE;
    }

    public function getEstadoBadgeAttribute(): array
    {
        return match ($this->estado) {
            self::ESTADO_ANULADO => ['label' => 'ANULADO', 'class' => 'bg-red-100 text-red-700'],
            self::ESTADO_ENVIADO => ['label' => 'ENVIADO', 'class' => 'bg-green-100 text-green-700'],
            default              => ['label' => 'PENDIENTE', 'class' => 'bg-gray-100 text-gray-600'],
        };
    }

    public function scopePendientes($query)
    {
        return $query->where('anulado', false)->whereNull('enviado_erp_at');
    }

    public function scopeEnviados($query)
    {
        return $query->where('anulado', false)->whereNotNull('enviado_erp_at');
    }

    public function scopeAnulados($query)
    {
        return $query->where('anulado', true);
    }
}
