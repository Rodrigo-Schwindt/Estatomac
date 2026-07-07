<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('nombre_fantasia', 'like', "%{$search}%")
                  ->orWhere('usuario', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cuit', 'like', "%{$search}%")
                  ->orWhere('cuil', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
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
                'pagination' => $pagination,
            ]);
        }

        return view('livewire.clientes.index', compact('clientes'));
    }

    public function create()
    {
        $vendedores = Vendedor::where('activo', true)->orderBy('nombre')->get();
        return view('livewire.clientes.create', compact('vendedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario'         => 'required|string|max:255|unique:clientes,usuario',
            'password'        => 'required|string|min:8|confirmed',
            'nombre'          => 'required|string|max:255',
            'codigo'          => 'nullable|string|max:255|unique:clientes,codigo',
            'email'           => 'nullable|email|max:255|unique:clientes,email',
            'cuil'            => 'nullable|string|max:255',
            'cuit'            => 'nullable|string|max:255',
            'telefono'        => 'nullable|string|max:255',
            'domicilio'       => 'nullable|string|max:255',
            'localidad'       => 'nullable|string|max:255',
            'provincia'       => 'nullable|string|max:255',
            'vendedor_id'     => 'nullable|exists:vendedores,id',
            'descuento'       => 'nullable|numeric|min:0',
            'descuento_canal' => 'nullable|numeric|min:0',
            'activo'          => 'required|boolean',
        ]);

        Cliente::create([
            'usuario'         => $request->usuario,
            'password'        => Hash::make($request->password),
            'codigo'          => $request->codigo ?: null,
            'nombre'          => $request->nombre,
            'nombre_fantasia' => $request->nombre_fantasia,
            'email'           => $request->email ?: null,
            'cuil'            => $request->cuil,
            'cuit'            => $request->cuit,
            'condicion_iva'   => $request->condicion_iva,
            'telefono'        => $request->telefono,
            'celular'         => $request->celular,
            'whatsapp'        => $request->whatsapp,
            'domicilio'       => $request->domicilio,
            'localidad'       => $request->localidad,
            'codigo_postal'   => $request->codigo_postal,
            'provincia'       => $request->provincia,
            'condicion_venta' => $request->condicion_venta,
            'tipo_operacion'  => $request->tipo_operacion,
            'descuento'       => $request->descuento,
            'transporte'      => $request->transporte,
            'vendedor_id'     => $request->vendedor_id ?: null,
            'rubro_cliente'   => $request->rubro_cliente,
            'tipo_lista'      => $request->tipo_lista,
            'canal'           => $request->canal,
            'descuento_canal' => $request->descuento_canal,
            'activo'          => $request->activo,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente');
    }

    public function edit(Cliente $cliente)
    {
        $vendedores = Vendedor::where('activo', true)->orderBy('nombre')->get();
        return view('livewire.clientes.edit', compact('cliente', 'vendedores'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $rules = [
            'usuario'         => 'required|string|max:255|unique:clientes,usuario,' . $cliente->id,
            'nombre'          => 'required|string|max:255',
            'email'           => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'cuil'            => 'nullable|string|max:255',
            'cuit'            => 'nullable|string|max:255',
            'telefono'        => 'nullable|string|max:255',
            'domicilio'       => 'nullable|string|max:255',
            'localidad'       => 'nullable|string|max:255',
            'provincia'       => 'nullable|string|max:255',
            'vendedor_id'     => 'nullable|exists:vendedores,id',
            'descuento'       => 'nullable|numeric|min:0',
            'descuento_canal' => 'nullable|numeric|min:0',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $wasInactive = !$cliente->activo;
        $willBeActive = $request->has('activo') && $request->activo == 1;

        $data = [
            'usuario'         => $request->usuario,
            'nombre'          => $request->nombre,
            'nombre_fantasia' => $request->nombre_fantasia,
            'email'           => $request->email ?: null,
            'cuil'            => $request->cuil,
            'cuit'            => $request->cuit,
            'condicion_iva'   => $request->condicion_iva,
            'telefono'        => $request->telefono,
            'celular'         => $request->celular,
            'whatsapp'        => $request->whatsapp,
            'domicilio'       => $request->domicilio,
            'localidad'       => $request->localidad,
            'codigo_postal'   => $request->codigo_postal,
            'provincia'       => $request->provincia,
            'condicion_venta' => $request->condicion_venta,
            'tipo_operacion'  => $request->tipo_operacion,
            'descuento'       => $request->descuento,
            'transporte'      => $request->transporte,
            'vendedor_id'     => $request->vendedor_id ?: null,
            'rubro_cliente'   => $request->rubro_cliente,
            'tipo_lista'      => $request->tipo_lista,
            'canal'           => $request->canal,
            'descuento_canal' => $request->descuento_canal,
            'activo'          => $willBeActive,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $cliente->update($data);

        if ($wasInactive && $willBeActive && $cliente->email) {
            $contactData = \App\Models\Contact::first();

            $mailData = [
                'nombre'              => $cliente->nombre,
                'usuario'             => $cliente->usuario,
                'email'               => $cliente->email,
                'url_login'           => route('home'),
                'contacto_email'      => $contactData->mail_adm ?? 'info@Todotex.com',
                'contacto_telefono'   => $contactData->phone_amd ?? null,
                'contacto_whatsapp'   => $contactData->wssp ?? null,
            ];

            try {
                \Illuminate\Support\Facades\Mail::to($cliente->email)
                    ->send(new \App\Mail\CuentaAprobadaMail($mailData));
            } catch (\Exception $e) {
                \Log::error('Error al enviar email de aprobación: ' . $e->getMessage());
            }
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado exitosamente',
        ]);
    }

    public function resetPassword(Cliente $cliente)
    {
        $cliente->password            = null;
        $cliente->password_changed_at = null;
        $cliente->save();

        return response()->json([
            'success' => true,
            'message' => "Password del cliente '{$cliente->nombre}' blanqueada. Se le pedirá definirla en su próximo ingreso.",
        ]);
    }

    public function toggleActivo(Cliente $cliente)
    {
        $wasInactive = !$cliente->activo;

        $cliente->activo = !$cliente->activo;
        $cliente->save();

        if ($wasInactive && $cliente->activo && $cliente->email) {
            $contactData = \App\Models\Contact::first();

            $mailData = [
                'nombre'            => $cliente->nombre,
                'usuario'           => $cliente->usuario,
                'email'             => $cliente->email,
                'url_login'         => route('home'),
                'contacto_email'    => $contactData->mail_adm ?? 'info@Todotex.com',
                'contacto_telefono' => $contactData->phone_amd ?? null,
                'contacto_whatsapp' => $contactData->wssp ?? null,
            ];

            try {
                \Illuminate\Support\Facades\Mail::to($cliente->email)
                    ->send(new \App\Mail\CuentaAprobadaMail($mailData));
            } catch (\Exception $e) {
                \Log::error('Error al enviar email de aprobación: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'activo'  => $cliente->activo,
            'message' => $cliente->activo ? 'Cliente activado' : 'Cliente desactivado',
        ]);
    }
}
