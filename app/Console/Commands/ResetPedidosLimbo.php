<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use Illuminate\Console\Command;

/**
 * Revierte el estado ENVIADO de pedidos cuyo cliente NO tiene pk_externa
 * (clientes huérfanos que se registraron por la web pero no están en el ERP).
 *
 * Esos pedidos fueron consumidos por el endpoint /pendientes y quedaron marcados
 * como enviados, pero el ERP los rechazó por inconsistencia (ClientesPK vacío).
 * Quedan en limbo: enviados en el B2B, nunca procesados por el ERP.
 *
 * Este comando los devuelve al estado PENDIENTE para que vuelvan al flujo normal
 * una vez que sus clientes se den de alta en el ERP.
 */
class ResetPedidosLimbo extends Command
{
    protected $signature = 'pedidos:reset-limbo {--dry-run : Solo muestra cuántos serían reseteados sin tocar nada}';

    protected $description = 'Devuelve a estado PENDIENTE los pedidos enviados al ERP cuyo cliente no tiene pk_externa.';

    public function handle(): int
    {
        $query = Pedido::query()
            ->whereNotNull('enviado_erp_at')
            ->where('anulado', false)
            ->where(function ($q) {
                $q->whereDoesntHave('cliente')
                  ->orWhereHas('cliente', fn ($cq) => $cq->whereNull('pk_externa'));
            });

        $total = $query->count();

        if ($total === 0) {
            $this->info('No hay pedidos en limbo. Nada para hacer.');
            return self::SUCCESS;
        }

        $this->warn("Pedidos en limbo (enviados al ERP pero con cliente sin pk_externa): {$total}");

        $sample = (clone $query)->with('cliente')->limit(5)->get();
        $this->line('Muestra:');
        foreach ($sample as $p) {
            $cli = $p->cliente?->nombre ?? '(sin cliente)';
            $this->line("  - pedido #{$p->numero_pedido} (id={$p->id}) cliente=\"{$cli}\" enviado_at={$p->enviado_erp_at}");
        }

        if ($this->option('dry-run')) {
            $this->info('Dry-run: no se reseteó nada.');
            return self::SUCCESS;
        }

        if (!$this->confirm("¿Resetear {$total} pedido(s) a PENDIENTE?", true)) {
            $this->info('Cancelado.');
            return self::SUCCESS;
        }

        $afectados = $query->update([
            'enviado_erp_at' => null,
            'erp_ip'         => null,
        ]);

        $this->info("✔ {$afectados} pedido(s) devueltos a PENDIENTE.");
        return self::SUCCESS;
    }
}
