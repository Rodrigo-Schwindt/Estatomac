<?php

namespace App\Http\Controllers\Precio;

use App\Http\Controllers\Controller;
use App\Models\Precio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrecioController extends Controller
{
    public function index(Request $request)
    {
        $query = Precio::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $precios = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('livewire.precio.partials.table', compact('precios'))->render(),
                'pagination' => view('livewire.precio.partials.pagination', compact('precios'))->render(),
            ]);
        }

        return view('livewire.precio.index', compact('precios'));
    }

    public function create()
    {
        return view('livewire.precio.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf,xls,xlsx|max:10240',
        ]);

        $path = $request->file('archivo')->store('precios', 'public');

        Precio::create([
            'title' => $request->title,
            'archivo' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lista de precios creada correctamente',
            'redirect' => route('precios.index')
        ]);
    }

    public function destroy($id)
    {
        $precio = Precio::findOrFail($id);
        Storage::disk('public')->delete($precio->archivo);
        $precio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Eliminado correctamente'
        ]);
    }
}