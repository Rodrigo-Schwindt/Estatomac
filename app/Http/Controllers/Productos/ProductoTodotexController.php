<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Models\ColorTodotex;
use App\Models\ProductoTodotex;
use App\Models\CategoriaTodotex;
use App\Models\Familia;
use App\Models\Simbolo;
use App\Models\Rubro;
use App\Models\ProductoGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoTodotexController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $codigo = trim($request->get('codigo', ''));
        $codigoDesde = trim($request->get('codigo_desde', ''));
        $codigoHasta = trim($request->get('codigo_hasta', ''));
        $sortField = $request->get('sortField', 'orden');
        $sortDirection = $request->get('sortDirection', 'asc');
        $perPage = $request->get('per_page', 25);
        $familiaId = $request->get('familia_id', '');
        $categoriaId = $request->get('categoria_id', '');
        $simboloId = $request->get('simbolo_id', '');
        $visible = $request->get('visible', '');
        $destacado = $request->get('destacado', '');

        $query = ProductoTodotex::with(['categorias.familia', 'categorias.rubro', 'gallery', 'simbolo']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%")
                  ->orWhere('orden', 'like', "%{$search}%");
            });
        }

        if ($codigo !== '') {
            $query->whereRaw('LOWER(TRIM(codigo)) = ?', [mb_strtolower($codigo)]);
        }

        // Filtro por rango numérico de código. Acepta códigos con o sin espacios
        // (ej "1201 4121" o "12014121"). Internamente quitamos espacios y casteamos a int.
        // Si el codigo no es puramente numérico (tiene letras), CAST devuelve 0 → no matchea el rango.
        $codigoDesdeInt = ctype_digit($codigoDesde) ? (int) $codigoDesde : null;
        $codigoHastaInt = ctype_digit($codigoHasta) ? (int) $codigoHasta : null;
        if ($codigoDesdeInt !== null) {
            $query->whereRaw("CAST(REPLACE(codigo, ' ', '') AS UNSIGNED) >= ?", [$codigoDesdeInt]);
        }
        if ($codigoHastaInt !== null) {
            $query->whereRaw("CAST(REPLACE(codigo, ' ', '') AS UNSIGNED) <= ?", [$codigoHastaInt]);
        }

        if ($familiaId !== '') {
            $query->whereHas('categorias', function ($q) use ($familiaId) {
                $q->where('familia_id', $familiaId);
            });
        }

        if ($categoriaId !== '') {
            $query->whereHas('categorias', function ($q) use ($categoriaId) {
                $q->where('categorias_todotex.id', $categoriaId);
            });
        }

        if ($simboloId !== '') {
            $query->where('simbolo_id', $simboloId);
        }

        if ($visible !== '') {
            $query->where('visible', (bool) $visible);
        }

        if ($destacado !== '') {
            $query->where('destacado', (bool) $destacado);
        }

        $allowedSorts = ['orden', 'titulo', 'codigo', 'precio_unitario', 'precio_paquete', 'precio_kg', 'destacado', 'visible', 'created_at'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'orden';
        }

        if ($sortField === 'orden') {
            $dir = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';
            $productos = $query->orderByRaw("(visible = 0 AND orden IS NULL), orden IS NULL, CAST(orden AS UNSIGNED) {$dir}")->paginate($perPage);
        } else {
            $productos = $query->orderBy($sortField, $sortDirection)->paginate($perPage);
        }
        $familias = Familia::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->get();
        $simbolos = Simbolo::orderBy('id')->get();
        $categorias = CategoriaTodotex::with(['familia', 'rubro'])
            ->orderBy('titulo')
            ->orderBy('rubro_id')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('livewire.productos-todotex.partials.table', compact('productos'))->render(),
                'pagination' => view('livewire.productos-todotex.partials.pagination', compact('productos'))->render(),
                'stats' => [
                    'total' => $productos->total(),
                    'from' => $productos->firstItem(),
                    'to' => $productos->lastItem(),
                    'current_page' => $productos->currentPage(),
                    'last_page' => $productos->lastPage(),
                ],
            ]);
        }

        return view('livewire.productos-todotex.index', compact('productos', 'familias', 'categorias', 'simbolos'));
    }

    public function create()
    {
        $familias = Familia::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->get();
        $rubros = Rubro::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->orderBy('titulo')->get();
        $colores = ColorTodotex::orderByRaw('CASE WHEN orden IS NULL THEN 1 ELSE 0 END')
            ->orderBy('orden')
            ->orderBy('codigo_color')
            ->get();
        $categorias = CategoriaTodotex::with(['familia', 'rubro'])
            ->orderBy('titulo')
            ->orderBy('rubro_id')
            ->get();
        return view('livewire.productos-todotex.create', compact('familias', 'rubros', 'categorias', 'colores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orden' => 'nullable|string|max:50',
            'destacado' => 'nullable|boolean',
            'visible' => 'nullable|boolean',
            'descripcion' => 'nullable|string',
            'codigo' => 'nullable|string|max:100',
            'color_todotex_id' => 'nullable|exists:colores_todotex,id',
            'presentacion' => 'nullable|string',
            'precio_paquete' => 'nullable|numeric|min:0',
            'precio_unitario' => 'nullable|numeric|min:0',
            'precio_kg' => 'nullable|numeric|min:0',
            'porcentaje_aumento' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'bulto' => 'nullable|string|max:100',
            'bulto_cantidad' => 'nullable|integer|min:1',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categorias_todotex,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'portada_index' => 'nullable|integer',
        ]);

        $titulo = strip_tags($request->descripcion ?? '');
        $titulo = mb_substr($titulo, 0, 255) ?: null;
        $bulto = ProductoTodotex::normalizeBulto($request->bulto);
        $bultoCantidad = $request->filled('bulto_cantidad')
            ? max(1, (int) $request->bulto_cantidad)
            : ($bulto !== null ? ProductoTodotex::deriveBultoCantidad($bulto) : null);
        $colorData = $this->resolveColorData(
            $request->input('color_todotex_id'),
            $request->codigo
        );

        $producto = ProductoTodotex::create([
            'titulo' => $titulo,
            'orden' => $request->orden,
            'destacado' => $request->boolean('destacado'),
            'visible' => $request->boolean('visible'),
            'descripcion' => $request->descripcion,
            'codigo' => $request->codigo,
            'color_todotex_id' => $colorData['color_todotex_id'],
            'codigo_color' => $colorData['codigo_color'],
            'nombre_color' => $colorData['nombre_color'],
            'presentacion' => $request->presentacion,
            'precio_paquete' => $request->precio_paquete,
            'precio_unitario' => $request->precio_unitario,
            'precio_kg' => $request->precio_kg,
            'porcentaje_aumento' => $request->porcentaje_aumento,
            'descuento' => $request->descuento,
            'bulto' => $bulto,
            'bulto_cantidad' => $bultoCantidad,
        ]);

        if ($request->has('categorias')) {
            $producto->categorias()->sync($request->categorias);
        }

        $portadaIndex = $request->input('portada_index', 0);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $image) {
                $path = $image->store('productos/gallery', 'public');
                ProductoGallery::create([
                    'producto_id' => $producto->id,
                    'image' => $path,
                    'portada' => ($i == $portadaIndex),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente',
            'redirect' => route('productos-todotex.index'),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $backPage = (int) $request->query('back_page', 1);
        $producto = ProductoTodotex::with(['categorias.familia', 'categorias.rubro', 'gallery', 'color'])->findOrFail($id);
        $familias = Familia::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->get();
        $rubros = Rubro::orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->orderBy('titulo')->get();
        $colores = ColorTodotex::orderByRaw('CASE WHEN orden IS NULL THEN 1 ELSE 0 END')
            ->orderBy('orden')
            ->orderBy('codigo_color')
            ->get();
        $categorias = CategoriaTodotex::with(['familia', 'rubro'])
            ->orderBy('titulo')
            ->orderBy('rubro_id')
            ->get();
        return view('livewire.productos-todotex.edit', compact('producto', 'familias', 'rubros', 'categorias', 'colores', 'backPage'));
    }

    public function update(Request $request, $id)
    {
        $producto = ProductoTodotex::findOrFail($id);

        $request->validate([
            'orden' => 'nullable|string|max:50',
            'destacado' => 'nullable|boolean',
            'visible' => 'nullable|boolean',
            'descripcion' => 'nullable|string',
            'codigo' => 'nullable|string|max:100',
            'color_todotex_id' => 'nullable|exists:colores_todotex,id',
            'presentacion' => 'nullable|string',
            'precio_paquete' => 'nullable|numeric|min:0',
            'precio_unitario' => 'nullable|numeric|min:0',
            'precio_kg' => 'nullable|numeric|min:0',
            'porcentaje_aumento' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'bulto' => 'nullable|string|max:100',
            'bulto_cantidad' => 'nullable|integer|min:1',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categorias_todotex,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'portada_id' => 'nullable|integer',
            'portada_index' => 'nullable|integer',
        ]);

        $titulo = strip_tags($request->descripcion ?? '');
        $titulo = mb_substr($titulo, 0, 255) ?: null;
        $bulto = ProductoTodotex::normalizeBulto($request->bulto);
        $bultoCantidad = $request->filled('bulto_cantidad')
            ? max(1, (int) $request->bulto_cantidad)
            : ($bulto !== null ? ProductoTodotex::deriveBultoCantidad($bulto) : null);
        $colorData = $this->resolveColorData(
            $request->input('color_todotex_id'),
            $request->codigo
        );

        $producto->update([
            'titulo' => $titulo,
            'orden' => $request->orden,
            'destacado' => $request->boolean('destacado'),
            'visible' => $request->boolean('visible'),
            'descripcion' => $request->descripcion,
            'codigo' => $request->codigo,
            'color_todotex_id' => $colorData['color_todotex_id'],
            'codigo_color' => $colorData['codigo_color'],
            'nombre_color' => $colorData['nombre_color'],
            'presentacion' => $request->presentacion,
            'precio_paquete' => $request->precio_paquete,
            'precio_unitario' => $request->precio_unitario,
            'precio_kg' => $request->precio_kg,
            'porcentaje_aumento' => $request->porcentaje_aumento,
            'descuento' => $request->descuento,
            'bulto' => $bulto,
            'bulto_cantidad' => $bultoCantidad,
        ]);

        $producto->categorias()->sync($request->input('categorias', []));

        // Update portada for existing images
        if ($request->filled('portada_id')) {
            ProductoGallery::where('producto_id', $producto->id)->update(['portada' => false]);
            ProductoGallery::where('producto_id', $producto->id)
                ->where('id', $request->portada_id)
                ->update(['portada' => true]);
        }

        $portadaIndex = $request->input('portada_index', null);
        if ($request->hasFile('images')) {
            // If portada_index points to a new image, clear existing portadas
            if ($portadaIndex !== null && !$request->filled('portada_id')) {
                ProductoGallery::where('producto_id', $producto->id)->update(['portada' => false]);
            }
            foreach ($request->file('images') as $i => $image) {
                $path = $image->store('productos/gallery', 'public');
                ProductoGallery::create([
                    'producto_id' => $producto->id,
                    'image' => $path,
                    'portada' => ($portadaIndex !== null && $i == $portadaIndex && !$request->filled('portada_id')),
                ]);
            }
        }

        $backPage = (int) $request->input('back_page', 1);

        return response()->json([
            'success'  => true,
            'message'  => 'Producto actualizado correctamente',
            'redirect' => route('productos-todotex.index') . '?page=' . $backPage,
        ]);
    }

    public function destroy($id)
    {
        $producto = ProductoTodotex::with('gallery')->findOrFail($id);

        foreach ($producto->gallery as $image) {
            if (Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
            $image->delete();
        }

        $producto->categorias()->detach();
        $producto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente',
        ]);
    }

    public function toggleVisible($id)
    {
        $producto = ProductoTodotex::findOrFail($id);
        $producto->visible = !$producto->visible;
        $producto->save();

        return response()->json([
            'success' => true,
            'visible' => (bool) $producto->visible,
            'message' => $producto->visible ? 'Producto visible' : 'Producto oculto',
        ]);
    }

    public function destroyGalleryImage($id, $imageId)
    {
        $producto = ProductoTodotex::findOrFail($id);
        $image = ProductoGallery::where('producto_id', $producto->id)->findOrFail($imageId);

        if (Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada correctamente',
        ]);
    }

    protected function resolveColorData($colorTodotexId, ?string $codigo): array
    {
        if (filled($colorTodotexId)) {
            $color = ColorTodotex::find($colorTodotexId);

            if ($color) {
                return [
                    'color_todotex_id' => $color->id,
                    'codigo_color' => $color->codigo_color,
                    'nombre_color' => $color->titulo,
                ];
            }
        }

        $derivedColor = ProductoTodotex::deriveColorData($codigo);

        if (filled($derivedColor['codigo_color'])) {
            $matchedColor = ColorTodotex::query()
                ->where('codigo_color', $derivedColor['codigo_color'])
                ->first();

            return [
                'color_todotex_id' => $matchedColor?->id,
                'codigo_color' => $matchedColor?->codigo_color ?? $derivedColor['codigo_color'],
                'nombre_color' => $matchedColor?->titulo ?? $derivedColor['nombre_color'],
            ];
        }

        return [
            'color_todotex_id' => null,
            'codigo_color' => null,
            'nombre_color' => null,
        ];
    }
}
