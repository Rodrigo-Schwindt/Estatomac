<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso Vendedores — Todotex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md px-4">

    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Acceso Vendedores</h1>
        <p class="text-sm text-gray-500 mt-1">Zona privada Todotex</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-8">
        <form method="POST" action="{{ route('cliente.vendedor.login.post') }}">
            @csrf

            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    autofocus
                    class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#018637] @error('email') border-red-400 @enderror"
                    placeholder="tu@email.com"
                >
                @error('email')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#018637]"
                    placeholder="••••••••"
                >
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#018637] focus:ring-[#018637]">
                    <span class="text-sm text-gray-600">Recordarme</span>
                </label>
            </div>

            <button type="submit"
                class="w-full py-3 bg-[#018637] hover:bg-[#016d2e] text-white font-semibold rounded-xl text-sm transition-colors">
                Ingresar
            </button>

            <div class="mt-4 text-center">
                <a href="{{ route('cliente.password.request') }}" class="text-sm text-[#018637] hover:underline">
                    Olvidé mi password
                </a>
            </div>
        </form>
    </div>

    <p class="text-center text-xs text-gray-400 mt-4">
        ¿Necesitás ayuda? Contactá a <x-soporte-ventas/>
    </p>

    <p class="text-center text-xs text-gray-400 mt-2">
        ¿Sos cliente?
        <a href="{{ route('home') }}" class="text-[#018637] hover:underline">Acceder como cliente</a>
    </p>
</div>

</body>
</html>
