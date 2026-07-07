<?php

namespace App\Console\Commands;

use App\Models\Catalog\Canal;
use App\Models\Catalog\Ciudad;
use App\Models\Catalog\CondicionIva;
use App\Models\Catalog\CondicionVenta;
use App\Models\Catalog\RubroCliente;
use App\Models\Catalog\TipoLista;
use App\Models\Catalog\TipoOperacion;
use App\Models\Catalog\Transporte;
use App\Models\Cliente;
use Illuminate\Console\Command;

class SyncCatalogsFromClientes extends Command
{
    protected $signature = 'catalogs:sync-from-clientes';
    protected $description = 'Pobla las tablas catálogo con los valores únicos presentes en clientes.';

    public function handle(): int
    {
        $this->info('Sincronizando catálogos…');

        $mapeo = [
            'condicion_iva'   => [CondicionIva::class,   'nombre'],
            'condicion_venta' => [CondicionVenta::class, 'nombre'],
            'tipo_operacion'  => [TipoOperacion::class,  'nombre'],
            'transporte'      => [Transporte::class,     'nombre'],
            'rubro_cliente'   => [RubroCliente::class,   'nombre'],
            'tipo_lista'      => [TipoLista::class,      'nombre'],
        ];

        foreach ($mapeo as $columna => [$model, $campo]) {
            $valores = Cliente::query()
                ->whereNotNull($columna)
                ->where($columna, '!=', '')
                ->distinct()
                ->pluck($columna);

            foreach ($valores as $v) {
                $model::firstOrCreate([$campo => $v]);
            }

            $this->line(" {$columna} → " . $valores->count() . ' valores procesados.');
        }

        // Canales: nombre + descuento promedio
        $canales = Cliente::query()
            ->selectRaw('canal, AVG(descuento_canal) AS prom')
            ->whereNotNull('canal')
            ->where('canal', '!=', '')
            ->groupBy('canal')
            ->get();
        foreach ($canales as $row) {
            Canal::firstOrCreate(['canal' => $row->canal], ['descuento_canal' => round($row->prom ?? 0, 2)]);
        }
        $this->line(' canal → ' . $canales->count() . ' valores procesados.');

        // Ciudades: nombre + provincia
        $ciudades = Cliente::query()
            ->select('localidad', 'provincia', 'codigo_postal')
            ->whereNotNull('localidad')
            ->where('localidad', '!=', '')
            ->distinct()
            ->get();
        foreach ($ciudades as $row) {
            Ciudad::firstOrCreate(
                ['nombre' => $row->localidad, 'provincia' => $row->provincia],
                ['codigo_postal' => $row->codigo_postal]
            );
        }
        $this->line(' ciudades → ' . $ciudades->count() . ' valores procesados.');

        $this->info('Listo.');
        return self::SUCCESS;
    }
}
