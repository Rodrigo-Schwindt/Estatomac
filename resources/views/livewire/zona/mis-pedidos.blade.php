
<div class="max-w-[1224px] mx-auto pb-[88px]" x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class="bg-white pt-[24px] pb-[61px]">
        <nav class="max-w-[1224px] mx-auto text-black font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[12px] font-medium leading-normal">Inicio</a>
            <span class="text-black font-inter text-[12px] font-light leading-normal">›</span>
            <span class="text-black font-inter text-[12px] font-light leading-normal">Mis Pedidos</span>
        </nav>
    </div>
    <h1 class="text-black font-inter text-[32px] font-semibold leading-normal mb-[32px]">Mis Pedidos</h1>

    @if($pedidos->isEmpty())
        <div class="bg-white rounded-[6px] p-12 text-center">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 font-inter text-[18px]">No tienes pedidos realizados</p>
            <a wire:navigate href="{{ route('cliente.productos') }}" class="inline-block mt-6 bg-[#E40044] text-white px-6 py-3 rounded-[4px] font-inter text-[14px] font-medium uppercase">
                Ir a Productos
            </a>
        </div>
    @else
        <table class="w-full border-collapse rounded-t-[6px]">
            <thead class="rounded-t-[6px]">
                <tr class="bg-black h-[52px] text-white font-inter text-[16px] font-light leading-normal">
                    <th class="w-[80px]"></th>
                    <th class="text-left pl-[22px]">Nº de pedido</th>
                    <th class="text-left pl-[18px]">Fecha de compra</th>
                   
                    <th class="text-left pl-[100px]">Fecha de entrega</th>
                    <th class="text-left pl-[10px]">Importe</th>
                    <th class="text-center pl-[8px]">Entregado</th>
                    <th class="text-right pr-[22px]"></th>
                </tr>
            </thead>

            <tbody class="bg-white">
                <tr>
                    <td colspan="8" class="h-[18px]"></td>
                </tr>

                @foreach($pedidos as $pedido)
                    <tr>
                        <!-- Icono -->
                        <td class="w-[80px] h-[73px]">
                            <div class="flex items-center justify-center">
                                <div class="w-[80px] h-[80px] flex items-center justify-center rounded bg-[#F8F8F8]">
                                    <svg width="43" height="43" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M28.667 7.16671H32.2503C33.2007 7.16671 34.1121 7.54424 34.7841 8.21624C35.4561 8.88825 35.8337 9.79968 35.8337 10.75V35.8334C35.8337 36.7837 35.4561 37.6952 34.7841 38.3672C34.1121 39.0392 33.2007 39.4167 32.2503 39.4167H10.7503C9.79997 39.4167 8.88853 39.0392 8.21653 38.3672C7.54452 37.6952 7.16699 36.7837 7.16699 35.8334V10.75C7.16699 9.79968 7.54452 8.88825 8.21653 8.21624C8.88853 7.54424 9.79997 7.16671 10.7503 7.16671H14.3337M21.5003 19.7084H28.667M21.5003 28.6667H28.667M14.3337 19.7084H14.3516M14.3337 28.6667H14.3516M16.1253 3.58337H26.8753C27.8648 3.58337 28.667 4.38553 28.667 5.37504V8.95837C28.667 9.94788 27.8648 10.75 26.8753 10.75H16.1253C15.1358 10.75 14.3337 9.94788 14.3337 8.95837V5.37504C14.3337 4.38553 15.1358 3.58337 16.1253 3.58337Z" stroke="#E40044" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        
                                </div>
                            </div>
                        </td>

                        <!-- Nº de pedido -->
                        <td class="pl-[22px] text-black font-inter text-[16px] font-normal leading-[25px]">
                            {{ $pedido->numero_pedido }}
                        </td>

                        <!-- Fecha de compra -->
                        <td class="pl-[18px] text-black font-inter text-[16px] font-normal leading-normal">
                            {{ $pedido->fecha_compra->format('d/m/Y') }}
                        </td>



                        <!-- Fecha de entrega -->
                        <td class="pl-[100px] text-black font-inter text-[16px] font-normal leading-normal">
                            {{ $pedido->fecha_entrega->format('d/m/Y') }}
                        </td>

                        <!-- Importe -->
                        <td class="pl-[10px] text-black font-inter text-[16px] font-normal leading-normal">
                            ${{ number_format($pedido->total, 2, ',', '.') }}
                        </td>

                        <!-- Entregado (checkbox solo visual) -->
                        <td class="text-center pl-[8px]">
                            <div class="flex items-center justify-center">
                                @if($pedido->entregado)
                                    <div class="relative w-[38px] h-[38px] flex items-center justify-center">
                                        <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M37.5 0.5V37.5H0.5V0.5H37.5Z" fill="white" stroke="#D9D9D9"/>
                                            <path d="M36.9436 1.05554H1.05469V36.9444H36.9436V1.05554Z" stroke="#D9D9D9"/>
                                        </svg>
                                        <svg class="absolute" xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                            <path d="M8.83075 22.3135L0.380745 13.8635C-0.126915 13.3558 -0.126915 12.5327 0.380745 12.025L2.21918 10.1865C2.72684 9.67882 3.55 9.67882 4.05766 10.1865L9.74999 15.8788L21.9423 3.68653C22.45 3.17887 23.2731 3.17887 23.7808 3.68653L25.6192 5.52502C26.1269 6.03268 26.1269 6.85579 25.6192 7.3635L10.6692 22.3136C10.1615 22.8212 9.33841 22.8212 8.83075 22.3135Z" fill="#E40044"/>
                                        </svg>
                                    </div>
                                @else
                                    <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M37.5 0.5V37.5H0.5V0.5H37.5Z" fill="white" stroke="#D9D9D9"/>
                                        <path d="M36.9436 1.05554H1.05469V36.9444H36.9436V1.05554Z" stroke="#D9D9D9"/>
                                    </svg>
                                @endif
                            </div>
                        </td>

                        <td>
                            <div class="flex justify-end gap-[20px] pr-[22px]">
                                <a
                                    href="{{ route('cliente.pedidos.detalle', $pedido->id) }}"
                                    wire:navigate
                                    class="rounded-[4px] border border-[#E40044] w-[127px] h-[44px] bg-white text-[#E40044] text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#E40044] hover:text-white transition-colors"
                                >
                                    VER ONLINE
                                </a>
                        
                                <a
                                    href="{{ route('cliente.pedidos.descargar', $pedido->id) }}"
                                    class="w-[127px] h-[44px] rounded-[4px] bg-[#E40044] text-white text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#b8003a] transition-colors"
                                >
                                    DESCARGAR
                                </a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="8" class="py-[10px]">
                            <div class="w-full h-[1px] bg-[#E5E5E5]"></div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    @push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-download', (event) => {
            window.open(event.url, '_blank');
        });
    });
</script>
@endpush
</div>