<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Models\CategoriaPadre;
use Illuminate\Http\Request;

class CategoriaPadreController extends Controller
{
    public function index(Request $request)
    {
        $query = CategoriaPadre::query();

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

        $categoriasPadre = $query->paginate(10);

        if ($request->ajax()) {
            $html = view('livewire.categorias-padre.partials.table', compact('categoriasPadre'))->render();
            $pagination = view('livewire.categorias-padre.partials.pagination', compact('categoriasPadre'))->render();
            
            return response()->json([
                'html' => $html,
                'pagination' => $pagination
            ]);
        }

        return view('livewire.categorias-padre.index', compact('categoriasPadre'));
    }

    public function create()
    {
        return view('livewire.categorias-padre.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|string|max:255',
        ]);

        CategoriaPadre::create([
            'title' => $request->title,
            'order' => $request->order,
        ]);

        return redirect()->route('categorias-padre.index')->with('success', 'Categoría Padre creada exitosamente');
    }

    public function edit($id)
    {
        $categoriaPadre = CategoriaPadre::findOrFail($id);
        return view('livewire.categorias-padre.edit', compact('categoriaPadre'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|string|max:255',
        ]);

        $categoriaPadre = CategoriaPadre::findOrFail($id);
        
        $categoriaPadre->update([
            'title' => $request->title,
            'order' => $request->order,
        ]);

        return redirect()->route('categorias-padre.index')->with('success', 'Categoría Padre actualizada exitosamente');
    }

    public function destroy($id)
    {
        $categoriaPadre = CategoriaPadre::findOrFail($id);
        $index = CategoriaPadre::orderBy('order', 'asc')->pluck('id')->search($categoriaPadre->id);

        if ($index !== false && $index < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Las primeras dos categorías padre están protegidas y no se pueden eliminar'
            ], 403);
        }

        $categoriaPadre->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría Padre eliminada exitosamente'
        ]);
    }
}