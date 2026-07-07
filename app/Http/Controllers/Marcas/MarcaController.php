<?php

namespace App\Http\Controllers\Marcas;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index(Request $request)
    {
        $query = Marca::with('modelos');

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

        $marcas = $query->paginate(10);

        if ($request->ajax()) {
            $html = view('livewire.marcas.partials.table', compact('marcas'))->render();
            $pagination = view('livewire.marcas.partials.pagination', compact('marcas'))->render();
            
            return response()->json([
                'html' => $html,
                'pagination' => $pagination
            ]);
        }

        return view('livewire.marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('livewire.marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|string|max:255',
            'modelos' => 'nullable|array',
            'modelos.*.title' => 'required|string|max:255',
            'modelos.*.order' => 'nullable|string|max:255',
        ]);

        $marca = new Marca();
        $marca->title = $request->title;
        $marca->order = $request->order;
        $marca->save();

        if ($request->has('modelos')) {
            foreach ($request->modelos as $modeloData) {
                Modelo::create([
                    'marca_id' => $marca->id,
                    'title' => $modeloData['title'],
                    'order' => $modeloData['order'] ?? null,
                ]);
            }
        }

        return redirect()->route('marcas.index')->with('success', 'Marca creada exitosamente');
    }

    public function edit(Marca $marca)
    {
        $marca->load('modelos');
        return view('livewire.marcas.edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|string|max:255',
            'modelos' => 'nullable|array',
            'modelos.*.id' => 'nullable|exists:modelos,id',
            'modelos.*.title' => 'required|string|max:255',
            'modelos.*.order' => 'nullable|string|max:255',
            'deleted_modelos' => 'nullable|array',
        ]);

        $marca->title = $request->title;
        $marca->order = $request->order;
        $marca->save();

        if ($request->has('deleted_modelos')) {
            Modelo::whereIn('id', $request->deleted_modelos)->delete();
        }

        if ($request->has('modelos')) {
            foreach ($request->modelos as $modeloData) {
                if (isset($modeloData['id'])) {
                    $modelo = Modelo::find($modeloData['id']);
                    if ($modelo) {
                        $modelo->update([
                            'title' => $modeloData['title'],
                            'order' => $modeloData['order'] ?? null,
                        ]);
                    }
                } else {
                    Modelo::create([
                        'marca_id' => $marca->id,
                        'title' => $modeloData['title'],
                        'order' => $modeloData['order'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada exitosamente');
    }

    public function destroy(Marca $marca)
    {
        $marca->delete();

        return response()->json([
            'success' => true,
            'message' => 'Marca eliminada exitosamente'
        ]);
    }
}