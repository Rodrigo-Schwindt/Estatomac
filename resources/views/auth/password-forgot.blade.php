<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - Todotex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Recuperar contraseña</h1>
            <p class="text-sm text-gray-500 mt-1">Te enviaremos un enlace por email</p>
        </div>

        <div class="">
            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('cliente.password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Soy</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipo" value="cliente" {{ old('tipo', 'cliente') === 'cliente' ? 'checked' : '' }}>
                            <span class="text-sm">Cliente</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipo" value="vendedor" {{ old('tipo') === 'vendedor' ? 'checked' : '' }}>
                            <span class="text-sm">Vendedor</span>
                        </label>
                    </div>
                    @error('tipo')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#23378C] @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-500">{!! $message !!}</p>@enderror
                </div>

                <button type="submit" class="w-full py-3 bg-[#23378C] hover:bg-[#1b2d72] text-white font-semibold rounded-xl text-sm transition-colors">
                    Enviar enlace
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-6">
                ¿Necesitás ayuda? Contactá a <x-soporte-ventas/>
            </p>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            <a href="{{ url('/') }}" class="text-[#23378C] hover:underline">← Volver al inicio</a>
        </p>
    </div>
</body>
</html>
