<?php

namespace App\Http\Controllers\Familias;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FamiliaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortField = $request->get('sortField', 'orden');
        $sortDirection = $request->get('sortDirection', 'asc');
        $perPage = $request->get('per_page', 15);

        $query = Familia::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('orden', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['orden', 'titulo', 'destacado', 'visible', 'created_at'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'orden';
        }

        if ($sortField === 'orden') {
            $dir = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';
            $familias = $query->orderByRaw("orden IS NULL, CAST(orden AS UNSIGNED) {$dir}")->paginate($perPage);
        } else {
            $familias = $query->orderBy($sortField, $sortDirection)->paginate($perPage);
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('livewire.familias.partials.table', compact('familias'))->render(),
                'pagination' => view('livewire.familias.partials.pagination', compact('familias'))->render(),
                'stats' => [
                    'total' => $familias->total(),
                    'from' => $familias->firstItem(),
                    'to' => $familias->lastItem(),
                    'current_page' => $familias->currentPage(),
                    'last_page' => $familias->lastPage(),
                ],
            ]);
        }

        $bannerTodos = DB::table('configuraciones')->where('clave', 'banner_todos_productos')->value('valor');

        return view('livewire.familias.index', compact('familias', 'bannerTodos'));
    }

    public function create()
    {
        return view('livewire.familias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'orden' => 'nullable|string|max:50',
            'destacado' => 'nullable|boolean',
            'visible' => 'nullable|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $data = [
            'titulo' => $request->titulo,
            'orden' => $request->orden,
            'destacado' => $request->boolean('destacado'),
            'visible' => $request->boolean('visible', true),
        ];

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('categorias/familias', 'public');
        }

        Familia::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Familia creada correctamente',
            'redirect' => route('familias.index'),
        ]);
    }

    public function edit($id)
    {
        $familia = Familia::findOrFail($id);
        return view('livewire.familias.edit', compact('familia'));
    }

    public function update(Request $request, $id)
    {
        $familia = Familia::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'orden' => 'nullable|string|max:50',
            'destacado' => 'nullable|boolean',
            'visible' => 'nullable|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $data = [
            'titulo' => $request->titulo,
            'orden' => $request->orden,
            'destacado' => $request->boolean('destacado'),
            'visible' => $request->boolean('visible', true),
        ];

        if ($request->hasFile('imagen')) {
            if ($familia->imagen) {
                Storage::disk('public')->delete($familia->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('categorias/familias', 'public');
        }

        $familia->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Familia actualizada correctamente',
            'redirect' => route('familias.index'),
        ]);
    }

    public function deleteImagen($id)
    {
        $familia = Familia::findOrFail($id);

        if ($familia->imagen) {
            Storage::disk('public')->delete($familia->imagen);
            $familia->update(['imagen' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada correctamente',
        ]);
    }

    public function destroy($id)
    {
        $familia = Familia::findOrFail($id);

        if ($familia->imagen) {
            Storage::disk('public')->delete($familia->imagen);
        }

        $familia->delete();

        return response()->json([
            'success' => true,
            'message' => 'Familia eliminada correctamente',
        ]);
    }

    public function uploadBannerTodos(Request $request)
    {
        $request->validate(['imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072']);

        $current = DB::table('configuraciones')->where('clave', 'banner_todos_productos')->value('valor');
        if ($current) {
            Storage::disk('public')->delete($current);
        }

        $path = $request->file('imagen')->store('configuraciones', 'public');

        DB::table('configuraciones')->updateOrInsert(
            ['clave' => 'banner_todos_productos'],
            ['valor' => $path]
        );

        return response()->json([
            'success' => true,
            'url'     => Storage::url($path),
        ]);
    }

    public function deleteBannerTodos()
    {
        $current = DB::table('configuraciones')->where('clave', 'banner_todos_productos')->value('valor');
        if ($current) {
            Storage::disk('public')->delete($current);
            DB::table('configuraciones')->where('clave', 'banner_todos_productos')->delete();
        }

        return response()->json(['success' => true]);
    }
}
