@php
    use App\Models\Contact;
    $contact = Contact::first();
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <title>{{ $title ?? 'Panel Administrativo' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
        
        /* Custom Scrollbar */
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #f87171;
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #ef4444;
        }

        .nav-item {
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .active-link {
            background: linear-gradient(to right, #ef4444, #f97316);
            color: white !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }
        .submenu-item {
            padding-left: 48px;
            font-size: 0.85rem;
            color: #62748b;
        }
        .submenu-item:hover { color: #ef4444; }
        .divider { border-top: 1px solid #f1f5f9; margin: 12px 0; }
    </style>
</head>

<body class="bg-gray-50" x-data="{ openSidebar: true }">
    
    <aside 
        x-show="openSidebar"
        class="w-72 bg-white border-r border-gray-200 shadow-xl flex flex-col fixed inset-y-0 left-0 z-40"
    >
        <div class="flex items-center justify-center py-8 px-6 border-b border-gray-50">
            @if($contact && $contact->icono_2)
                <a href="{{ url('/') }}" class="flex items-center justify-center">
                    <img src="{{ Storage::url($contact->icono_1) }}" class="h-12 w-auto object-contain" alt="Logo">
                </a>
            @else
                <div class="h-12 w-32 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg flex items-center justify-center text-white font-bold">
                    ADMIN
                </div>
            @endif
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto custom-scroll">
            
            <div x-data="{ open: {{ request()->routeIs('sliders.*', 'nosotros.home.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="nav-item w-full text-gray-700 hover:bg-gray-50 justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Inicio</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('sliders.index') }}" class="nav-item submenu-item {{ request()->routeIs('sliders.*') ? 'text-red-500 font-bold' : '' }}">Sliders</a>
                    <a href="{{ route('nosotros.home.index') }}" class="nav-item submenu-item {{ request()->routeIs('nosotros.home.*') ? 'text-red-500 font-bold' : '' }}">Nosotros Home</a>
                </div>
            </div>

            <a href="{{ route('nosotros.index') }}" class="nav-item {{ request()->routeIs('nosotros.index') ? 'active-link' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('nosotros.index') ? '' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Nosotros
            </a>

            <div x-data="{ open: {{ request()->routeIs('productos.*', 'categorias.*', 'marcas.*', 'equivalencias.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="nav-item w-full text-gray-700 hover:bg-gray-50 justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Productos</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('categorias.index') }}" class="nav-item submenu-item">Categorías</a>
                    <a href="{{ route('marcas.index') }}" class="nav-item submenu-item">Marcas</a>
                    <a href="{{ route('productos.index') }}" class="nav-item submenu-item">Productos</a>
                    <a href="{{ route('equivalencias.index') }}" class="nav-item submenu-item">Equivalencias</a>
                </div>
            </div>

            <div x-data="{ open: {{ request()->routeIs('novedades.*', 'novcategories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="nav-item w-full text-gray-700 hover:bg-gray-50 justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <span>Novedades</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('novcategories.index') }}" class="nav-item submenu-item">Cat. Novedades</a>
                    <a href="{{ route('novedades.index') }}" class="nav-item submenu-item">Ver Novedades</a>
                </div>
            </div>

            <div class="divider"></div>

            <a href="{{ route('clientes.index') }}" class="nav-item {{ request()->routeIs('clientes.*') ? 'active-link' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('clientes.*') ? '' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Clientes
            </a>

            <a href="{{ route('carrito.config.index') }}" class="nav-item {{ request()->routeIs('carrito.config.*') ? 'active-link' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('carrito.config.*') ? '' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Config. Pagos
            </a>

            <a href="{{ route('precios.index') }}" class="nav-item {{ request()->routeIs('precios.*') ? 'active-link' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('precios.*') ? '' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Lista de precios
            </a>

            <a href="{{ route('admin.pedidos.index') }}" class="nav-item {{ request()->routeIs('admin.pedidos.*') ? 'active-link' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.pedidos.*') ? '' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Pedidos
            </a>

            <div class="divider"></div>

            <a href="{{ route('admin.contacto') }}" class="nav-item {{ request()->routeIs('admin.contacto') ? 'active-link' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.contacto') ? '' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Contacto
            </a>

            <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active-link' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('usuarios.*') ? '' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Usuarios
            </a>
            <a href="{{ route('admin.metadata') }}" class="nav-item {{ request()->routeIs('admin.metadata') ? 'active-link' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.metadata') ? '' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Metadata
            </a>
            
            <a href="{{ route('admin.newsletter') }}" class="nav-item {{ request()->routeIs('admin.newsletter*') ? 'active-link' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.newsletter*') ? '' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
                Newsletter
            </a>
        </nav>

        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 transition-all shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="ml-72 min-h-screen bg-gray-50">
        <div class="p-8 animate-fadeIn">
            @yield('content')
            {{ $slot ?? '' }}
        </div>
    </main>

</body>
</html>