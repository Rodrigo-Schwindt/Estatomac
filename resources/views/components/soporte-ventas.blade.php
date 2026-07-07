@php
    $email = \App\Models\Parametro::config()->email_soporte;
    $label = $slot->isEmpty() ? 'Soporte de Ventas' : $slot;
@endphp

@if($email)
    <span class="relative inline-block group">
        <a href="mailto:{{ $email }}" class="text-[#23378C] underline hover:text-[#1b2d72]">{{ $label }}</a>
        <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-1 whitespace-nowrap rounded-md bg-gray-900 text-white text-xs px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity">
            {{ $email }}
        </span>
    </span>
@else
    <span>{{ $label }}</span>
@endif
