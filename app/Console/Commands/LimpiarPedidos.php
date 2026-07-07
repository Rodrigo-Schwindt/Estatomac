<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Borra pedidos del B2B (y sus items por cascade).
 *
 * Pensado para limpiar pedidos de prueba durante la integración con el ERP,
 * así el endpoint /api/erp/pedidos vuelve a estado limpio. NO toca clientes,
 * productos, ni ningún otro dato.
 *
 * Ejemplos:
 *   php artisan pedidos:limpiar --dry-run
 *   php artisan pedidos:limpiar
 *   php artisan pedidos:limpiar --hasta=2026-06-26
 *   php artisan pedidos:limpiar --solo-no-enviados
 *   php artisan pedidos:limpiar --force          (sin confirmación)
 */
class LimpiarPedidos extends Command
{
    protected $signature = 'pedidos:limpiar
        {--dry-run            : Muestra cuántos se borrarían sin tocar nada}
        {--hasta=             : Solo borra pedidos con fecha_compra <= AAAA-MM-DD}
        {--desde=             : Solo borra pedidos con fecha_compra >= AAAA-MM-DD}
        {--solo-no-enviados   : Solo borra pedidos que aún no fueron enviados al ERP}
        {--solo-enviados      : Solo borra pedidos que ya fueron enviados al ERP}
        {--reset-autoincrement : Reinicia el autoincrement a 1 (solo si quedaron 0 pedidos)}
        {--force              : Omite la confirmación}';

    protected $description = 'Borra pedidos del B2B (y sus items). Útil para limpiar pruebas de la API ERP.';

    public function handle(): int
    {
        $hasta            = $this->option('hasta');
        $desde            = $this->option('desde');
        $soloNoEnviados   = (bool) $this->option('solo-no-enviados');
        $soloEnviados     = (bool) $this->option('solo-enviados');
        $dryRun           = (bool) $this->option('dry-run');
        $force            = (bool) $this->option('force');
        $resetAutoInc     = (bool) $this->option('reset-autoincrement');

        if ($soloNoEnviados && $soloEnviados) {
            $this->error('No podés combinar --solo-no-enviados con --solo-enviados.');
            return self::FAILURE;
        }

        $query = Pedido::query()
            ->when($hasta, fn ($q) => $q->whereDate('fecha_compra', '<=', $hasta))
            ->when($desde, fn ($q) => $q->whereDate('fecha_compra', '>=', $desde))
            ->when($soloNoEnviados, fn ($q) => $q->whereNull('enviado_erp_at'))
            ->when($soloEnviados, fn ($q) => $q->whereNotNull('enviado_erp_at'));

        $total = $query->count();

        if ($total === 0) {
            $this->info('No hay pedidos que coincidan con el filtro. Nada para borrar.');
            return self::SUCCESS;
        }

        $this->warn("Pedidos a borrar: {$total}");
        $sample = (clone $query)->with('cliente')->latest('id')->limit(5)->get();
        $this->line('Muestra (más recientes):');
        foreach ($sample as $p) {
            $cli   = $p->cliente?->nombre ?? '(sin cliente)';
            $fecha = optional($p->fecha_compra)->toDateString() ?? '-';
            $estado = $p->enviado_erp_at ? 'ENVIADO' : ($p->anulado ? 'ANULADO' : 'PENDIENTE');
            $this->line("  - #{$p->numero_pedido} (id={$p->id}) fecha={$fecha} estado={$estado} cliente=\"{$cli}\"");
        }

        if ($dryRun) {
            $this->info('Dry-run: no se borró nada.');
            return self::SUCCESS;
        }

        if (!$force && !$this->confirm("¿Borrar definitivamente {$total} pedido(s) y todos sus items?", false)) {
            $this->info('Cancelado.');
            return self::SUCCESS;
        }

        $borrados = 0;
        DB::transaction(function () use ($query, &$borrados) {
            // Los items se borran solos por la FK con onDelete('cascade').
            $borrados = $query->delete();
        });

        $this->info("✔ {$borrados} pedido(s) borrados.");

        if ($resetAutoInc) {
            $quedan = Pedido::query()->count();
            if ($quedan === 0) {
                DB::statement('ALTER TABLE pedidos AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE pedido_items AUTO_INCREMENT = 1');
                $this->info('✔ Autoincrement reiniciado a 1 (pedidos y pedido_items).');
            } else {
                $this->warn("No se reseteó el autoincrement: quedan {$quedan} pedido(s) en la tabla.");
            }
        }

        return self::SUCCESS;
    }
}
