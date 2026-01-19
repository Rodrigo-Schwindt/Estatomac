@extends('layouts.admin')

@section('content')

<div class="p-6 mx-auto space-y-10 animate-fadeIn">

    <div class="flex justify-between items-center pb-4">
        <h2 class="text-2xl font-bold text-slate-900">Editar Usuario</h2>

        <a href="{{ route('usuarios.index') }}"
           class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition cursor-pointer">
            ← Volver
        </a>
    </div>

    <form action="{{ route('usuarios.update', $user->id) }}" method="POST"
          class="bg-white border border-slate-200 rounded-md shadow-sm p-6 space-y-8">
        
        @csrf
        @method('PUT')

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-800">Nombre *</label>
            <input name="name" type="text"
                   value="{{ old('name', $user->name) }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600">
            @error('name')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-800">Email *</label>
            <input name="email" type="email"
                   value="{{ old('email', $user->email) }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600">
            @error('email')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-800">Rol *</label>
            <select name="role"
                    class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600">
                <option value="admin" @selected($user->role === 'admin')>Admin</option>
                <option value="user" @selected($user->role === 'user')>Usuario</option>
            </select>
            @error('role')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-800">Contraseña (opcional)</label>
        
            <div class="relative">
                <input type="password" name="password" id="passwordInput"
                       placeholder="Dejar vacío para mantener"
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white
                              focus:ring-2 focus:ring-blue-600 focus:outline-none pr-10">
        
                <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-2 flex items-center text-slate-500 cursor-pointer">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.522 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        
            @error('password')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        

        <div class="pt-4 border-t border-slate-200 flex justify-end space-x-3">
            <a href="{{ route('usuarios.index') }}"
               class="px-6 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition cursor-pointer">
                Cancelar
            </a>

            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                Actualizar
            </button>
        </div>

    </form>

</div>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
    
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.426-4.568M9.88 9.88a3 3 0 104.24 4.24M6.1 6.1l11.8 11.8"/>
            `;
        } else {
            input.type = 'password';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.522 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
            `;
        }
    });
    </script>
    
@endsection
