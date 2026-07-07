<?php

namespace App\Services\Imports\Erp;

use App\Models\Catalog\Canal;
use App\Models\Cliente;
use App\Models\Precio;
use App\Models\ProductoTodotex;
use App\Models\Vendedor;
use App\Models\Erp\ErpFamilia;
use App\Models\Erp\ErpSubfamilia;
use App\Models\Erp\ListaPrecioImporte;
use App\Models\Erp\MapeoErpCategoria;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Importer minimalista: solo trae del ERP lo que el B2B necesita para operar
 * sin pisar datos del front (imágenes, colores, categorización, descripciones B2B).
 *
 * Estrategia:
 *   - Canales, Clientes, ListasPrecios, ListasPreciosImportes: reemplazo total
 *     (son 100% del ERP, no hay datos del B2B que perder).
 *   - Productos: UPSERT por `codigo` — si el producto ya existe en el B2B,
 *     solo le agrega `pk_externa`. Si no existe, lo crea con datos mínimos.
 *     NO toca: nombre_color, codigo_color, color_todotex_id, imágenes (gallery),
 *     descripción, categorización (familia/categoría/rubro).
 *   - Familias, Subfamilias, Materiales, etc.: SE IGNORAN. No las necesitamos.
 *   - Sync post-import: actualiza precio_unitario/paquete/kg en productos
 *     desde la lista vigente, matcheando por `codigo` (funciona para productos
 *     viejos cargados manualmente y para los nuevos del ERP).
 */
class ErpZipImporter
{
    use ErpImportHelpers;

    /** @var array<string,int> Resumen de filas procesadas por archivo */
    public array $procesadas = [];

    /** Archivos del ZIP que ignoramos completamente. */
    private array $archivosIgnorados = [
        'Materiales.xlsx',
        'TiposDeProductos.xlsx',
        'Unidades.xlsx',
        'ProductosExclusividad.xlsx',
    ];

    public function importar(string $extractedDir): array
    {
        $extractedDir = rtrim($extractedDir, '/\\');
        $files = $this->resolveFiles($extractedDir);

        DB::transaction(function () use ($files) {
            $this->importarCanales($files['Canales']);
            // Personal va ANTES de Clientes — cuando importemos clientes vamos a resolver
            // vendedor_id contra la tabla vendedores recién actualizada.
            if (isset($files['Personal'])) {
                $this->importarPersonal($files['Personal']);
            }
            $this->importarClientes($files['Clientes']);
            // Importamos Familias/Subfamilias del ERP a tablas ocultas (no visibles al cliente).
            // Las usamos como referencia para que el admin defina mapeos.
            $this->importarErpFamilias($files['Familias']);
            $this->importarErpSubfamilias($files['Subfamilias']);
            $this->upsertProductos($files['Productos']);
            $this->importarListasPrecios($files['ListasPrecios']);
            $this->importarListasPreciosImportes($files['ListasPreciosImportes']);
            $this->extraerBultosDeOtrosDatos($files['ListasPreciosOtrosDatos']);
            $this->sincronizarPreciosDeListaVigente();
            // Aplicar mapeos: asignar familias/categorías B2B a productos según las reglas del admin.
            $this->aplicarMapeosACategorias();
        });

        $this->procesadas['IgnoradosNoUsados'] = count($this->archivosIgnorados);
        return $this->procesadas;
    }

    private function resolveFiles(string $dir): array
    {
        $necesarios = [
            'Canales'                 => 'Canales.xlsx',
            'Clientes'                => 'Clientes.xlsx',
            'Familias'                => 'Familias.xlsx',
            'Subfamilias'             => 'Subfamilias.xlsx',
            'Productos'               => 'Productos.xlsx',
            'ListasPrecios'           => 'ListasPrecios.xlsx',
            'ListasPreciosImportes'   => 'ListasPreciosImportes.xlsx',
            'ListasPreciosOtrosDatos' => 'ListasPreciosOtrosDatos.xlsx',
        ];

        // Personal.xlsx es OPCIONAL — apareció en una segunda versión del dump del ERP.
        // Si no viene, el import sigue funcionando sin actualizar vendedores.
        $opcionales = [
            'Personal' => 'Personal.xlsx',
        ];

        $resolved = [];
        foreach ($necesarios as $key => $filename) {
            $path = $dir . DIRECTORY_SEPARATOR . $filename;
            if (!is_file($path)) {
                throw new RuntimeException("Falta el archivo {$filename} en el ZIP.");
            }
            $resolved[$key] = $path;
        }
        foreach ($opcionales as $key => $filename) {
            $path = $dir . DIRECTORY_SEPARATOR . $filename;
            if (is_file($path)) {
                $resolved[$key] = $path;
            }
        }
        return $resolved;
    }

