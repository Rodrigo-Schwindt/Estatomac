<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoTodotex extends Model
{
    use HasFactory;

    protected $table = 'productos_todotex';

    public const COLOR_NAMES = [
        '4001' => 'amarillo patito',
        '4002' => 'amarillo sol',
        '4003' => 'amarillo fluo',
        '4004' => 'amarillo mostaza',
        '4005' => 'amarillo oro',
        '4006' => 'azul francia',
        '4007' => 'azul marino',
        '4012' => 'amarillo ferrari',
        '4013' => 'orquidea',
        '4014' => 'gris ceniza',
        '4015' => 'turquesa báltico',
        '4017' => 'blanco óptico',
        '4018' => 'bordó',
        '4020' => 'celeste bandera',
        '4021' => 'celeste bebé',
        '4022' => 'celeste turqueza',
        '4023' => 'fucsia',
        '4025' => 'gris rústico',
        '4026' => 'gris topo',
        '4027' => 'lila / lavanda',
        '4028' => 'magenta',
        '4029' => 'marrón africano',
        '4030' => 'marrón dulce de leche',
        '4032' => 'naranja',
        '4033' => 'naranja fluo',
        '4034' => 'salmón',
        '4035' => 'yute rubio',
        '4036' => 'yute pardo o rojizo',
        '4038' => 'natural arena',
        '4039' => 'negro',
        '4040' => 'rojo',
        '4041' => 'rosa bebé',
        '4042' => 'rosa dior',
        '4044' => 'rosa nude',
        '4052' => 'verde musgo',
        '4055' => 'verde agua',
        '4056' => 'verde beneton',
        '4057' => 'verde botella',
        '4059' => 'verde fluo',
        '4061' => 'verde manzana',
        '4062' => 'verde navidad',
        '4063' => 'púrpura',
        '4064' => 'violeta',
        '4079' => 'marrón chocolate',
        '4080' => 'cobre',
        '4081' => 'crudo',
        '4084' => 'argentina',
        '4116' => 'rosa carmesí',
        '4117' => 'vainilla',
        '4118' => 'canela',
        '4120' => 'camel',
        '4121' => 'piel',
        '4122' => 'visón',
        '4123' => 'lima',
        '4124' => 'menta',
        '4125' => 'mandarina',
    ];

    protected $fillable = [
        'titulo',
        'orden',
        'destacado',
        'visible',
        'descripcion',
        'codigo',
        'color_todotex_id',
        'codigo_color',
        'nombre_color',
        'presentacion',
        'precio_paquete',
        'precio_unitario',
        'precio_kg',
        'porcentaje_aumento',
        'descuento',
        'bulto',
        'bulto_cantidad',
        'simbolo_id',
    ];

    protected $casts = [
        'destacado' => 'boolean',
        'visible' => 'boolean',
        'precio_paquete' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'precio_kg' => 'decimal:2',
        'porcentaje_aumento' => 'decimal:2',
        'descuento' => 'decimal:2',
        'bulto_cantidad' => 'integer',
        'color_todotex_id' => 'integer',
    ];

    protected static ?array $resolvedColorNames = null;

    public static function normalizeBulto(?string $bulto): ?string
    {
        $bulto = trim((string) $bulto);

        return $bulto !== '' ? $bulto : null;
    }

    public static function deriveBultoCantidad(?string $bulto): int
    {
        $bulto = trim((string) $bulto);

        if ($bulto === '') {
            return 1;
        }

        if (preg_match('/(\d+(?:[.,]\d+)?)/', $bulto, $matches)) {
            return max(1, (int) floatval(str_replace(',', '.', $matches[1])));
        }

        return 1;
    }

    public static function deriveColorData(?string $codigo): array
    {
        $codigo = trim((string) $codigo);

        if ($codigo === '' || strlen($codigo) <= 4) {
            return [
                'codigo_color' => null,
                'nombre_color' => null,
            ];
        }

        $codigoColor = substr($codigo, -4);
        $colorNames = self::getColorNamesMap();

        if (!array_key_exists($codigoColor, $colorNames)) {
            return [
                'codigo_color' => null,
                'nombre_color' => null,
            ];
        }

        return [
            'codigo_color' => $codigoColor,
            'nombre_color' => $colorNames[$codigoColor],
        ];
    }

    protected static function getColorNamesMap(): array
    {
        if (self::$resolvedColorNames !== null) {
            return self::$resolvedColorNames;
        }

        $dbColors = ColorTodotex::query()
            ->whereNotNull('codigo_color')
            ->whereNotNull('titulo')
            ->pluck('titulo', 'codigo_color')
            ->map(fn ($titulo) => trim((string) $titulo))
            ->filter()
            ->all();

        self::$resolvedColorNames = array_replace(self::COLOR_NAMES, $dbColors);

        return self::$resolvedColorNames;
    }

    public function categorias()
    {
        return $this->belongsToMany(CategoriaTodotex::class, 'categoria_producto', 'producto_id', 'categoria_id');
    }

    public function simbolo()
    {
        return $this->belongsTo(Simbolo::class, 'simbolo_id');
    }

    public function color()
    {
        return $this->belongsTo(ColorTodotex::class, 'color_todotex_id');
    }

    public function gallery()
    {
        return $this->hasMany(ProductoGallery::class, 'producto_id');
    }
}
