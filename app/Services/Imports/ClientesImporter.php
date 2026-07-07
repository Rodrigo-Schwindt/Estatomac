<?php

namespace App\Services\Imports;

use App\Models\Catalog\Canal;
use App\Models\Catalog\Ciudad;
use App\Models\Catalog\CondicionIva;
use App\Models\Catalog\CondicionVenta;
use App\Models\Catalog\RubroCliente;
use App\Models\Catalog\TipoLista;
use App\Models\Catalog\TipoOperacion;
use App\Models\Catalog\Transporte;
use App\Models\Cliente;
use App\Models\Vendedor;
use RuntimeException;

class ClientesImporter
{
    /**
     * Reemplaza la tabla de clientes (y populariza canales) con las filas dadas.
     * Las claves esperadas (normalizadas): codigo, cliente|nombre, nombre_fantasia, direccion|domicilio,
     * telefono, celular, codigo_postal, ciudades|localidad, provincia, email, whatsapp, condicion_iva,
     * cuit, activo|activosn, condicion_venta|condicion_de_ventas, tipo_operacion|tipo_operaciones,
     * descuento, transporte, vendedor|vendedor_id, rubro_cliente|rubro_clientes, tipo_lista|tipos_de_listas,
     * canal, descuento_canal.
     */
    public function __invoke(array $rows): int
    {
        // Mapa de vendedores por código externo o nombre para resolver FK
        $vendedoresPorCodigo = Vendedor::pluck('id', 'codigo_externo')->filter()->all();
        $vendedoresPorNombre = Vendedor::pluck('id', 'nombre')->all();

        // Vaciar clientes antes de reemplazar (rollback automático si falla algo)
        Cliente::query()->delete();

        $procesadas = 0;
        foreach ($rows as $idx => $row) {
            $codigo = $this->str($row, 'codigo');
            if (!$codigo) {
                throw new RuntimeException("Fila " . ($idx + 2) . ": falta 'codigo' del cliente.");
            }

            $nombre = $this->str($row, 'cliente') ?? $this->str($row, 'nombre');
            if (!$nombre) {
                throw new RuntimeException("Fila " . ($idx + 2) . ": falta 'nombre' o 'cliente'.");
            }

            $canalNombre   = $this->str($row, 'canal');
            $descCanal     = $this->num($row, 'descuento_canal');
            if ($canalNombre) {
                Canal::firstOrCreate(['canal' => $canalNombre], ['descuento_canal' => $descCanal ?? 0]);
            }

            $localidad   = $this->str($row, 'ciudades') ?? $this->str($row, 'localidad');
            $provincia   = $this->str($row, 'provincia');
            $codigoPostal = $this->str($row, 'codigo_postal');
            if ($localidad) {
                Ciudad::firstOrCreate(
                    ['nombre' => $localidad, 'provincia' => $provincia],
                    ['codigo_postal' => $codigoPostal]
                );
            }

            $condicionIva   = $this->str($row, 'condicion_iva') ?? $this->str($row, 'condicion_de_iva');
            if ($condicionIva)   CondicionIva::firstOrCreate(['nombre' => $condicionIva]);

            $condicionVenta = $this->str($row, 'condicion_venta') ?? $this->str($row, 'condicion_de_ventas');
            if ($condicionVenta) CondicionVenta::firstOrCreate(['nombre' => $condicionVenta]);

            $tipoOperacion  = $this->str($row, 'tipo_operacion') ?? $this->str($row, 'tipo_operaciones');
            if ($tipoOperacion)  TipoOperacion::firstOrCreate(['nombre' => $tipoOperacion]);

            $transporte     = $this->str($row, 'transporte');
            if ($transporte)     Transporte::firstOrCreate(['nombre' => $transporte]);

            $rubroCliente   = $this->str($row, 'rubro_cliente') ?? $this->str($row, 'rubro_clientes');
            if ($rubroCliente)   RubroCliente::firstOrCreate(['nombre' => $rubroCliente]);

            $tipoLista      = $this->str($row, 'tipo_lista') ?? $this->str($row, 'tipos_de_listas');
            if ($tipoLista)      TipoLista::firstOrCreate(['nombre' => $tipoLista]);

            $vendedorRaw = $this->str($row, 'vendedor') ?? $this->str($row, 'vendedor_id');
            $vendedorId  = $vendedorRaw
                ? ($vendedoresPorCodigo[$vendedorRaw] ?? $vendedoresPorNombre[$vendedorRaw] ?? null)
                : null;

            $activoRaw = $this->str($row, 'activo') ?? $this->str($row, 'activosn');
            $activo    = in_array(mb_strtoupper((string) $activoRaw), ['S', 'SI', 'Y', 'YES', '1', 'TRUE'], true);

            Cliente::create([
                'codigo'          => $codigo,
                'usuario'         => $this->str($row, 'usuario') ?? $codigo,
                'nombre'          => $nombre,
                'nombre_fantasia' => $this->str($row, 'nombre_fantasia') ?? $this->str($row, 'nombrefantasia') ?? $nombre,
                'email'           => $this->str($row, 'email'),
                'cuit'            => $this->str($row, 'cuit'),
                'condicion_iva'   => $condicionIva,
                'telefono'        => $this->str($row, 'telefono'),
                'celular'         => $this->str($row, 'celular'),
                'whatsapp'        => $this->str($row, 'whatsapp'),
                'domicilio'       => $this->str($row, 'direccion') ?? $this->str($row, 'domicilio'),
                'localidad'       => $localidad,
                'codigo_postal'   => $codigoPostal,
                'provincia'       => $provincia,
                'condicion_venta' => $condicionVenta,
                'tipo_operacion'  => $tipoOperacion,
                'descuento'       => $this->num($row, 'descuento') ?? 0,
                'transporte'      => $transporte,
                'vendedor_id'     => $vendedorId,
                'rubro_cliente'   => $rubroCliente,
                'tipo_lista'      => $tipoLista,
                'canal'           => $canalNombre,
                'descuento_canal' => $descCanal ?? 0,
                'activo'          => $activo,
            ]);

            $procesadas++;
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