    /* ============================================================
     *  Familias y Subfamilias del ERP — tablas ocultas (referencia para el admin)
     * ============================================================ */
    private function importarErpFamilias(string $path): void
    {
        [, $rows] = $this->readExcel($path);
        ErpFamilia::query()->delete();
        $bulk = [];
        $now = now();
        foreach ($rows as $r) {
            $pk = $this->int('FamiliasPK', $r);
            $nombre = $this->str('Familia', $r, 120);
            if (!$pk || !$nombre) continue;
            $bulk[] = ['pk_externa' => $pk, 'nombre' => $nombre, 'created_at' => $now, 'updated_at' => $now];
        }
        if (!empty($bulk)) DB::table('erp_familias')->insert($bulk);
        $this->procesadas['ErpFamilias'] = count($bulk);
    }

    private function importarErpSubfamilias(string $path): void
    {
        [, $rows] = $this->readExcel($path);
        ErpSubfamilia::query()->delete();
        $bulk = [];
        $now = now();
        foreach ($rows as $r) {
            $pk = $this->int('SubfamiliasPK', $r);
            $nombre = $this->str('SubFamilia', $r, 120);
            if (!$pk || !$nombre) continue;
            $bulk[] = [
                'pk_externa'         => $pk,
                'nombre'             => $nombre,
                'familias_pk_externa' => $this->int('FamiliasPK', $r),
                'created_at'         => $now,
                'updated_at'         => $now,
            ];
        }
        if (!empty($bulk)) DB::table('erp_subfamilias')->insert($bulk);
        $this->procesadas['ErpSubfamilias'] = count($bulk);
    }

    /* ============================================================
     *  Canales — reemplazo total
     * ============================================================ */
    private function importarCanales(string $path): void
    {
        [, $rows] = $this->readExcel($path);
        Canal::query()->delete();
        foreach ($rows as $r) {
            $pk = $this->int('CanalesPK', $r);
            $canal = $this->str('Canal', $r, 60);
            if (!$pk || !$canal) continue;
            Canal::create([
                'pk_externa'      => $pk,
                'canal'           => $canal,
                'descuento_canal' => $this->num('Descuento', $r) ?? 0,
                'supervisor_pct'  => $this->num('Supervisor', $r),
                'vendedor_pct'    => $this->num('Vendedor', $r),
                'supervisor1_pct' => $this->num('Supervisor1', $r),
                'supervisor2_pct' => $this->num('Supervisor2', $r),
            ]);
        }
        $this->procesadas['Canales'] = count($rows);
    }

