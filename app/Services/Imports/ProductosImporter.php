<?php

namespace App\Services\Imports;

use App\Models\Precio;
use App\Models\ProductoTodotex;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class ProductosImporter
{
    public ?int $listaPrecioId         = null;
    public ?UploadedFile $listaArchivo = null;
    public ?int $listaNumero           = null;
    public ?string $listaTitulo        = null;
    public ?string $listaFechaDesde    = null;
    public bool $listaVigente          = false;

    /**
     * Reemplaza los productos. Si se provee información de la lista, crea/actualiza la cabecera Precio
     * y, si vigente=true, la activa como única vigente.
     *
     * Claves esperadas (normalizadas): codigo, titulo|nombre|descripcion, presentacion,
     * precio_paquete, precio_unitario, precio_kilo|precio_kg, descuento, bulto, bulto_cantidad,
     * codigo_color, nombre_color, porcentaje_aumento.
     */
    public function __invoke(array $rows): int
    {
        // Vaciar productos antes de reemplazar
        ProductoTodotex::query()->delete();

        $procesadas = 0;
        foreach ($rows as $idx => $row) {
            $codigo = $this->str($row, 'codigo');
            if (!$codigo) {
                throw new RuntimeException("Fila " . ($idx + 2) . ": falta 'codigo' del producto.");
            }
            $titulo = $this->str($row, 'titulo') ?? $this->str($row, 'nombre') ?? $this->str($row, 'descripcion');
            if (!$titulo) {
                throw new RuntimeException("Fila " . ($idx + 2) . ": falta título/nombre del producto.");
            }

            ProductoTodotex::create([
                'codigo'             => $codigo,
                'titulo'             => $titulo,
                'descripcion'        => $this->str($row, 'descripcion'),
                'presentacion'       => $this->str($row, 'presentacion'),
                'precio_paquete'     => $this->num($row, 'precio_paquete')  ?? 0,
                'precio_unitario'    => $this->num($row, 'precio_unitario') ?? 0,
                'precio_kg'          => $this->num($row, 'precio_kilo') ?? $this->num($row, 'precio_kg') ?? 0,
                'porcentaje_aumento' => $this->num($row, 'porcentaje_aumento') ?? 0,
                'descuento'          => $this->num($row, 'descuento') ?? 0,
                'bulto'              => $this->str($row, 'bulto'),
                'bulto_cantidad'     => (int) ($this->num($row, 'bulto_cantidad') ?? 0) ?: null,
                'codigo_color'       => $this->str($row, 'codigo_color'),
                'nombre_color'       => $this->str($row, 'nombre_color'),
                'visible'            => true,
            ]);

            $procesadas++;
        }

        // Lista de Precios cabecera (sin importes detallados — diferido por decisión del cliente)
        if ($this->listaTitulo || $this->listaNumero || $this->listaArchivo) {
            $data = [
                'numero'      => $this->listaNumero,
                'title'       => $this->listaTitulo ?? ('Lista ' . ($this->listaNumero ?? now()->format('Y-m-d'))),
                'fecha_desde' => $this->listaFechaDesde ?: now()->toDateString(),
                'vigente_sn'  => $this->listaVigente,
            ];
            if ($this->listaArchivo) {
                $data['archivo'] = $this->listaArchivo->store('precios', 'public');
            }

            $precio = $this->listaPrecioId
                ? tap(Precio::find($this->listaPrecioId))->fill($data)
                : new Precio($data);
            if (!$this->listaPrecioId && !isset($data['archivo'])) {
                $data['archivo'] = ''; // archivo opcional en este flujo
                $precio->archivo = '';
            }
            $precio->save();

            if ($this->listaVigente) {
                $precio->activar();
            }
        }

        return $procesadas;
    }

    private function str(array $row, string $key): ?string
    {
        $v = $row[$key] ?? null;
        if ($v === null) return null;
        $v = trim((string) $v);
        return $v === '' ? null : $v;
    }

    private function num(array $row, string $key): ?float
    {
        $v = $this->str($row, $key);
        if ($v === null) return null;
        $v = str_replace(',', '.', $v);
        return is_numeric($v) ? (float) $v : null;
    }
}
