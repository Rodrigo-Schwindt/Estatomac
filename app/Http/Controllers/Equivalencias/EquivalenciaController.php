<?php

namespace App\Http\Controllers\Equivalencias;

use App\Http\Controllers\Controller;
use App\Models\Equivalencia;
use App\Models\Producto;
use Illuminate\Http\Request;

class EquivalenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Equivalencia::with('producto');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('order', 'like', "%{$search}%")
                  ->orWhereHas('producto', function($q2) use ($search) {
                      $q2->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $sortField = $request->get('sortField', 'order');
        $sortDirection = $request->get('sortDirection', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $equivalencias = $query->paginate(10);

        if ($request->ajax()) {
            $html = view('livewire.equivalencias.partials.table', compact('equivalencias'))->render();
            $pagination = view('livewire.equivalencias.partials.pagination', compact('equivalencias'))->render();
            
            return response()->json([
                'html' => $html,
                'pagination' => $pagination
            ]);
        }

        return view('livewire.equivalencias.index', compact('equivalencias'));
    }

    public function create()
    {
        $productos = Producto::orderBy('title')->get();
        return view('livewire.equivalencias.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'order' => 'nullable|string|max:255',
        ]);

        Equivalencia::create([
            'producto_id' => $request->producto_id,
            'title' => $request->title,
            'code' => $request->code,
            'order' => $request->order,
        ]);

        return redirect()->route('equivalencias.index')->with('success', 'Equivalencia creada exitosamente');
    }

    public function edit(Equivalencia $equivalencia)
    {
        $productos = Producto::orderBy('title')->get();
        return view('livewire.equivalencias.edit', compact('equivalencia', 'productos'));
    }

    public function update(Request $request, Equivalencia $equivalencia)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'order' => 'nullable|string|max:255',
        ]);

        $equivalencia->update([
            'producto_id' => $request->producto_id,
            'title' => $request->title,
            'code' => $request->code,
            'order' => $request->order,
        ]);

        return redirect()->route('equivalencias.index')->with('success', 'Equivalencia actualizada exitosamente');
    }

    public function destroy(Equivalencia $equivalencia)
    {
        $equivalencia->delete();

        return response()->json([
            'success' => true,
            'message' => 'Equivalencia eliminada exitosamente'
        ]);
    }
}