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

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $precios = $query->orderByDesc('vigente_sn')->orderByDesc('fecha_desde')->orderByDesc('id')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('livewire.precio.partials.table', compact('precios'))->render(),
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
        $data = $request->validate([
            'numero'        => 'nullable|integer',
            'title'         => 'required|string|max:255',
            'fecha_desde'   => 'nullable|date',
            'observaciones' => 'nullable|string|max:1000',
            'vigente_sn'    => 'nullable|boolean',
            'archivo'       => 'required|file|mimes:pdf,xls,xlsx|max:30240',
        ]);

        $data['archivo']    = $request->file('archivo')->store('precios', 'public');
        $data['vigente_sn'] = $request->boolean('vigente_sn');

        $precio = Precio::create($data);

        if ($precio->vigente_sn) {
            $precio->activar();
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Lista de precios creada correctamente',
            'redirect' => route('precios.index'),
        ]);
    }

    public function edit(Precio $precio)
    {
        return view('livewire.precio.edit', compact('precio'));
    }

    public function update(Request $request, Precio $precio)
    {
        $data = $request->validate([
            'numero'        => 'nullable|integer',
            'title'         => 'required|string|max:255',
            'fecha_desde'   => 'nullable|date',
            'observaciones' => 'nullable|string|max:1000',
            'vigente_sn'    => 'nullable|boolean',
            'archivo'       => 'nullable|file|mimes:pdf,xls,xlsx|max:30240',
        ]);

        $data['vigente_sn'] = $request->boolean('vigente_sn');

        if ($request->hasFile('archivo')) {
            if ($precio->archivo) {
                Storage::disk('public')->delete($precio->archivo);
            }
            $data['archivo'] = $request->file('archivo')->store('precios', 'public');
        }

        $precio->update($data);

        if ($precio->vigente_sn) {
            $precio->activar();
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Lista de precios actualizada correctamente',
            'redirect' => route('precios.index'),
        ]);
    }

    public function activar(Precio $precio)
    {
        $precio->activar();
        return back()->with('success', "Lista '{$precio->title}' activada como vigente.");
    }

    public function destroy($id)
    {
        $precio = Precio::findOrFail($id);
        if ($precio->archivo) {
            Storage::disk('public')->delete($precio->archivo);
        }
        $precio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Eliminado correctamente',
        ]);
    }
}
