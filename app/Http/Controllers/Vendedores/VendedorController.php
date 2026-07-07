<?php

namespace App\Http\Controllers\Vendedores;

use App\Http\Controllers\Controller;
use App\Models\Vendedor;
use Illuminate\Http\Request;

class VendedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendedor::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%")
                  ->orWhere('celular', 'like', "%{$search}%");
            });
        }

        $sortField = $request->get('sortField', 'nombre');
        $sortDirection = $request->get('sortDirection', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $vendedores = $query->paginate(15);

        if ($request->ajax()) {
            $html = view('livewire.vendedores.partials.table', compact('vendedores'))->render();
            $pagination = view('livewire.vendedores.partials.pagination', compact('vendedores'))->render();

            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
            ]);
        }

        return view('livewire.vendedores.index', compact('vendedores'));
    }

    public function create()
    {
        $todosVendedores = Vendedor::orderBy('nombre')->get(['id', 'nombre']);
        return view('livewire.vendedores.create', compact('todosVendedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'     => 'required|string|max:255',
            'email'      => 'nullable|email|unique:vendedores,email|max:255',
            'telefono'   => 'nullable|string|max:255',
            'celular'    => 'nullable|string|max:255',
            'whatsapp'   => 'nullable|string|max:255',
            'comision'   => 'nullable|numeric|min:0|max:100',
            'opera_como' => 'nullable|integer|exists:vendedores,id',
            'activo'     => 'required|boolean',
            'password'   => 'nullable|string|min:6',
        ]);

        Vendedor::create([
            'nombre'     => $request->nombre,
            'email'      => $request->email ?: null,
            'telefono'   => $request->telefono,
            'celular'    => $request->celular,
            'whatsapp'   => $request->whatsapp,
            'comision'   => $request->comision ?? 0,
            'opera_como' => $request->opera_como ?: null,
            'activo'     => $request->activo,
            'password'   => $request->filled('password') ? $request->password : 'todotex2025',
        ]);

        return redirect()->route('vendedores.index')->with('success', 'Vendedor creado exitosamente');
    }

    public function edit(Vendedor $vendedor)
    {
        $otrosVendedores = Vendedor::where('id', '!=', $vendedor->id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('livewire.vendedores.edit', compact('vendedor', 'otrosVendedores'));
    }

    public function update(Request $request, Vendedor $vendedor)
    {
        $request->validate([
            'nombre'     => 'required|string|max:255',
            'email'      => 'nullable|email|unique:vendedores,email,' . $vendedor->id . '|max:255',
            'telefono'   => 'nullable|string|max:255',
            'celular'    => 'nullable|string|max:255',
            'whatsapp'   => 'nullable|string|max:255',
            'comision'   => 'nullable|numeric|min:0|max:100',
            'opera_como' => 'nullable|integer|exists:vendedores,id',
            'password'   => 'nullable|string|min:6',
        ]);

        $data = [
            'nombre'     => $request->nombre,
            'email'      => $request->email ?: null,
            'telefono'   => $request->telefono,
            'celular'    => $request->celular,
            'whatsapp'   => $request->whatsapp,
            'comision'   => $request->comision ?? 0,
            'opera_como' => $request->opera_como ?: null,
            'activo'     => $request->has('activo') && $request->activo == 1,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $vendedor->update($data);

        return redirect()->route('vendedores.index')->with('success', 'Vendedor actualizado exitosamente');
    }

    public function destroy(Vendedor $vendedor)
    {
        $vendedor->clientes()->update(['vendedor_id' => null]);
        $vendedor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendedor eliminado exitosamente',
        ]);
    }

    public function resetPassword(Vendedor $vendedor)
    {
        $vendedor->password            = null;
        $vendedor->password_changed_at = null;
        $vendedor->save();

        return response()->json([
            'success' => true,
            'message' => "Password del vendedor '{$vendedor->nombre}' blanqueada. Se le pedirá definirla en su próximo ingreso.",
        ]);
    }

    public function toggleActivo(Vendedor $vendedor)
    {
        $vendedor->activo = !$vendedor->activo;
        $vendedor->save();

        return response()->json([
            'success' => true,
            'activo' => $vendedor->activo,
            'message' => $vendedor->activo ? 'Vendedor activado' : 'Vendedor desactivado',
        ]);
    }
}
