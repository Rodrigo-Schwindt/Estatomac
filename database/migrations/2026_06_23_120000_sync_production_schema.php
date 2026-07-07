<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración de sincronización para producción.
 *
 * Aplica de forma IDEMPOTENTE todo lo que pueda faltar:
 * - Si la tabla/columna ya existe, la salta.
 * - Si no existe, la crea.
 *
 * Es segura para correr varias veces. No toca datos existentes.
 *
 * Útil cuando el registro de migrations del server quedó desincronizado.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->crearTablasNuevas();
        $this->agregarColumnasClientes();
        $this->agregarColumnasProductos();
        $this->agregarColumnasCanales();
        $this->agregarColumnasFamilias();
        $this->agregarColumnasPrecios();
        $this->normalizarTipos();
    }

    public function down(): void
    {
        // No hacemos rollback — esta migration es solo para sincronizar.
    }

    private function crearTablasNuevas(): void
    {
        if (!Schema::hasTable('b2b_password_resets')) {
            Schema::create('b2b_password_resets', function (Blueprint $table) {
                $table->id();
                $table->string('tipo', 20);
                $table->string('email', 120);
                $table->string('token', 80);
                $table->timestamp('created_at')->nullable();
                $table->unique(['tipo', 'email']);
                $table->index('token');
            });
        }

        if (!Schema::hasTable('bulk_import_logs')) {
            Schema::create('bulk_import_logs', function (Blueprint $table) {
                $table->id();
                $table->string('proceso', 60);
                $table->string('archivo', 255)->nullable();
                $table->string('estado', 20);
                $table->integer('filas_procesadas')->default(0);
                $table->longText('mensaje')->nullable();
                $table->json('detalle_errores')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
                $table->index(['proceso', 'created_at']);
            });
        }

        if (!Schema::hasTable('listas_precios_importes')) {
            Schema::create('listas_precios_importes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pk_externa')->unique();
                $table->unsignedBigInteger('listas_precios_pk_externa')->index();
                $table->unsignedBigInteger('productos_pk_externa')->index();
                $table->decimal('precio_paquete', 18, 2)->default(0);
                $table->decimal('precio_unitario', 18, 2)->default(0);
                $table->decimal('precio_kilo', 18, 2)->default(0);
                $table->decimal('costo_producto', 18, 2)->default(0);
                $table->string('bulto', 120)->nullable();
                $table->timestamps();
                $table->unique(['listas_precios_pk_externa', 'productos_pk_externa'], 'lpi_lista_producto_uniq');
            });
        } elseif (!Schema::hasColumn('listas_precios_importes', 'bulto')) {
            Schema::table('listas_precios_importes', function (Blueprint $table) {
                $table->string('bulto', 120)->nullable()->after('costo_producto');
            });
        }

        if (!Schema::hasTable('erp_familias')) {
            Schema::create('erp_familias', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pk_externa')->unique();
                $table->string('nombre', 120);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('erp_subfamilias')) {
            Schema::create('erp_subfamilias', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pk_externa')->unique();
                $table->string('nombre', 120);
                $table->unsignedBigInteger('familias_pk_externa')->nullable()->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mapeos_erp_categoria')) {
            Schema::create('mapeos_erp_categoria', function (Blueprint $table) {
                $table->id();
                $table->string('entidad_tipo', 20);
                $table->unsignedBigInteger('entidad_pk_externa');
                $table->unsignedBigInteger('familia_id')->nullable();
                $table->unsignedBigInteger('categoria_id')->nullable();
                $table->timestamps();
                $table->unique(['entidad_tipo', 'entidad_pk_externa'], 'mapeo_erp_uniq');
                $table->index('familia_id');
                $table->index('categoria_id');
            });
        }
    }

    private function agregarColumnasClientes(): void
    {
        if (!Schema::hasTable('clientes')) return;

        $columnas = [
            'pk_externa'                     => fn ($t) => $t->unsignedBigInteger('pk_externa')->nullable()->index(),
            'direccion_entregas'             => fn ($t) => $t->string('direccion_entregas', 255)->nullable(),
            'horario_apertura'               => fn ($t) => $t->string('horario_apertura', 255)->nullable(),
            'horario_cierre'                 => fn ($t) => $t->string('horario_cierre', 255)->nullable(),
            'contactos'                      => fn ($t) => $t->string('contactos', 255)->nullable(),
            'observaciones_erp'              => fn ($t) => $t->text('observaciones_erp')->nullable(),
            'saldo'                          => fn ($t) => $t->decimal('saldo', 18, 2)->nullable(),
            'saldo_inicial'                  => fn ($t) => $t->decimal('saldo_inicial', 18, 2)->nullable(),
            'especial_sn'                    => fn ($t) => $t->string('especial_sn', 1)->nullable(),
            'nomina'                         => fn ($t) => $t->string('nomina', 30)->nullable(),
            'transporte_alternativo'         => fn ($t) => $t->string('transporte_alternativo', 60)->nullable(),
            'alerta'                         => fn ($t) => $t->string('alerta', 255)->nullable(),
            'radio'                          => fn ($t) => $t->string('radio', 60)->nullable(),
            'ciudades_pk_externa'            => fn ($t) => $t->unsignedInteger('ciudades_pk_externa')->nullable(),
            'condiciones_iva_pk_externa'     => fn ($t) => $t->unsignedInteger('condiciones_iva_pk_externa')->nullable(),
            'vendedores_pk_externa'          => fn ($t) => $t->unsignedInteger('vendedores_pk_externa')->nullable()->index(),
            'canales_pk_externa'             => fn ($t) => $t->unsignedInteger('canales_pk_externa')->nullable()->index(),
            'condiciones_ventas_pk_externa'  => fn ($t) => $t->unsignedInteger('condiciones_ventas_pk_externa')->nullable(),
            'tipos_operaciones_pk_externa'   => fn ($t) => $t->unsignedInteger('tipos_operaciones_pk_externa')->nullable(),
            'transportes_pk_externa'         => fn ($t) => $t->unsignedInteger('transportes_pk_externa')->nullable(),
            'rubros_clientes_pk_externa'     => fn ($t) => $t->unsignedInteger('rubros_clientes_pk_externa')->nullable(),
            'tipos_de_listas_pk_externa'     => fn ($t) => $t->unsignedInteger('tipos_de_listas_pk_externa')->nullable(),
        ];

        foreach ($columnas as $nombre => $definicion) {
            if (!Schema::hasColumn('clientes', $nombre)) {
                Schema::table('clientes', function (Blueprint $t) use ($definicion) {
                    $definicion($t);
                });
            }
        }
    }

    private function agregarColumnasProductos(): void
    {
        if (!Schema::hasTable('productos_todotex')) return;

        $columnas = [
            'pk_externa'             => fn ($t) => $t->unsignedBigInteger('pk_externa')->nullable()->index(),
            'familias_pk_externa'    => fn ($t) => $t->unsignedBigInteger('familias_pk_externa')->nullable()->index(),
            'subfamilias_pk_externa' => fn ($t) => $t->unsignedBigInteger('subfamilias_pk_externa')->nullable()->index(),
        ];

        foreach ($columnas as $nombre => $definicion) {
            if (!Schema::hasColumn('productos_todotex', $nombre)) {
                Schema::table('productos_todotex', function (Blueprint $t) use ($definicion) {
                    $definicion($t);
                });
            }
        }
    }

    private function agregarColumnasCanales(): void
    {
        if (!Schema::hasTable('canales')) return;

        $columnas = [
            'pk_externa'      => fn ($t) => $t->unsignedBigInteger('pk_externa')->nullable()->index(),
            'supervisor_pct'  => fn ($t) => $t->decimal('supervisor_pct', 18, 2)->nullable(),
            'vendedor_pct'    => fn ($t) => $t->decimal('vendedor_pct', 18, 2)->nullable(),
            'supervisor1_pct' => fn ($t) => $t->decimal('supervisor1_pct', 18, 2)->nullable(),
            'supervisor2_pct' => fn ($t) => $t->decimal('supervisor2_pct', 18, 2)->nullable(),
        ];

        foreach ($columnas as $nombre => $definicion) {
            if (!Schema::hasColumn('canales', $nombre)) {
                Schema::table('canales', function (Blueprint $t) use ($definicion) {
                    $definicion($t);
                });
            }
        }
    }

    private function agregarColumnasFamilias(): void
    {
        if (!Schema::hasTable('familias')) return;

        if (!Schema::hasColumn('familias', 'pk_externa')) {
            Schema::table('familias', function (Blueprint $t) {
                $t->unsignedBigInteger('pk_externa')->nullable()->index();
            });
        }
        if (!Schema::hasColumn('familias', 'imagen')) {
            Schema::table('familias', function (Blueprint $t) {
                $t->string('imagen', 255)->nullable();
            });
        }
    }

    private function agregarColumnasPrecios(): void
    {
        if (!Schema::hasTable('precios')) return;

        if (!Schema::hasColumn('precios', 'pk_externa')) {
            Schema::table('precios', function (Blueprint $t) {
                $t->unsignedBigInteger('pk_externa')->nullable()->index();
            });
        }
    }

    private function normalizarTipos(): void
    {
        // password en clientes: debe ser NULLABLE (el importer del ERP no trae passwords)
        if (Schema::hasColumn('clientes', 'password')) {
            try {
                Schema::table('clientes', function (Blueprint $t) {
                    $t->string('password')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // Si ya es nullable, doctrine puede tirar warning. Lo ignoramos.
            }
        }

        // mensaje en bulk_import_logs: longtext (los stack traces superan TEXT 64KB)
        if (Schema::hasColumn('bulk_import_logs', 'mensaje')) {
            try {
                Schema::table('bulk_import_logs', function (Blueprint $t) {
                    $t->longText('mensaje')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // Si ya es longtext, no pasa nada.
            }
        }
    }
};
