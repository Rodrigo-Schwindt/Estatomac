<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaTodotex;
use App\Models\Erp\ErpFamilia;
use App\Models\Erp\ErpSubfamilia;
use App\Models\Erp\MapeoErpCategoria;
use App\Models\Familia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapeoErpController extends Controller
{
    public function index()
    {
        $familiasErp    = ErpFamilia::orderBy('nombre')->get();
        $subfamiliasErp = ErpSubfamilia::orderBy('nombre')->get();
        $familiasB2B    = Familia::orderBy('titulo')->get();
        $categoriasB2B  = CategoriaTodotex::with('familia')->orderBy('titulo')->get();

        // Cantidad de productos por familia y subfamilia del ERP
        $countsFam = DB::table('productos_todotex')
            ->whereNotNull('familias_pk_externa')
            ->select('familias_pk_externa', DB::raw('COUNT(*) as n'))
            ->groupBy('familias_pk_externa')
            ->pluck('n', 'familias_pk_externa')
            ->all();

        $countsSub = DB::table('productos_todotex')
            ->whereNotNull('subfamilias_pk_externa')
            ->select('subfamilias_pk_externa', DB::raw('COUNT(*) as n'))
            ->groupBy('subfamilias_pk_externa')
            ->pluck('n', 'subfamilias_pk_externa')
            ->all();

        // Cuántos de cada familia/subfamilia están sin categoría asignada
        $sinCatFam = DB::table('productos_todotex as p')
            ->leftJoin('categoria_producto as cp', 'cp.producto_id', '=', 'p.id')
            ->whereNotNull('p.familias_pk_externa')
            ->whereNull('cp.id')
            ->select('p.familias_pk_externa', DB::raw('COUNT(DISTINCT p.id) as n'))
            ->groupBy('p.familias_pk_externa')
            ->pluck('n', 'p.familias_pk_externa')
            ->all();

        $sinCatSub = DB::table('productos_todotex as p')
            ->leftJoin('categoria_producto as cp', 'cp.producto_id', '=', 'p.id')
            ->whereNotNull('p.subfamilias_pk_externa')
            ->whereNull('cp.id')
            ->select('p.subfamilias_pk_externa', DB::raw('COUNT(DISTINCT p.id) as n'))
            ->groupBy('p.subfamilias_pk_externa')
            ->pluck('n', 'p.subfamilias_pk_externa')
            ->all();

        // Mapeos existentes indexados [tipo][pk_externa] => mapeo
        $mapeos = MapeoErpCategoria::all()->groupBy('entidad_tipo')->map(
            fn ($items) => $items->keyBy('entidad_pk_externa')
        );

        $statsFam = [
            'total'    => $familiasErp->count(),
            'mapeadas' => isset($mapeos['familia']) ? $mapeos['familia']->count() : 0,
        ];
        $statsSub = [
            'total'    => $subfamiliasErp->count(),
            'mapeadas' => isset($mapeos['subfamilia']) ? $mapeos['subfamilia']->count() : 0,
        ];

        return view('admin.mapeos-erp.index', compact(
            'familiasErp', 'subfamiliasErp',
            'familiasB2B', 'categoriasB2B',
            'mapeos', 'statsFam', 'statsSub',
            'countsFam', 'countsSub', 'sinCatFam', 'sinCatSub'
        ));
    }

    /**
     * Devuelve JSON con los productos asociados a una familia o subfamilia del ERP.
     * Usado por el modal de la vista de mapeos para verificar visualmente.
     */
    public function productos(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:familia,subfamilia',
            'pk'   => 'required|integer',
        ]);

        $columna = $request->tipo === 'familia' ? 'familias_pk_externa' : 'subfamilias_pk_externa';

        $productos = DB::table('productos_todotex as p')
            ->where("p.{$columna}", $request->pk)
            ->leftJoin('categoria_producto as cp', 'cp.producto_id', '=', 'p.id')
            ->leftJoin('categorias_todotex as c', 'c.id', '=', 'cp.categoria_id')
            ->select(
                'p.id',
                'p.codigo',
                'p.titulo',
                'p.precio_unitario',
                'p.pk_externa',
                DB::raw('GROUP_CONCAT(DISTINCT c.titulo SEPARATOR ", ") as categorias')
            )
            ->groupBy('p.id', 'p.codigo', 'p.titulo', 'p.precio_unitario', 'p.pk_externa')
            ->orderBy('p.codigo')
            ->limit(500)
            ->get();

        return response()->json([
            'tipo'      => $request->tipo,
            'pk'        => (int) $request->pk,
            'productos' => $productos,
            'total'     => $productos->count(),
        ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'mapeos'                       => 'array',
            'mapeos.*.entidad_tipo'        => 'required|in:familia,subfamilia',
            'mapeos.*.entidad_pk_externa'  => 'required|integer',
            'mapeos.*.familia_id'          => 'nullable|integer|exists:familias,id',
            'mapeos.*.categoria_id'        => 'nullable|integer|exists:categorias_todotex,id',
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['mapeos'] ?? [] as $m) {
                // Si los dos están vacíos, borramos el mapeo
                if (empty($m['familia_id']) && empty($m['categoria_id'])) {
                    MapeoErpCategoria::where('entidad_tipo', $m['entidad_tipo'])
                        ->where('entidad_pk_externa', $m['entidad_pk_externa'])
                        ->delete();
                    continue;
                }
                MapeoErpCategoria::updateOrCreate(
                    [
                        'entidad_tipo'       => $m['entidad_tipo'],
                        'entidad_pk_externa' => $m['entidad_pk_externa'],
                    ],
                    [
                        'familia_id'   => $m['familia_id'] ?: null,
                        'categoria_id' => $m['categoria_id'] ?: null,
                    ]
                );
            }
        });

        return back()->with('success', 'Mapeos guardados.');
    }

    /**
     * Aplica los mapeos a productos del ERP que todavía no tienen categoría.
     * Útil cuando recién definiste reglas y querés categorizar todo lo histórico
     * sin esperar al próximo import.
     */
    public function aplicar(Request $request)
    {
        $mapeos = MapeoErpCategoria::all();
        $mapeosPorTipo = [
            'familia'    => [],
            'subfamilia' => [],
        ];
        foreach ($mapeos as $m) {
            $mapeosPorTipo[$m->entidad_tipo][$m->entidad_pk_externa] = [
                'familia_id'   => $m->familia_id,
                'categoria_id' => $m->categoria_id,
            ];
        }

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
            if ($prod->subfamilias_pk_externa && isset($mapeosPorTipo['subfamilia'][$prod->subfamilias_pk_externa])) {
                $mapeo = $mapeosPorTipo['subfamilia'][$prod->subfamilias_pk_externa];
            } elseif ($prod->familias_pk_externa && isset($mapeosPorTipo['familia'][$prod->familias_pk_externa])) {
                $mapeo = $mapeosPorTipo['familia'][$prod->familias_pk_externa];
            }

            if (!$mapeo || !$mapeo['categoria_id']) continue;

            $pivotInserts[] = [
                'producto_id'  => $prod->id,
                'categoria_id' => $mapeo['categoria_id'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
            $asignados++;
        }

        if (!empty($pivotInserts)) {
            foreach (array_chunk($pivotInserts, 500) as $chunk) {
                DB::table('categoria_producto')->insert($chunk);
            }
        }

        return back()->with('success', "Se categorizaron {$asignados} producto(s) según los mapeos definidos.");
    }

    /**
     * Crea una familia B2B nueva desde el modal del panel de mapeos.
     * Devuelve JSON con el id + titulo para que el JS la agregue al dropdown.
     */
    public function crearFamiliaB2B(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255|unique:familias,titulo',
        ]);

        $familia = Familia::create([
            'titulo'    => $data['titulo'],
            'orden'     => (int) Familia::max('orden') + 1,
            'destacado' => false,
            'visible'   => true,
        ]);

        return response()->json([
            'id'     => $familia->id,
            'titulo' => $familia->titulo,
        ]);
    }

    /**
     * Crea una categoría B2B nueva desde el modal del panel de mapeos.
     * Requiere familia_id porque la columna es NOT NULL.
     *
     * Se crea OCULTA (visible=false), igual que los productos importados del ERP:
     * el admin tiene que revisarla (asignarle imagen, orden, etc.) y activarla
     * a mano. Así no aparece en la web sin curar.
     */
    public function crearCategoriaB2B(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo'     => 'required|string|max:255',
            'familia_id' => 'required|integer|exists:familias,id',
        ]);

        $cat = CategoriaTodotex::create([
            'titulo'     => $data['titulo'],
            'familia_id' => $data['familia_id'],
            'orden'      => (int) CategoriaTodotex::where('familia_id', $data['familia_id'])->max('orden') + 1,
            'destacado'  => false,
            'visible'    => false,
        ]);

        $cat->load('familia');

        return response()->json([
            'id'         => $cat->id,
            'titulo'     => $cat->titulo,
            'familia_id' => $cat->familia_id,
            'familia'    => $cat->familia?->titulo,
        ]);
    }
}