    /* ============================================================
     *  Personal del ERP → vendedores (UPSERT seguro)
     *
     *  Solo procesa filas con PermitirVenderSN = 'S'.
     *  Match cascade: pk_externa → codigo_externo → email → crear nuevo.
     *
     *  ERP siempre actualiza: nombre, comision, activo.
     *  B2B preserva (solo set si vacío): email, telefono, celular, whatsapp.
     *  NUNCA se toca: password, password_changed_at, remember_token, opera_como.
     *
     *  Al final, marca como inactivos a los vendedores B2B sin pk_externa
     *  (los que no aparecen en el Personal.xlsx).
     * ============================================================ */
    private function importarPersonal(string $path): void
    {
        [, $rows] = $this->readExcel($path);

        // Maps de matching
        $existentesPorPk     = Vendedor::whereNotNull('pk_externa')->pluck('id', 'pk_externa')->all();
        $existentesPorCodigo = Vendedor::whereNotNull('codigo_externo')->pluck('id', 'codigo_externo')->all();
        $existentesPorEmail  = Vendedor::whereNotNull('email')->where('email', '!=', '')->pluck('id', 'email')->all();

        $actualizados = 0;
        $creados = 0;
        $ignorados = 0;
        $pksProcesadas = [];

        foreach ($rows as $r) {
            $pk = $this->int('PersonalPK', $r);
            $nombre = $this->str('Nombre', $r, 60);
            if (!$pk || !$nombre) continue;

            // Solo importamos los que pueden vender
            if (!$this->sn('PermitirVenderSN', $r)) {
                $ignorados++;
                continue;
            }

            $email = $this->str('EMail', $r, 60);
            // Si el email es un placeholder tipo "-" o "0", lo descartamos
            if ($email !== null && (mb_strlen($email) < 5 || !str_contains($email, '@'))) {
                $email = null;
            }

            // Matching cascade
            $vendedorIdExistente = null;
            if (isset($existentesPorPk[$pk])) {
                $vendedorIdExistente = $existentesPorPk[$pk];
            } elseif (isset($existentesPorCodigo[(string) $pk])) {
                // El codigo_externo de mis vendedores cargados a mano puede ser el PersonalPK como string
                $vendedorIdExistente = $existentesPorCodigo[(string) $pk];
            } elseif ($email !== null && isset($existentesPorEmail[$email])) {
                $vendedorIdExistente = $existentesPorEmail[$email];
            }

            $erpAuthority = [
                'pk_externa'     => $pk,
                'codigo_externo' => (string) $pk,
                'nombre'         => $nombre,
                'activo'         => $this->sn('ActivoSN', $r),
                'comision'       => $this->num('Comision', $r) ?? 0,
                'updated_at'     => now(),
            ];

            $b2bPreserved = [
                'email'    => $email,
                'telefono' => $this->str('Telefono', $r, 60),
                'celular'  => $this->str('Celular', $r, 60),
                'whatsapp' => $this->str('Whatsapp', $r, 60),
            ];

            if ($vendedorIdExistente) {
                $existente = Vendedor::find($vendedorIdExistente);
                if (!$existente) continue;

                $update = $erpAuthority;
                foreach ($b2bPreserved as $field => $value) {
                    if (empty($existente->$field) && $value !== null) {
                        $update[$field] = $value;
                    }
                }
                DB::table('vendedores')->where('id', $vendedorIdExistente)->update($update);
                $pksProcesadas[] = $pk;
                $actualizados++;
            } else {
                DB::table('vendedores')->insert(array_merge($erpAuthority, $b2bPreserved, [
                    'created_at' => now(),
                ]));
                $pksProcesadas[] = $pk;
                $creados++;
            }
        }

        // Marcar como inactivos los vendedores B2B que no aparecen en el Personal.xlsx
        // (huérfanos del ERP). No los borramos para preservar histórico/relaciones.
        $marcadosInactivos = Vendedor::query()
            ->where('activo', true)
            ->where(function ($q) use ($pksProcesadas) {
                $q->whereNull('pk_externa')
                  ->orWhereNotIn('pk_externa', $pksProcesadas);
            })
            ->update(['activo' => false, 'updated_at' => now()]);

        $this->procesadas['Vendedores_actualizados']  = $actualizados;
        $this->procesadas['Vendedores_creados']       = $creados;
        $this->procesadas['Vendedores_no_vendedores'] = $ignorados;
        $this->procesadas['Vendedores_inactivados']   = $marcadosInactivos;
    }

