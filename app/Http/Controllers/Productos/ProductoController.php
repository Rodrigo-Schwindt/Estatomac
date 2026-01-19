<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria']);

        // Filtro de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('order', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Filtro por visible/oculto
        if ($request->filled('visible')) {
            $visible = $request->visible === '1' ? 1 : 0;
            $query->where('visible', $visible);
        }

        // Filtro por nuevo/recambio
        if ($request->filled('nuevo')) {
            $query->where('nuevo', $request->nuevo);
        }

        // Filtro por importados
        if ($request->filled('importados')) {
            $importados = $request->importados === '1' ? 1 : 0;
            $query->where('importados', $importados);
        }

        // Filtro por destacado
        if ($request->filled('destacado')) {
            $destacado = $request->destacado === '1' ? 1 : 0;
            $query->where('destacado', $destacado);
        }

        // Filtro por oferta
        if ($request->filled('oferta')) {
            $oferta = $request->oferta === '1' ? 1 : 0;
            $query->where('oferta', $oferta);
        }

        $sortField = $request->get('sortField', 'order');
        $sortDirection = $request->get('sortDirection', 'asc');
        
        // Validar campos de ordenamiento
        $allowedSortFields = ['order', 'title', 'code', 'precio', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'order';
        }
        
        $query->orderBy($sortField, $sortDirection);

        // Configurar items por página
        $perPage = $request->get('per_page', 25);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 25;
        }

        $productos = $query->paginate($perPage)->appends($request->except('page'));

        $categorias = Categoria::orderBy('title')->get();

        if ($request->ajax()) {
            $html = view('livewire.productos.partials.table', compact('productos'))->render();
            $pagination = view('livewire.productos.partials.pagination', compact('productos'))->render();
            
            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
                'stats' => [
                    'total' => $productos->total(),
                    'from' => $productos->firstItem(),
                    'to' => $productos->lastItem(),
                    'current_page' => $productos->currentPage(),
                    'last_page' => $productos->lastPage(),
                ]
            ]);
        }
        
        return view('livewire.productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $marcas = Marca::orderBy('title')->get();
        $categorias = Categoria::orderBy('title')->get();
        $modelos = Modelo::orderBy('title')->get();
        return view('livewire.productos.create', compact('marcas', 'categorias', 'modelos'));
    }

    public function getModelos($marcaId)
    {
        $modelos = Modelo::where('marca_id', $marcaId)->orderBy('title')->get();
        return response()->json($modelos);
    }

    public function getSubcategorias($categoriaId)
    {
        $subcategorias = Subcategoria::where('categoria_id', $categoriaId)->orderBy('order')->get();
        return response()->json($subcategorias);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:productos,code',
            'marcas' => 'nullable|array',
            'marcas.*' => 'exists:marcas,id',
            'modelos' => 'nullable|array',
            'modelos.*' => 'exists:modelos,id',
            'categoria_id' => 'nullable|exists:categorias,id',
            'order' => 'nullable|string|max:255',
            'visible' => 'required|boolean',
            'nuevo' => 'required|in:nuevo,recambio',
            'destacado' => 'required|boolean',
            'oferta' => 'required|boolean',
            'importados' => 'required|boolean',
            'precio' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'subcategorias' => 'nullable|array',
            'subcategorias.*.id' => 'required|exists:subcategorias,id',
            'subcategorias.*.valor' => 'nullable|string|max:255',
        ]);

        $producto = new Producto();
        $producto->title = $request->title;
        $producto->code = $request->code;
        $producto->categoria_id = $request->categoria_id;
        $producto->order = $request->order;
        $producto->visible = $request->visible;
        $producto->nuevo = $request->nuevo;
        $producto->destacado = $request->destacado;
        $producto->oferta = $request->oferta;
        $producto->importados = $request->importados;
        $producto->precio = $request->precio;
        $producto->descuento = $request->descuento ?? 0;

        if ($request->hasFile('image')) {
            $producto->image = $request->file('image')->store('productos', 'public');
        }

        $producto->save();

        if ($request->has('marcas')) {
            $producto->marcas()->sync($request->marcas);
        }

        if ($request->has('modelos')) {
            $producto->modelos()->sync($request->modelos);
        }

        if ($request->has('subcategorias')) {
            $syncData = [];
            foreach ($request->subcategorias as $subcategoria) {
                if (!empty($subcategoria['valor'])) {
                    $syncData[$subcategoria['id']] = ['valor' => $subcategoria['valor']];
                }
            }
            $producto->subcategorias()->sync($syncData);
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $file) {
                Gallery::create([
                    'producto_id' => $producto->id,
                    'image' => $file->store('gallery', 'public'),
                    'order' => $index + 1,
                ]);
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente');
    }

    public function edit(Producto $producto)
    {
        $producto->load(['gallery', 'subcategorias', 'marcas', 'modelos']);
        $marcas = Marca::orderBy('title')->get();
        $categorias = Categoria::orderBy('title')->get();
        $modelos = Modelo::orderBy('title')->get();
        $subcategorias = $producto->categoria_id ? Subcategoria::where('categoria_id', $producto->categoria_id)->orderBy('order')->get() : collect();
        
        return view('livewire.productos.edit', compact('producto', 'marcas', 'categorias', 'modelos', 'subcategorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:productos,code,' . $producto->id,
            'marcas' => 'nullable|array',
            'marcas.*' => 'exists:marcas,id',
            'modelos' => 'nullable|array',
            'modelos.*' => 'exists:modelos,id',
            'categoria_id' => 'nullable|exists:categorias,id',
            'order' => 'nullable|string|max:255',
            'visible' => 'required|boolean',
            'nuevo' => 'required|in:nuevo,recambio',
            'destacado' => 'required|boolean',
            'oferta' => 'required|boolean',
            'importados' => 'required|boolean',
            'precio' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'deleted_gallery' => 'nullable|array',
            'subcategorias' => 'nullable|array',
            'subcategorias.*.id' => 'required|exists:subcategorias,id',
            'subcategorias.*.valor' => 'nullable|string|max:255',
        ]);

        $producto->title = $request->title;
        $producto->code = $request->code;
        $producto->categoria_id = $request->categoria_id;
        $producto->order = $request->order;
        $producto->visible = $request->visible;
        $producto->nuevo = $request->nuevo;
        $producto->destacado = $request->destacado;
        $producto->oferta = $request->oferta;
        $producto->importados = $request->importados;
        $producto->precio = $request->precio;
        $producto->descuento = $request->descuento ?? 0;

        if ($request->hasFile('image')) {
            if ($producto->image) {
                Storage::disk('public')->delete($producto->image);
            }
            $producto->image = $request->file('image')->store('productos', 'public');
        }

        $producto->save();

        if ($request->has('marcas')) {
            $producto->marcas()->sync($request->marcas);
        } else {
            $producto->marcas()->detach();
        }

        if ($request->has('modelos')) {
            $producto->modelos()->sync($request->modelos);
        } else {
            $producto->modelos()->detach();
        }

        if ($request->has('subcategorias')) {
            $syncData = [];
            foreach ($request->subcategorias as $subcategoria) {
                if (!empty($subcategoria['valor'])) {
                    $syncData[$subcategoria['id']] = ['valor' => $subcategoria['valor']];
                }
            }
            $producto->subcategorias()->sync($syncData);
        } else {
            $producto->subcategorias()->detach();
        }

        if ($request->has('deleted_gallery')) {
            $galleryItems = Gallery::whereIn('id', $request->deleted_gallery)->get();
            foreach ($galleryItems as $item) {
                Storage::disk('public')->delete($item->image);
                $item->delete();
            }
        }

        if ($request->hasFile('gallery')) {
            $lastOrder = $producto->gallery()->max('order') ?? 0;
            foreach ($request->file('gallery') as $index => $file) {
                Gallery::create([
                    'producto_id' => $producto->id,
                    'image' => $file->store('gallery', 'public'),
                    'order' => $lastOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function showImportForm()
    {
        return view('livewire.productos.import');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->image) {
            Storage::disk('public')->delete($producto->image);
        }

        foreach ($producto->gallery as $item) {
            Storage::disk('public')->delete($item->image);
        }

        $producto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ]);
    }
}