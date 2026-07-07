<?php

namespace App\Http\Controllers\Colores;

use App\Http\Controllers\Controller;
use App\Models\ColorTodotex;
use Illuminate\Http\Request;

class ColorTodotexController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortField = $request->get('sortField', 'orden');
        $sortDirection = $request->get('sortDirection', 'asc');
        $perPage = $request->get('per_page', 15);

        $query = ColorTodotex::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('codigo_color', 'like', "%{$search}%")
                    ->orWhere('orden', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['orden', 'titulo', 'codigo_color', 'created_at'];
        if (!in_array($sortField, $allowedSorts, true)) {
            $sortField = 'orden';
        }

        $colores = $query
            ->orderByRaw('CASE WHEN orden IS NULL THEN 1 ELSE 0 END')
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('livewire.colores-todotex.partials.table', compact('colores'))->render(),
                'pagination' => view('livewire.colores-todotex.partials.pagination', compact('colores'))->render(),
                'stats' => [
                    'total' => $colores->total(),
                    'from' => $colores->firstItem(),
                    'to' => $colores->lastItem(),
                    'current_page' => $colores->currentPage(),
                    'last_page' => $colores->lastPage(),
                ],
            ]);
        }

        return view('livewire.colores-todotex.index', compact('colores'));
    }

    public function create()
    {
        return view('livewire.colores-todotex.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'codigo_color' => ['required', 'regex:/^\d{4}$/', 'unique:colores_todotex,codigo_color'],
            'orden' => 'nullable|integer|min:0',
        ]);

        $codigoColor = trim((string) $request->codigo_color);

        ColorTodotex::create([
            'titulo' => $request->titulo,
            'codigo_color' => $codigoColor,
            'orden' => $request->filled('orden') ? (int) $request->orden : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Color creado correctamente',
            'redirect' => route('colores-todotex.index'),
        ]);
    }

    public function edit($id)
    {
        $color = ColorTodotex::findOrFail($id);

        return view('livewire.colores-todotex.edit', compact('color'));
    }

    public function update(Request $request, $id)
    {
        $color = ColorTodotex::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'codigo_color' => ['required', 'regex:/^\d{4}$/', 'unique:colores_todotex,codigo_color,' . $color->id],
            'orden' => 'nullable|integer|min:0',
        ]);

        $codigoColor = trim((string) $request->codigo_color);

        $color->update([
            'titulo' => $request->titulo,
            'codigo_color' => $codigoColor,
            'orden' => $request->filled('orden') ? (int) $request->orden : null,
        ]);

        $color->productos()->update([
            'codigo_color' => $color->codigo_color,
            'nombre_color' => $color->titulo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Color actualizado correctamente',
            'redirect' => route('colores-todotex.index'),
        ]);
    }

    public function destroy($id)
    {
        $color = ColorTodotex::findOrFail($id);
        $color->delete();

        return response()->json([
            'success' => true,
            'message' => 'Color eliminado correctamente',
        ]);
    }
}
