<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Models\CategoriaTodotex;
use App\Models\Familia;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoriaTodotexController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortField = $request->get('sortField', 'orden');
        $sortDirection = $request->get('sortDirection', 'asc');
        $perPage = $request->get('per_page', 15);
        $familiaId = $request->get('familia_id', '');
        $rubroId = $request->get('rubro_id', '');
        $visible = $request->get('visible', '');

        $query = CategoriaTodotex::with(['familia', 'rubros']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('orden', 'like', "%{$search}%");
            });
        }

        if ($familiaId !== '') {
            $query->where('familia_id', $familiaId);
        }

        if ($rubroId !== '') {
            $query->where('rubro_id', $rubroId);
        }

        if ($visible !== '') {
            $query->where('visible', (bool) $visible);
        }

        $allowedSorts = ['orden', 'titulo', 'destacado', 'visible', 'familia_id', 'rubro_id', 'created_at'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'orden';
        }

        if ($sortField === 'orden') {
            $dir = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';
            $categorias = $query->orderByRaw("(visible = 0 AND orden IS NULL), orden IS NULL, CAST(orden AS UNSIGNED) {$dir}")->paginate($perPage);
        } else {
            $categorias = $query->orderBy($sortField, $sortDirection)->paginate($perPage);
        }
        $familias = Familia::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->get();
        $rubros = Rubro::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->orderBy('titulo')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('livewire.categorias-todotex.partials.table', compact('categorias'))->render(),
                'pagination' => view('livewire.categorias-todotex.partials.pagination', compact('categorias'))->render(),
                'stats' => [
                    'total' => $categorias->total(),
                    'from' => $categorias->firstItem(),
                    'to' => $categorias->lastItem(),
                    'current_page' => $categorias->currentPage(),
                    'last_page' => $categorias->lastPage(),
                ],
            ]);
        }

        return view('livewire.categorias-todotex.index', compact('categorias', 'familias', 'rubros'));
    }

    public function create()
    {
        $familias = Familia::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->get();
        $rubros = Rubro::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->orderBy('titulo')->get();

        return view('livewire.categorias-todotex.create', compact('familias', 'rubros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'orden' => 'nullable|string|max:50',
            'visible' => 'nullable|boolean',
            'familia_id' => 'required|exists:familias,id',
            'rubro_id' => 'nullable|exists:rubros,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $data = [
            'titulo' => $request->titulo,
            'orden' => $request->orden,
            'visible' => $request->boolean('visible', true),
            'familia_id' => $request->familia_id,
            'rubro_id' => $request->input('rubro_id') ?: null,
        ];

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('categorias/categorias-todotex', 'public');
        }

        $categoria = CategoriaTodotex::create($data);

        if ($data['rubro_id']) {
            $categoria->rubros()->sync([$data['rubro_id']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada correctamente',
            'redirect' => route('categorias-todotex.index'),
        ]);
    }

    public function edit($id)
    {
        $categoria = CategoriaTodotex::with('rubro')->findOrFail($id);
        $familias = Familia::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->get();
        $rubros = Rubro::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->orderBy('titulo')->get();

        return view('livewire.categorias-todotex.edit', compact('categoria', 'familias', 'rubros'));
    }

    public function update(Request $request, $id)
    {
        $categoria = CategoriaTodotex::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'orden' => 'nullable|string|max:50',
            'visible' => 'nullable|boolean',
            'familia_id' => 'required|exists:familias,id',
            'rubro_id' => 'nullable|exists:rubros,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $data = [
            'titulo' => $request->titulo,
            'orden' => $request->orden,
            'visible' => $request->boolean('visible', true),
            'familia_id' => $request->familia_id,
            'rubro_id' => $request->input('rubro_id') ?: null,
        ];

        if ($request->hasFile('imagen')) {
            if ($categoria->imagen) {
                Storage::disk('public')->delete($categoria->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('categorias/categorias-todotex', 'public');
        }

        $categoria->update($data);
        $categoria->rubros()->sync($data['rubro_id'] ? [$data['rubro_id']] : []);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada correctamente',
            'redirect' => route('categorias-todotex.index'),
        ]);
    }

    public function deleteImagen($id)
    {
        $categoria = CategoriaTodotex::findOrFail($id);

        if ($categoria->imagen) {
            Storage::disk('public')->delete($categoria->imagen);
            $categoria->update(['imagen' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada correctamente',
        ]);
    }

    public function destroy($id)
    {
        $categoria = CategoriaTodotex::findOrFail($id);

        if ($categoria->imagen) {
            Storage::disk('public')->delete($categoria->imagen);
        }

        $categoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada correctamente',
        ]);
    }

    public function fusionar()
    {
        $familias = Familia::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->get();
        $rubros   = Rubro::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->orderBy('titulo')->get();

        $categoriasPorFamilia = CategoriaTodotex::where('visible', true)
            ->with('rubro')
            ->withCount('productos')
            ->orderByRaw('CAST(orden AS UNSIGNED)')
            ->get()
            ->groupBy('familia_id')
            ->map(fn ($cats) => $cats->map(fn ($c) => [
                'id'             => $c->id,
                'titulo'         => $c->titulo,
                'rubro_id'       => $c->rubro_id,
                'rubro_titulo'   => $c->rubro?->titulo,
                'productos_count'=> $c->productos_count,
            ])->values())
            ->toArray();

        $categoriasOcultas = CategoriaTodotex::where('visible', false)
            ->whereNotNull('fusionada_en_id')
            ->with(['familia', 'fusionadaEn'])
            ->withCount('productos')
            ->orderBy('fusionada_en_id')
            ->orderBy('titulo')
            ->get();

        return view('livewire.categorias-todotex.fusionar', compact('familias', 'rubros', 'categoriasPorFamilia', 'categoriasOcultas'));
    }

    public function ordenProductos(Request $request, $id)
    {
        $categoria = CategoriaTodotex::with('familia')->findOrFail($id);
        $search = $request->get('search', '');

        $query = \App\Models\ProductoTodotex::query()
            ->join('categoria_producto as cp', function ($join) use ($id) {
                $join->on('cp.producto_id', '=', 'productos_todotex.id')
                     ->where('cp.categoria_id', '=', $id);
            })
            ->select('productos_todotex.*', 'cp.orden as cat_orden')
            ->orderByRaw('cp.orden IS NULL, CAST(cp.orden AS UNSIGNED), productos_todotex.codigo');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('productos_todotex.titulo', 'like', "%{$search}%")
                  ->orWhere('productos_todotex.codigo', 'like', "%{$search}%");
            });
        }

        $productos = $query->paginate(50)->withQueryString();

        return view('livewire.categorias-todotex.orden', compact('categoria', 'productos', 'search'));
    }

    public function guardarOrdenProductos(Request $request, $id)
    {
        CategoriaTodotex::findOrFail($id);

        foreach ($request->input('ordenes', []) as $productoId => $orden) {
            DB::table('categoria_producto')
                ->where('categoria_id', $id)
                ->where('producto_id', (int) $productoId)
                ->update(['orden' => ($orden !== null && $orden !== '') ? (int) $orden : null]);
        }

        return response()->json(['success' => true, 'message' => 'Orden guardado correctamente.']);
    }

    public function productosCategoria($id)
    {
        $categoria = CategoriaTodotex::findOrFail($id);
        $productos = $categoria->productos()
            ->select(['productos_todotex.id', 'productos_todotex.codigo', 'productos_todotex.titulo'])
            ->orderBy('codigo')
            ->get();

        return response()->json(['productos' => $productos]);
    }

    public function revertirCategoria($id)
    {
        $categoria = CategoriaTodotex::findOrFail($id);
        $categoria->update(['visible' => true, 'fusionada_en_id' => null]);

        return response()->json([
            'success' => true,
            'message' => "«{$categoria->titulo}» fue revertida y ahora es visible nuevamente.",
        ]);
    }

    public function procesarFusion(Request $request)
    {
        $request->validate([
            'titulo'          => 'required|string|max:255',
            'familia_id'      => 'required|exists:familias,id',
            'categoria_ids'   => 'required|array|min:2',
            'categoria_ids.*' => 'exists:categorias_todotex,id',
            'orden'           => 'nullable|string|max:50',
            'rubro_ids'       => 'nullable|array',
            'rubro_ids.*'     => 'exists:rubros,id',
        ]);

        $categoriaIds = $request->categoria_ids;

        $categorias = CategoriaTodotex::whereIn('id', $categoriaIds)
            ->where('familia_id', $request->familia_id)
            ->get();

        if ($categorias->count() < 2) {
            return response()->json([
                'success' => false,
                'errors'  => ['categoria_ids' => 'Debés seleccionar al menos 2 categorías de la misma familia.'],
            ], 422);
        }

        // Todos los productos de las categorías originales (sin duplicados)
        $productIds = DB::table('categoria_producto')
            ->whereIn('categoria_id', $categoriaIds)
            ->pluck('producto_id')
            ->unique()
            ->values();

        // Crear nueva categoría fusionada
        $nueva = CategoriaTodotex::create([
            'titulo'     => $request->titulo,
            'orden'      => $request->orden,
            'familia_id' => $request->familia_id,
            'visible'    => true,
        ]);

        // Asociar rubros via pivot
        $rubroIds = array_filter((array) $request->input('rubro_ids', []));
        if (!empty($rubroIds)) {
            $nueva->rubros()->attach($rubroIds);
        }

        // Asignar todos los productos a la nueva categoría
        if ($productIds->isNotEmpty()) {
            $nueva->productos()->attach($productIds->toArray());
        }

        // Desasociar productos de las categorías originales
        DB::table('categoria_producto')
            ->whereIn('categoria_id', $categoriaIds)
            ->delete();

        // Ocultar categorías originales y registrar que fueron fusionadas en la nueva
        CategoriaTodotex::whereIn('id', $categoriaIds)->update([
            'visible'         => false,
            'fusionada_en_id' => $nueva->id,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => "Fusión completada: {$categorias->count()} categorías ocultas, {$productIds->count()} productos reasignados a «{$nueva->titulo}».",
            'redirect' => route('categorias-todotex.index'),
        ]);
    }

    public function toggleVisible($id)
    {
        $categoria = CategoriaTodotex::findOrFail($id);
        $categoria->visible = !$categoria->visible;
        $categoria->save();

        return response()->json([
            'success' => true,
            'visible' => $categoria->visible,
            'message' => $categoria->visible ? 'Categoría visible' : 'Categoría oculta',
        ]);
    }
}
