<div
x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-700"
x-transition:enter-start="opacity-0 transform translate-y-6 scale-[0.99]"
x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
class="w-full overflow-hidden"
>
@if($sliders->count() > 0)
<div
x-data="{
currentSlideIndex: 1,
autoSlide: null,
isDragging: false,
mouseStartX: 0,
mouseEndX: 0,
init() { this.startAutoSlide() },
startAutoSlide() {
this.stopAutoSlide()
this.autoSlide = setInterval(() => {
this.currentSlideIndex = this.currentSlideIndex < {{ $sliders->count() }} ? this.currentSlideIndex + 1 : 1
}, 5000)
},
stopAutoSlide() { if (this.autoSlide) clearInterval(this.autoSlide) },
previous() {
this.stopAutoSlide()
this.currentSlideIndex = this.currentSlideIndex > 1 ? this.currentSlideIndex - 1 : {{ $sliders->count() }}
this.startAutoSlide()
},
next() {
this.stopAutoSlide()
this.currentSlideIndex = this.currentSlideIndex < {{ $sliders->count() }} ? this.currentSlideIndex + 1 : 1
this.startAutoSlide()
},
handleMouseDown(e) { this.mouseStartX = e.clientX; this.isDragging = true },
handleMouseMove(e) { if (this.isDragging) this.mouseEndX = e.clientX },
handleMouseUp() {
if (!this.isDragging) return
this.isDragging = false
const diff = this.mouseStartX - this.mouseEndX
if (Math.abs(diff) > 50) diff > 0 ? this.next() : this.previous()
},
handleTouchStart(e) { this.mouseStartX = e.touches[0].clientX; this.mouseEndX = this.mouseStartX; this.isDragging = true },
handleTouchMove(e) { if (this.isDragging) this.mouseEndX = e.touches[0].clientX },
handleTouchEnd() {
if (!this.isDragging) return
this.isDragging = false
const diff = this.mouseStartX - this.mouseEndX
if (Math.abs(diff) > 50) diff > 0 ? this.next() : this.previous()
}
}"
x-on:mousedown="handleMouseDown"
x-on:mousemove="handleMouseMove"
x-on:mouseup="handleMouseUp"
x-on:mouseleave="isDragging = false"
x-on:touchstart="handleTouchStart"
x-on:touchmove="handleTouchMove"
x-on:touchend="handleTouchEnd"
class="relative w-full overflow-hidden select-none group"
>
<div class="relative h-[668px] max-[1199px]:h-[560px] max-lg:h-[520px] max-md:h-[500px] max-sm:h-[520px] w-full">

        @foreach($sliders as $index => $slider)
        <template x-if="currentSlideIndex == {{ $index + 1 }}">
            <div
                x-cloak
                x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 scale-105"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute inset-0"
            >
                <div class="
                    absolute inset-0 z-10 flex flex-col 
                    pt-[204px]
                    mb-[25px] 
                    max-w-[1224px] mx-auto 
                    px-4 xl:px-0
                    max-[1199px]:pt-[150px]
                    max-[1199px]:px-6
                    max-lg:pt-[140px] 
                    max-md:pt-[96px] 
                    max-sm:pt-[112px]
                    max-[420px]:pt-[96px]
                    max-md:text-center
                    max-md:items-center
                ">
                    <h1 
                        class="
                            w-full max-w-[720px] text-white font-inter text-[68px] font-semibold leading-tight
                            max-[1199px]:text-[52px] max-lg:text-[42px] max-md:text-[34px] max-sm:text-[30px] max-[420px]:text-[27px]
                            max-md:leading-[1.15]
                            drop-shadow-md
                        "
                        x-transition:enter="transition ease-out duration-700 delay-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                    >
                        {{ $slider->title }}
                    </h1>
                    <p
                        x-data="{
                            expandIfLong() {
                                const lineHeight = parseFloat(getComputedStyle(this.$refs.desc).lineHeight);
                                const lines = Math.round(this.$refs.desc.scrollHeight / lineHeight);
                                if (lines > 3) {
                                    this.$refs.desc.style.maxWidth = '820px';
                                }
                            }
                        }"
                        x-init="expandIfLong()"
                        x-ref="desc"
                        class="
                            w-full max-w-[620px] text-white font-inter text-[18px] font-light leading-normal
                             max-[1199px]:mt-4 max-md:text-[16px] max-sm:text-[15px] max-sm:leading-[22px] max-sm:line-clamp-3
                        "
                        x-transition:enter="transition ease-out duration-700 delay-500"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                    >
                        {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($slider->description)) }}
                    </p>
                    @if($slider->url || $slider->button_text)
                    <a
                    href="{{ $slider->url ?: '#' }}"
                    class="
                    absolute
                    bottom-[140px] max-[1199px]:bottom-[92px] max-lg:bottom-[76px] max-md:bottom-[74px] max-sm:bottom-[88px] max-[420px]:bottom-[78px]
                    flex px-[5px] py-[10px] justify-center items-center
                    w-[160px] h-[40px] max-sm:w-[148px] max-sm:h-[38px]
                    text-white text-center font-inter text-[16px] font-normal leading-normal 
                    max-sm:text-[15px]
                    bg-[#23378C]
                    rounded-[40px] border border-[#23378C]
                    transition-all duration-300
                    max-md:left-1/2 max-md:-translate-x-1/2
                    hover:border-[#1A2A6E] hover:text-white hover:bg-[#1A2A6E] hover:shadow-[0_8px_20px_rgba(35,55,140,0.25)]
                    active:scale-95
                    "
                    >
                        {{ $slider->button_text ?: 'Ver más' }}
                    </a>
                    @endif

                    @if($sliders->count() > 1)
                    <div class="
                        absolute 
                        bottom-[64px] 
                        z-20 flex gap-3 
                        max-[1199px]:bottom-[38px]
                        max-xl:bottom-[30px]
                        max-md:w-full
                        max-md:bottom-[26px]
                        max-md:justify-center
                        pl-2
                    ">
                        @foreach($sliders as $i => $s)
                        <button
                            class="transition-all duration-300  w-[40px] h-[4px] max-md:w-[28px] max-md:h-[5px] cursor-pointer  hover:bg-white/80"
                            x-on:click="currentSlideIndex = {{ $i + 1 }}"
                            :class="currentSlideIndex === {{ $i + 1 }} ? 'bg-white drop-shadow-lg scale-110' : 'bg-white/50'"
                        ></button>
                        @endforeach
                    </div>
                    @endif
                    
                </div>

                @php
                    $ext = strtolower(pathinfo($slider->image, PATHINFO_EXTENSION));
                    $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'avi']);
                @endphp

                <div class="absolute inset-0">
                    @if($isVideo)
                        <video class="absolute w-full h-full inset-0 object-cover" autoplay loop muted playsinline>
                            <source src="{{ Storage::url($slider->image) }}" type="video/{{ $ext }}">
                        </video>
                    @else
                        <img 
                            class="absolute w-full h-full inset-0 object-cover transition-transform duration-[2000ms] ease-in-out transform"
                            :class="currentSlideIndex == {{ $index + 1 }} ? 'scale-100' : 'scale-110'"
                            src="{{ Storage::url($slider->image) }}"
                            alt="{{ $slider->title }}"
                        />
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/30 max-md:from-black/85 max-md:via-black/55 max-md:to-black/35"></div>

                </div>
            </div>
        </template>
        @endforeach

    </div>
</div>
@endif




<livewire:vistas.productos.productos-destacados/>
<livewire:vistas.nosotros.nosotros-home />
<livewire:vistas.novedades.destacadas/>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoriaSelect = document.getElementById('categoriaSelect');
        const arrowCategoria = document.getElementById('arrowCategoria');
        
        const marcaSelect = document.getElementById('marcaSelect');
        const arrowMarca = document.getElementById('arrowMarca');
        const codigoSelect = document.getElementById('codigoSelect');
        const arrowCodigo = document.getElementById('arrowCodigo');

        const equivalenciaSelect = document.getElementById('equivalenciaSelect');
        const arrowEquivalencia = document.getElementById('arrowEquivalencia');

        if (codigoSelect && arrowCodigo) {
            codigoSelect.addEventListener('click', function() {
                const isRotated = arrowCodigo.style.transform.includes('rotate(180deg)');
                arrowCodigo.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        }

        if (equivalenciaSelect && arrowEquivalencia) {
            equivalenciaSelect.addEventListener('click', function() {
                const isRotated = arrowEquivalencia.style.transform.includes('rotate(180deg)');
                arrowEquivalencia.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        }
        
        if (categoriaSelect && arrowCategoria) {
            categoriaSelect.addEventListener('click', function() {
                const isRotated = arrowCategoria.style.transform.includes('rotate(180deg)');
                arrowCategoria.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        }
        
        if (marcaSelect && arrowMarca) {
            marcaSelect.addEventListener('click', function() {
                const isRotated = arrowMarca.style.transform.includes('rotate(180deg)');
                arrowMarca.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        }
        
        document.addEventListener('livewire:navigated', function() {
            const modeloSelect = document.getElementById('modeloSelect');
            const arrowModelo = document.getElementById('arrowModelo');
            
            if (modeloSelect && arrowModelo) {
                modeloSelect.addEventListener('click', function() {
                    const isRotated = arrowModelo.style.transform.includes('rotate(180deg)');
                    arrowModelo.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
                });
            }
        });
    });
</script>

</div>