    /* ============================================================
     *  Clientes — UPSERT seguro
     *
     *  Matching cascade: pk_externa → codigo → email → crear nuevo.
     *
     *  Campos que el ERP siempre actualiza (fuente de verdad comercial):
     *    nombre, nombre_fantasia, cuit, descuento, activo, saldo, especial_sn,
     *    nomina, transporte_alternativo, alerta, radio, observaciones_erp,
     *    vendedor_id, todas las *_pk_externa.
     *
     *  Campos que se preservan si ya tienen valor en B2B (solo se llenan si están vacíos):
     *    email, domicilio, telefono, celular, whatsapp, direccion_entregas,
     *    contactos, horario_apertura, horario_cierre, codigo_postal.
     *
     *  Campos que NUNCA se tocan (gestión propia del B2B):
     *    password, password_changed_at, remember_token, usuario.
     *
     *  Clientes que están SOLO en el B2B (no aparecen en el Excel) se preservan
     *  tal cual — son típicamente clientes que se autoregistraron desde la web.
     * ============================================================ */
    private function importarClientes(string $path): void
    {
        [, $rows] = $this->readExcel($path);
        // Map de PersonalPK (ERP) → id (B2B). Después de importarPersonal() esto trae los datos frescos.
        // Fallback a codigo_externo para compatibilidad con vendedores cargados a mano antes del integration.
        $vendedorMapPorPkExterna = Vendedor::whereNotNull('pk_externa')->pluck('id', 'pk_externa')->all();
        $vendedorMapPorCodigo    = Vendedor::whereNotNull('codigo_externo')->pluck('id', 'codigo_externo')->all();

        // Pre-cargar maps de matching: pk_externa, codigo, email
        $existentesPorPk     = Cliente::whereNotNull('pk_externa')->pluck('id', 'pk_externa')->all();
        $existentesPorCodigo = Cliente::whereNotNull('codigo')->pluck('id', 'codigo')->all();
        $existentesPorEmail  = Cliente::whereNotNull('email')->where('email', '!=', '')->pluck('id', 'email')->all();

        // Dedup en memoria del propio Excel
        $codigosVistos = [];
        $emailsVistos  = [];

        $now = now();
        $bulkInsert = [];
        $actualizados = 0;
        $creados = 0;
        $preservados = 0;

        foreach ($rows as $r) {
            $pk = $this->int('ClientesPK', $r);
            $codigo = $this->str('Codigo', $r, 10);
            $nombre = $this->str('Cliente', $r, 120);
            if (!$pk || !$nombre) continue;
            if ($codigo !== null && isset($codigosVistos[$codigo])) continue;
            if ($codigo !== null) $codigosVistos[$codigo] = true;

            $email = $this->str('EMail', $r, 120);
            $emailNormalizado = $email !== null ? mb_strtolower($email) : null;
            // Dedup de email dentro del Excel
            if ($emailNormalizado !== null && isset($emailsVistos[$emailNormalizado])) {
                $email = null;
                $emailNormalizado = null;
            }
            if ($emailNormalizado !== null) $emailsVistos[$emailNormalizado] = true;

            $vendedoresPkExterna = $this->int('VendedoresPK', $r);
            // Match cascade: primero por pk_externa (poblado por importarPersonal),
            // después por codigo_externo (vendedores cargados a mano que usaban el PersonalPK como código).
            $vendedorIdInterno = null;
            if ($vendedoresPkExterna) {
                $vendedorIdInterno = $vendedorMapPorPkExterna[$vendedoresPkExterna]
                    ?? $vendedorMapPorCodigo[(string) $vendedoresPkExterna]
                    ?? null;
            }

            // Matching cascade
            $clienteIdExistente = null;
            if (isset($existentesPorPk[$pk])) {
                $clienteIdExistente = $existentesPorPk[$pk];
            } elseif ($codigo !== null && isset($existentesPorCodigo[$codigo])) {
                $clienteIdExistente = $existentesPorCodigo[$codigo];
            } elseif ($email !== null && isset($existentesPorEmail[$email])) {
                $clienteIdExistente = $existentesPorEmail[$email];
            }

            // Campos siempre pisados por el ERP
            $erpAuthority = [
                'pk_externa'                    => $pk,
                'codigo'                        => $codigo,
                'nombre'                        => $nombre,
                'nombre_fantasia'               => $this->str('NombreFantasia', $r, 120) ?? $nombre,
                'cuit'                          => $this->str('CUIT', $r, 13),
                'descuento'                     => $this->num('Descuento', $r) ?? 0,
                'saldo'                         => $this->num('Saldo', $r),
                'saldo_inicial'                 => $this->num('SaldoInicial', $r),
                'especial_sn'                   => $this->snChar('EspecialSN', $r),
                'nomina'                        => $this->str('Nomina', $r, 30),
                'transporte_alternativo'        => $this->str('TransporteAlternativo', $r, 60),
                'alerta'                        => $this->str('Alerta', $r, 255),
                'radio'                         => $this->str('Radio', $r, 60),
                'observaciones_erp'             => $this->str('Observaciones', $r),
                'activo'                        => $this->sn('ActivoSN', $r),
                'vendedor_id'                   => $vendedorIdInterno,
                'ciudades_pk_externa'           => $this->int('CiudadesPK', $r),
                'condiciones_iva_pk_externa'    => $this->int('CondicionesIVAPK', $r),
                'vendedores_pk_externa'         => $vendedoresPkExterna,
                'canales_pk_externa'            => $this->int('CanalesPK', $r),
                'condiciones_ventas_pk_externa' => $this->int('CondicionesVentasPK', $r),
                'tipos_operaciones_pk_externa'  => $this->int('TiposOperacionesPK', $r),
                'transportes_pk_externa'        => $this->int('TransportesPK', $r),
                'rubros_clientes_pk_externa'    => $this->int('RubrosClientesPK', $r),
                'tipos_de_listas_pk_externa'    => $this->int('TiposDeListasPK', $r),
                'updated_at'                    => $now,
            ];

            // Campos preservados si ya tienen valor en B2B
            $b2bPreserved = [
                'email'              => $email,
                'domicilio'          => $this->str('Direccion', $r, 255),
                'direccion_entregas' => $this->str('DireccionEntregas', $r, 255),
                'horario_apertura'   => $this->str('HorarioApertura', $r, 255),
                'horario_cierre'     => $this->str('HorarioCierre', $r, 255),
                'telefono'           => $this->str('Telefono', $r, 120),
                'celular'            => $this->str('Celular', $r, 120),
                'whatsapp'           => $this->str('Whatsapp', $r, 120),
                'codigo_postal'      => $this->str('CodigoPostal', $r, 20),
                'contactos'          => $this->str('Contactos', $r, 255),
            ];

            if ($clienteIdExistente) {
                // UPDATE — pisamos campos ERP, mantenemos B2B preservados si ya tienen valor
                $existente = Cliente::find($clienteIdExistente);
                if (!$existente) continue;

                $update = $erpAuthority;
                foreach ($b2bPreserved as $field => $value) {
                    if (empty($existente->$field) && $value !== null) {
                        $update[$field] = $value;
                    }
                }

                // Si el cliente todavía no tenía codigo, no toleramos que codigo del ERP
                // colisione con otro registro existente.
                if ($codigo !== null && empty($existente->codigo)) {
                    // No hay riesgo — el codigo no estaba usado
                    $update['codigo'] = $codigo;
                } elseif ($existente->codigo && $existente->codigo !== $codigo) {
                    // Conflicto: el cliente B2B ya tenía otro codigo. Preservamos el del B2B.
                    unset($update['codigo']);
                }

                DB::table('clientes')->where('id', $clienteIdExistente)->update($update);
                $actualizados++;
            } else {
                // INSERT — cliente nuevo, asignamos usuario base y campos completos
                // Usuario NOT NULL: usamos codigo o pk como fallback
                $usuarioBase = $codigo ?? (string) $pk;
                $usuario = $usuarioBase;
                // Verificar colisión con un usuario existente (raro pero posible)
                if (Cliente::where('usuario', $usuario)->exists()) {
                    $usuario = $usuarioBase . '-' . $pk;
                }

                $bulkInsert[] = array_merge($erpAuthority, $b2bPreserved, [
                    'usuario'    => $usuario,
                    'created_at' => $now,
                ]);
                $creados++;
            }
        }

        if (!empty($bulkInsert)) {
            foreach (array_chunk($bulkInsert, 500) as $chunk) {
                DB::table('clientes')->insert($chunk);
            }
        }

        // Clientes huérfanos: los que están en B2B pero no aparecen en el Excel.
        // No los tocamos — son típicamente registros web autónomos.
        $preservados = Cliente::whereNull('pk_externa')->count();

        $this->procesadas['Clientes_actualizados'] = $actualizados;
        $this->procesadas['Clientes_creados']      = $creados;
        $this->procesadas['Clientes_preservados']  = $preservados;
    }

