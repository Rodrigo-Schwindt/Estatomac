<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona Privada - Iniciar Sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Zona Privada
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Ingrese sus credenciales para acceder
                </p>
            </div>
            
            <form class="mt-8 space-y-6" action="{{ route('cliente.login.post') }}" method="POST">
                @csrf
                
                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    </div>
                @endif

                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="usuario" class="block text-sm font-medium text-gray-700 mb-1">
                            Usuario
                        </label>
                        <input 
                            id="usuario" 
                            name="usuario" 
                            type="text" 
                            required 
                            value="{{ old('usuario') }}"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm"
                            placeholder="Usuario"
                        >
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Contraseña
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm"
                            placeholder="Contraseña"
                        >
                    </div>
                </div>

                <div class="flex items-center">
                    <input 
                        id="remember" 
                        name="remember" 
                        type="checkbox" 
                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Recordarme
                    </label>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-[#E4002B] hover:bg-[#c00024] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Iniciar Sesión
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        ← Volver al sitio
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>