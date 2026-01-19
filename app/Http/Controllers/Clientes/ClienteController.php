<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('usuario', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cuit', 'like', "%{$search}%")
                  ->orWhere('cuil', 'like', "%{$search}%");
            });
        }

        $sortField = $request->get('sortField', 'nombre');
        $sortDirection = $request->get('sortDirection', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $clientes = $query->paginate(10);

        if ($request->ajax()) {
            $html = view('livewire.clientes.partials.table', compact('clientes'))->render();
            $pagination = view('livewire.clientes.partials.pagination', compact('clientes'))->render();
            
            return response()->json([
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('livewire.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('livewire.clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string|max:255|unique:clientes,usuario',
            'password' => 'required|string|min:8|confirmed',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clientes,email',
            'cuil' => 'nullable|string|max:255',
            'cuit' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'domicilio' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'activo' => 'required|boolean',
        ]);

        Cliente::create([
            'usuario' => $request->usuario,
            'password' => Hash::make($request->password),
            'nombre' => $request->nombre,
            'email' => $request->email,
            'cuil' => $request->cuil,
            'cuit' => $request->cuit,
            'telefono' => $request->telefono,
            'domicilio' => $request->domicilio,
            'localidad' => $request->localidad,
            'provincia' => $request->provincia,
            'descuento' => $request->descuento,
            'activo' => $request->activo,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente');
    }

    public function edit(Cliente $cliente)
    {
        return view('livewire.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
{
    $rules = [
        'usuario' => 'required|string|max:255|unique:clientes,usuario,' . $cliente->id,
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:clientes,email,' . $cliente->id,
        'cuil' => 'nullable|string|max:255',
        'cuit' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:255',
        'domicilio' => 'nullable|string|max:255',
        'localidad' => 'nullable|string|max:255',
        'provincia' => 'nullable|string|max:255',
        'descuento' => 'nullable|numeric|min:0|max:100',
        'activo' => 'required|boolean',
    ];

    // Solo validar contraseña si se proporciona
    if ($request->filled('password')) {
        $rules['password'] = 'required|string|min:8|confirmed';
    }

    $validated = $request->validate($rules);

    $data = [
        'usuario' => $request->usuario,
        'nombre' => $request->nombre,
        'email' => $request->email,
        'cuil' => $request->cuil,
        'cuit' => $request->cuit,
        'telefono' => $request->telefono,
        'domicilio' => $request->domicilio,
        'localidad' => $request->localidad,
        'provincia' => $request->provincia,
        'descuento' => $request->descuento,
        'activo' => $request->activo,
    ];

    // Solo actualizar contraseña si se proporciona
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $cliente->update($data);

    return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente');
}

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado exitosamente'
        ]);
    }

    public function toggleActivo(Cliente $cliente)
    {
        $cliente->activo = !$cliente->activo;
        $cliente->save();

        return response()->json([
            'success' => true,
            'activo' => $cliente->activo,
            'message' => $cliente->activo ? 'Cliente activado' : 'Cliente desactivado'
        ]);
    }
}