    /* ============================================================
     *  Productos — UPSERT por codigo (NO destructivo)
     *  Si existe en el B2B → solo agrega pk_externa (preserva imágenes/colores/categorías)
     *  Si no existe → crea con datos mínimos
     * ============================================================ */
    private function upsertProductos(string $path): void
    {
        [, $rows] = $this->readExcel($path);

        // Mapa codigoNormalizado → producto_id (normalizamos quitando espacios y
        // pasando a lowercase para que matchee "1201 4121" con "12014121")
        $existentes = ProductoTodotex::query()
            ->whereNotNull('codigo')
            ->get(['id', 'codigo'])
            ->mapWithKeys(fn ($p) => [$this->normalizarCodigo($p->codigo) => $p->id])
            ->all();

        $actualizados = 0;
        $creados = 0;
        $now = now();

        foreach ($rows as $r) {
            $pk = $this->int('ProductosPK', $r);
            $codigo = $this->str('CodigoProducto', $r, 100);
            $titulo = $this->str('Producto', $r, 255);
            if (!$pk || !$codigo) continue;

            // Buscamos match por código normalizado (sin espacios, lowercase).
            // Ej: "1201 4121" (ERP) matchea con "12014121" (B2B cargado a mano).
            $codigoNormalizado = $this->normalizarCodigo($codigo);

            // bulto_cantidad solo se actualiza si el ERP lo trae con un valor > 0.
            // Si viene vacío o 0, dejamos lo que ya estaba en el B2B.
            $paquetesPorBultos = $this->num('PaquetesPorBultos', $r);
            $bultoCantidad = ($paquetesPorBultos !== null && $paquetesPorBultos > 0)
                ? (int) $paquetesPorBultos
                : null;

            // FKs del ERP a familia/subfamilia — las guardamos siempre para poder aplicar mapeos
            $familiasPkExterna    = $this->int('FamiliasPK', $r);
            $subfamiliasPkExterna = $this->int('SubfamiliasPK', $r);

            if (isset($existentes[$codigoNormalizado])) {
                // Producto YA existe en el B2B → agregamos pk_externa, FKs ERP y bulto_cantidad.
                // NO tocamos titulo, descripción, color, imágenes, categorización, NI el codigo.
                $update = [
                    'pk_externa'             => $pk,
                    'familias_pk_externa'    => $familiasPkExterna,
                    'subfamilias_pk_externa' => $subfamiliasPkExterna,
                    'updated_at'             => $now,
                ];
                if ($bultoCantidad !== null) {
                    $update['bulto_cantidad'] = $bultoCantidad;
                }
                DB::table('productos_todotex')
                    ->where('id', $existentes[$codigoNormalizado])
                    ->update($update);
                $actualizados++;
            } else {
                // Producto NUEVO → lo creamos con datos mínimos del ERP.
                // SIEMPRE queda como OCULTO (visible=false). El admin tiene que
                // revisarlo, agregarle imágenes/categorías y activarlo a mano.
                // Así evitamos que aparezcan en la web sin curar.
                if (!$titulo) continue;
                DB::table('productos_todotex')->insert([
                    'pk_externa'             => $pk,
                    'familias_pk_externa'    => $familiasPkExterna,
                    'subfamilias_pk_externa' => $subfamiliasPkExterna,
                    'codigo'                 => $codigo,
                    'titulo'                 => $titulo,
                    'descripcion'            => $titulo,
                    'presentacion'           => $this->str('Presentacion', $r, 255),
                    'orden'                  => 0,
                    'visible'                => false,
                    'destacado'              => false,
                    'precio_paquete'         => 0,
                    'precio_unitario'        => 0,
                    'precio_kg'              => 0,
                    'porcentaje_aumento'     => 0,
                    'descuento'              => 0,
                    'bulto_cantidad'         => $bultoCantidad,
                    'created_at'             => $now,
                    'updated_at'             => $now,
                ]);
                $creados++;
            }
        }

        $this->procesadas['Productos_actualizados'] = $actualizados;
        $this->procesadas['Productos_creados']      = $creados;
    }

