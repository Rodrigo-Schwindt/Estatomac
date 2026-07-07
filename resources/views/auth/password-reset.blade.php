<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña - Todotex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Restablecer contraseña</h1>
            <p class="text-sm text-gray-500 mt-1">Definí una contraseña nueva</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-8">
            @if($errors->any())
                <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('cliente.password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="tipo" value="{{ $tipo }}">
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" value="{{ $email }}" disabled class="w-full px-4 py-2.5 border rounded-xl text-sm bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña</label>
                    <input id="password" type="password" name="password" required autofocus autocomplete="new-password"
                        class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#23378C]">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#23378C]">
                </div>

                <button type="submit" class="w-full py-3 bg-[#23378C] hover:bg-[#1b2d72] text-white font-semibold rounded-xl text-sm transition-colors">
                    Guardar contraseña
                </button>
            </form>
        </div>
    </div>
</body>
</html>
