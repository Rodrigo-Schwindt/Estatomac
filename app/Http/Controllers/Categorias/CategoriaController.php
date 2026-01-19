<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::with('subcategorias');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('order', 'like', "%{$search}%");
            });
        }

        $sortField = $request->get('sortField', 'order');
        $sortDirection = $request->get('sortDirection', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $categorias = $query->paginate(10);
        $banner = Banner::forSection('categorias')->first();

        if ($request->ajax()) {
            $html = view('livewire.categorias.partials.table', compact('categorias'))->render();
            $pagination = view('livewire.categorias.partials.pagination', compact('categorias'))->render();
            
            return response()->json([
                'html' => $html,
                'pagination' => $pagination
            ]);
        }

        return view('livewire.categorias.index', compact('categorias', 'banner'));
    }

    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner_title' => 'nullable|string|max:255',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $banner = Banner::forSection('categorias')->first();

        if (!$banner) {
            $banner = new Banner();
            $banner->section = 'categorias';
        }

        $banner->title = $request->banner_title;

        if ($request->hasFile('banner_image')) {
            if ($banner->image_banner) {
                Storage::disk('public')->delete($banner->image_banner);
            }
            $banner->image_banner = $request->file('banner_image')->store('banners', 'public');
        }

        $banner->save();

        return redirect()->route('categorias.index')->with('success', 'Banner actualizado exitosamente');
    }

    public function deleteBannerImage()
    {
        $banner = Banner::forSection('categorias')->first();

        if ($banner && $banner->image_banner) {
            Storage::disk('public')->delete($banner->image_banner);
            $banner->image_banner = null;
            $banner->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Imagen del banner eliminada exitosamente'
        ]);
    }

    public function create()
    {
        return view('livewire.categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|string|max:255',
            'destacado' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'subcategorias' => 'nullable|array',
            'subcategorias.*.title' => 'required|string|max:255',
            'subcategorias.*.order' => 'nullable|string|max:255',
            'subcategorias.*.tolerancia' => 'required|boolean', // AGREGADO
        ]);

        $categoria = new Categoria();
        $categoria->title = $request->title;
        $categoria->order = $request->order;
        $categoria->destacado = $request->destacado;

        if ($request->hasFile('image')) {
            $categoria->image = $request->file('image')->store('categorias', 'public');
        }

        $categoria->save();

        if ($request->has('subcategorias')) {
            foreach ($request->subcategorias as $subcategoriaData) {
                Subcategoria::create([
                    'categoria_id' => $categoria->id,
                    'title' => $subcategoriaData['title'],
                    'order' => $subcategoriaData['order'] ?? null,
                    'tolerancia' => $subcategoriaData['tolerancia'] ?? 0, // AGREGADO
                ]);
            }
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente');
    }

    public function edit(Categoria $categoria)
    {
        $categoria->load('subcategorias');
        return view('livewire.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|string|max:255',
            'destacado' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'subcategorias' => 'nullable|array',
            'subcategorias.*.id' => 'nullable|exists:subcategorias,id',
            'subcategorias.*.title' => 'required|string|max:255',
            'subcategorias.*.order' => 'nullable|string|max:255',
            'subcategorias.*.tolerancia' => 'required|boolean', // AGREGADO
            'deleted_subcategorias' => 'nullable|array',
        ]);

        $categoria->title = $request->title;
        $categoria->order = $request->order;
        $categoria->destacado = $request->destacado;

        if ($request->hasFile('image')) {
            if ($categoria->image) {
                Storage::disk('public')->delete($categoria->image);
            }
            $categoria->image = $request->file('image')->store('categorias', 'public');
        }

        $categoria->save();

        if ($request->has('deleted_subcategorias')) {
            Subcategoria::whereIn('id', $request->deleted_subcategorias)->delete();
        }

        if ($request->has('subcategorias')) {
            foreach ($request->subcategorias as $subcategoriaData) {
                if (isset($subcategoriaData['id'])) {
                    $subcategoria = Subcategoria::find($subcategoriaData['id']);
                    if ($subcategoria) {
                        $subcategoria->update([
                            'title' => $subcategoriaData['title'],
                            'order' => $subcategoriaData['order'] ?? null,
                            'tolerancia' => $subcategoriaData['tolerancia'] ?? 0, // AGREGADO
                        ]);
                    }
                } else {
                    Subcategoria::create([
                        'categoria_id' => $categoria->id,
                        'title' => $subcategoriaData['title'],
                        'order' => $subcategoriaData['order'] ?? null,
                        'tolerancia' => $subcategoriaData['tolerancia'] ?? 0, // AGREGADO
                    ]);
                }
            }
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->image) {
            Storage::disk('public')->delete($categoria->image);
        }

        $categoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente'
        ]);
    }
}