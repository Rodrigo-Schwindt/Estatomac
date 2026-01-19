@extends('layouts.admin')

@section('content')

<div class="space-y-10 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">

    <div class="flex justify-between items-center pb-4">
        <h2 class="text-2xl font-bold text-slate-900">Usuarios</h2>

        <a href="{{ route('usuarios.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer">
            + Nuevo Usuario
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-md px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 text-xs uppercase">
                <tr>
                    <th class="p-4 text-start">Nombre</th>
                    <th class="p-4 text-start">Email</th>
                    <th class="p-4 text-start">Rol</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @foreach($users as $u)
                <tr>
                    <td class="p-4">{{ $u->name }}</td>
                    <td class="p-4">{{ $u->email }}</td>
                    <td class="p-4">{{ ucfirst($u->role) }}</td>

                    <td class="p-4 text-center flex justify-center gap-3">
                        <a href="{{ route('usuarios.edit', $u->id) }}"
                           class="text-blue-600 hover:underline cursor-pointer">
                           <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>

                        @if(auth()->id() !== $u->id)
                        <form action="{{ route('usuarios.destroy', $u->id) }}"
                              method="POST"
                              onsubmit="return confirm('¿Eliminar usuario?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline cursor-pointer">
                                <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

@endsection