    /* ============================================================
     *  Listas de precios (cabecera)
     * ============================================================ */
    private function importarListasPrecios(string $path): void
    {
        [, $rows] = $this->readExcel($path);
        Precio::query()->delete();
        foreach ($rows as $r) {
            $pk = $this->int('ListasPreciosPK', $r);
            $nombre = $this->str('Nombre', $r, 50);
            if (!$pk || !$nombre) continue;
            Precio::create([
                'pk_externa'    => $pk,
                'numero'        => $this->int('Numero', $r),
                'title'         => $nombre,
                'fecha_desde'   => $this->date('FechaDesde', $r),
                'vigente_sn'    => $this->sn('VigenteSN', $r),
                'observaciones' => $this->str('Observaciones', $r),
                'archivo'       => '',
            ]);
        }
        $this->procesadas['ListasPrecios'] = count($rows);
    }

    /* ============================================================
     *  Listas de precios (importes por producto)
     * ============================================================ */
    private function importarListasPreciosImportes(string $path): void
    {
        [, $rows] = $this->readExcel($path);
        ListaPrecioImporte::query()->delete();
        $now = now();
        $bulk = [];
        foreach ($rows as $r) {
            $pk = $this->int('ListasPreciosImportesPK', $r);
            $listaPk = $this->int('ListasPreciosPK', $r);
            $productoPk = $this->int('ProductosPK', $r);
            if (!$pk || !$listaPk || !$productoPk) continue;

            $bulk[] = [
                'pk_externa'                => $pk,
                'listas_precios_pk_externa' => $listaPk,
                'productos_pk_externa'      => $productoPk,
                'precio_paquete'            => $this->num('PrecioPaquete', $r) ?? 0,
                'precio_unitario'           => $this->num('PrecioUnitario', $r) ?? 0,
                'precio_kilo'               => $this->num('PrecioKilo', $r) ?? 0,
                'costo_producto'            => $this->num('CostoProducto', $r) ?? 0,
                'created_at'                => $now,
                'updated_at'                => $now,
            ];
        }
        foreach (array_chunk($bulk, 1000) as $chunk) {
            DB::table('listas_precios_importes')->insert($chunk);
        }
        $this->procesadas['ListasPreciosImportes'] = count($bulk);
    }

