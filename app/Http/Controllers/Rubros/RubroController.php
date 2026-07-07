<?php

namespace App\Http\Controllers\Rubros;

use App\Http\Controllers\Controller;
use App\Models\CategoriaTodotex;
use App\Models\ProductoTodotex;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RubroController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortField = $request->get('sortField', 'orden');
        $sortDirection = $request->get('sortDirection', 'asc');
        $perPage = $request->get('per_page', 15);

        $query = Rubro::query()->withCount('categorias');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('orden', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['orden', 'titulo', 'created_at'];
        if (!in_array($sortField, $allowedSorts, true)) {
            $sortField = 'orden';
        }

        $rubros = $query->orderBy($sortField, $sortDirection)->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('livewire.rubros.partials.table', compact('rubros'))->render(),
                'pagination' => view('livewire.rubros.partials.pagination', compact('rubros'))->render(),
                'stats' => [
                    'total' => $rubros->total(),
                    'from' => $rubros->firstItem(),
                    'to' => $rubros->lastItem(),
                    'current_page' => $rubros->currentPage(),
                    'last_page' => $rubros->lastPage(),
                ],
            ]);
        }

        return view('livewire.rubros.index', compact('rubros'));
    }

    public function create()
    {
        return view('livewire.rubros.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $data = [
            'titulo' => $request->titulo,
            'orden' => $request->filled('orden') ? (int) $request->orden : null,
        ];

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('categorias/rubros', 'public');
        }

        Rubro::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Rubro creado correctamente',
            'redirect' => route('rubros.index'),
        ]);
    }

    public function edit($id)
    {
        $rubro = Rubro::findOrFail($id);

        return view('livewire.rubros.edit', compact('rubro'));
    }

    public function update(Request $request, $id)
    {
        $rubro = Rubro::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $data = [
            'titulo' => $request->titulo,
            'orden' => $request->filled('orden') ? (int) $request->orden : null,
        ];

        if ($request->hasFile('imagen')) {
            if ($rubro->imagen) {
                Storage::disk('public')->delete($rubro->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('categorias/rubros', 'public');
        }

        $rubro->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Rubro actualizado correctamente',
            'redirect' => route('rubros.index'),
        ]);
    }

    public function deleteImagen($id)
    {
        $rubro = Rubro::findOrFail($id);

        if ($rubro->imagen) {
            Storage::disk('public')->delete($rubro->imagen);
            $rubro->update(['imagen' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada correctamente',
        ]);
    }

    public function ordenProductos(Request $request, $id)
    {
        $rubro = Rubro::with('categorias')->findOrFail($id);
        $search = $request->get('search', '');
        $categoriaFiltroId = $request->get('categoria_id', '');

        $categoriaIds = $rubro->categorias->pluck('id');

        $query = ProductoTodotex::whereHas('categorias', fn($q) => $q->whereIn('categorias_todotex.id', $categoriaIds))
            ->with(['categorias' => fn($q) => $q->whereIn('categorias_todotex.id', $categoriaIds)])
            ->leftJoin('rubro_producto_orden as rpo', function ($join) use ($id) {
                $join->on('rpo.producto_id', '=', 'productos_todotex.id')
                     ->where('rpo.rubro_id', '=', $id);
            })
            ->select('productos_todotex.*', 'rpo.orden as rubro_orden');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('productos_todotex.titulo', 'like', "%{$search}%")
                  ->orWhere('productos_todotex.codigo', 'like', "%{$search}%");
            });
        }

        if ($categoriaFiltroId !== '') {
            $query->whereHas('categorias', fn($q) => $q->where('categorias_todotex.id', $categoriaFiltroId));
        }

        $query->orderByRaw('rpo.orden IS NULL, CAST(rpo.orden AS UNSIGNED), productos_todotex.codigo');

        $productos = $query->paginate(50)->withQueryString();

        return view('livewire.rubros.orden', compact('rubro', 'productos', 'search', 'categoriaFiltroId'));
    }

    public function guardarOrdenProductos(Request $request, $id)
    {
        Rubro::findOrFail($id);

        $ordenes = $request->input('ordenes', []);

        foreach ($ordenes as $productoId => $orden) {
            if ($orden === null || $orden === '') {
                DB::table('rubro_producto_orden')
                    ->where('rubro_id', $id)
                    ->where('producto_id', $productoId)
                    ->delete();
            } else {
                DB::table('rubro_producto_orden')->updateOrInsert(
                    ['rubro_id' => $id, 'producto_id' => (int) $productoId],
                    ['orden' => (int) $orden]
                );
            }
        }

        return response()->json(['success' => true, 'message' => 'Orden guardado correctamente.']);
    }

    public function destroy($id)
    {
        $rubro = Rubro::findOrFail($id);

        if ($rubro->imagen) {
            Storage::disk('public')->delete($rubro->imagen);
        }

        $rubro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rubro eliminado correctamente',
        ]);
    }
}
