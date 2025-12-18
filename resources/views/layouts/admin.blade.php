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
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

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
        .animate-fadeIn {
            animation: fadeIn 0.4s ease-out;
        }
        .nav-item {
            transition: all 0.2s ease;
        }
        .nav-item:hover {
            transform: translateX(4px);
        }
    </style>
</head>

<body class="bg-gray-50" x-data="{ openSidebar: true }">
    
    <aside 
        x-show="openSidebar"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="w-72 bg-white border-r border-gray-200 shadow-sm flex flex-col fixed inset-y-0 left-0 z-40 overflow-y-auto"
    >
        <div class="flex items-center justify-center py-8 px-6 border-b border-gray-100">
            @if($contact && $contact->icono_2)
                <a href="{{ url('/') }}" wire:navigate class="flex items-center justify-center">
                    <img src="{{ Storage::url($contact->icono_2) }}" 
                         class="h-16 w-auto object-contain"
                         alt="Logo">
                </a>
            @else
                <div class="h-16 w-32 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="text-gray-400 text-sm font-medium">Sin logo</span>
                </div>
            @endif
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