    /* ============================================================
     *  ListasPreciosOtrosDatos — solo extraemos el campo `Bulto`
     *
     *  El archivo trae también márgenes, costos, etc. — TODO eso se descarta.
     *  Solo guardamos `Bulto` (string descriptivo como "60 Paquetes.") para
     *  poder sincronizar productos_todotex.bulto desde la lista vigente.
     * ============================================================ */
    private function extraerBultosDeOtrosDatos(string $path): void
    {
        [, $rows] = $this->readExcel($path);
        $procesados = 0;
        // Hacemos UPDATE en chunks: agrupamos por valor de bulto para reducir queries.
        // Si el valor del bulto está vacío, no tocamos (queda lo que ya había).
        foreach (array_chunk($rows, 500) as $chunk) {
            foreach ($chunk as $r) {
                $listaPk    = $this->int('ListasPreciosPK', $r);
                $productoPk = $this->int('ProductosPK', $r);
                $bulto      = $this->str('Bulto', $r, 120);
                if (!$listaPk || !$productoPk || !$bulto) continue;
                DB::table('listas_precios_importes')
                    ->where('listas_precios_pk_externa', $listaPk)
                    ->where('productos_pk_externa', $productoPk)
                    ->update(['bulto' => $bulto]);
                $procesados++;
            }
        }
        $this->procesadas['BultosExtraidos'] = $procesados;
    }

