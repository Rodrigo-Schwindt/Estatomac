<?php

namespace App\Http\Controllers\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('livewire.usuarios.index', compact('users'));
    }

    public function create()
    {
        return view('livewire.usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:admin,user',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('livewire.usuarios.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $u = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $u->id,
            'role'     => 'required|in:admin,user',
            'password' => 'nullable|min:6',
        ]);

        $u->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'password' => $request->password ? Hash::make($request->password) : $u->password,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy($id)
    {
        if ($id == auth()->id()) {
            return back()->with('error', 'No podés eliminar tu propio usuario');
        }

        User::findOrFail($id)->delete();

        return back()->with('success', 'Usuario eliminado correctamente');
    }
}