    /* ============================================================
     *  Sync de precios + bulto → productos_todotex (matching por pk_externa)
     *
     *  Para productos donde hicimos UPSERT, ya tienen pk_externa cargada.
     *  Hacemos JOIN por pk_externa: simple y rápido.
     *  Los productos que NO tienen pk_externa quedan con sus datos manuales.
     *
     *  El campo `bulto` solo se sobrescribe si la lista vigente trae un valor
     *  (COALESCE no-null). Si la lista no tiene bulto para ese producto,
     *  preservamos el que ya tenía cargado el B2B.
     * ============================================================ */
    private function sincronizarPreciosDeListaVigente(): void
    {
        $vigente = Precio::query()->vigente()->orderByDesc('fecha_desde')->first();
        if (!$vigente || !$vigente->pk_externa) {
            $this->procesadas['SyncPrecios'] = 0;
            return;
        }

        // Decisión operativa: `productos_todotex.precio_unitario` guarda el precio
        // de la mínima unidad de compra (= 1 paquete). El cliente no compra
        // unidades sueltas. Por eso usamos l.precio_paquete del ERP.
        // El precio por unidad real (l.precio_unitario) no lo persistimos en el
        // producto — está disponible en listas_precios_importes si se necesita.
        $afectadosVigente = DB::update(
            "UPDATE productos_todotex p
             JOIN listas_precios_importes l
               ON l.productos_pk_externa = p.pk_externa
              AND l.listas_precios_pk_externa = ?
             SET p.precio_unitario = l.precio_paquete,
                 p.precio_paquete  = l.precio_paquete,
                 p.precio_kg       = l.precio_kilo,
                 p.bulto           = COALESCE(l.bulto, p.bulto),
                 p.updated_at      = NOW()",
            [$vigente->pk_externa]
        );
        $this->procesadas['SyncPrecios'] = $afectadosVigente;

        // Fallback: productos con pk_externa que quedaron en 0 porque no están
        // en la lista vigente. Usamos el precio de la lista más reciente (por
        // fecha_desde) que sí los incluya. Evita ver $0 en el front.
        $afectadosFallback = DB::update("
            UPDATE productos_todotex p
            JOIN (
                SELECT
                    l.productos_pk_externa,
                    l.precio_paquete,
                    l.precio_kilo,
                    l.bulto,
                    ROW_NUMBER() OVER (
                        PARTITION BY l.productos_pk_externa
                        ORDER BY pr.fecha_desde DESC, pr.id DESC
                    ) AS rn
                FROM listas_precios_importes l
                JOIN precios pr ON pr.pk_externa = l.listas_precios_pk_externa
            ) ult ON ult.productos_pk_externa = p.pk_externa AND ult.rn = 1
            SET p.precio_unitario = ult.precio_paquete,
                p.precio_paquete  = ult.precio_paquete,
                p.precio_kg       = ult.precio_kilo,
                p.bulto           = COALESCE(ult.bulto, p.bulto),
                p.updated_at      = NOW()
            WHERE p.precio_unitario <= 0
              AND p.pk_externa IS NOT NULL
        ");
        $this->procesadas['SyncPreciosFallback'] = $afectadosFallback;
    }

    /* ============================================================
     *  Aplicar mapeos ERP → categorización B2B
     *
     *  Para cada producto, mira su subfamilias_pk_externa y familias_pk_externa
     *  y si hay un mapeo definido por el admin, asigna la familia y/o categoría
     *  del B2B correspondiente. Solo asigna si el producto NO tiene categoría
     *  todavía — los productos categorizados a mano se respetan.
     *
     *  Precedencia: si hay mapeo por subfamilia (más específico) gana sobre
     *  mapeo por familia (más general).
     * ============================================================ */
    private function aplicarMapeosACategorias(): void
    {
        // Cargo mapeos en memoria: [tipo][pk_externa] => ['familia_id'=>X, 'categoria_id'=>Y]
        $mapeos = MapeoErpCategoria::query()->get();
        if ($mapeos->isEmpty()) {
            $this->procesadas['CategoriasAsignadas'] = 0;
            return;
        }

        $mapeosPorTipo = [
            MapeoErpCategoria::TIPO_FAMILIA    => [],
            MapeoErpCategoria::TIPO_SUBFAMILIA => [],
        ];
        foreach ($mapeos as $m) {
            $mapeosPorTipo[$m->entidad_tipo][$m->entidad_pk_externa] = [
                'familia_id'   => $m->familia_id,
                'categoria_id' => $m->categoria_id,
            ];
        }

        // Solo productos del ERP que no tienen categoría todavía
        $productos = DB::table('productos_todotex as p')
            ->leftJoin('categoria_producto as cp', 'cp.producto_id', '=', 'p.id')
            ->whereNotNull('p.pk_externa')
            ->whereNull('cp.id')
            ->select('p.id', 'p.familias_pk_externa', 'p.subfamilias_pk_externa')
            ->get();

        $asignados = 0;
        $now = now();
        $pivotInserts = [];

        foreach ($productos as $prod) {
            $mapeo = null;
            // Precedencia: subfamilia → familia
            if ($prod->subfamilias_pk_externa && isset($mapeosPorTipo['subfamilia'][$prod->subfamilias_pk_externa])) {
                $mapeo = $mapeosPorTipo['subfamilia'][$prod->subfamilias_pk_externa];
            } elseif ($prod->familias_pk_externa && isset($mapeosPorTipo['familia'][$prod->familias_pk_externa])) {
                $mapeo = $mapeosPorTipo['familia'][$prod->familias_pk_externa];
            }

            if (!$mapeo) continue;

            if ($mapeo['categoria_id']) {
                $pivotInserts[] = [
                    'producto_id'  => $prod->id,
                    'categoria_id' => $mapeo['categoria_id'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
                $asignados++;
            }
        }

        if (!empty($pivotInserts)) {
            foreach (array_chunk($pivotInserts, 500) as $chunk) {
                DB::table('categoria_producto')->insert($chunk);
            }
        }
        $this->procesadas['CategoriasAsignadas'] = $asignados;
    }

    /**
     * Normaliza un código de producto para que matchee aunque difiera por
     * espacios o mayúsculas. Ej: "1201 4121" → "12014121", "ABC 12" → "abc12".
     */
    private function normalizarCodigo(?string $codigo): string
    {
        return mb_strtolower(preg_replace('/\s+/', '', (string) $codigo));
    }
}
