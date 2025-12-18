<div 
    x-data="{ show: false }" 
    x-init="setTimeout(() => show = true, 50)" 
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
>
    @if($sliders->count() > 0)
    <div 
        x-data="{
            currentSlideIndex: 1,
            autoSlide: null,
            init() { this.startAutoSlide() },
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
            }
        }"
        x-on:mousedown="handleMouseDown"
        x-on:mousemove="handleMouseMove"
        x-on:mouseup="handleMouseUp"
        x-on:mouseleave="isDragging = false"
        class="relative w-full overflow-hidden select-none"
    >
        <div class="relative h-[672px] max-lg:h-[520px] max-md:h-[420px] max-sm:h-[520px] w-full">

            @foreach($sliders as $index => $slider)
            <template x-if="currentSlideIndex == {{ $index + 1 }}">
                <div
                    x-cloak
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                >
                    <div class="
                        absolute inset-0 z-10 flex flex-col 
                        pt-[230px]
                        mb-[25px] 
                        max-w-[1224px] mx-auto 
                        max-xl:px-6 
                        max-lg:pt-[200px] 
                        max-md:pt-[120px] 
                        max-sm:pt-[160px]
                        max-md:text-center
                        max-md:items-center
                    ">
                        <h1 class="
                            w-[720px] text-white font-inter text-[46px] font-semibold leading-tight
                        ">
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
                                w-[620px] text-white font-inter text-[18px] font-light leading-normal
                            "
                        >
                            {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($slider->description)) }}
                        </p>
                        <a 
                            href="{{ $slider->url }}"
                            class="
                                absolute 
                                bottom-[66px] 
                                flex w-[184px] px-[5px] py-[10px] justify-center items-center rounded-[4px] border border-white w-[164px] h-[44px] text-white text-center font-inter text-[14px] font-normal leading-normal uppercase bg-transparent
                            "
                        >
                            Ver productos
                        </a>

                        @if($sliders->count() > 1)
                        <div class="
                            absolute 
                            bottom-[24px] 
                            z-20 flex gap-3 
                        
                            max-xl:bottom-[30px]
                            max-md:static
                            max-md:mt-[20px]
                            max-md:justify-center
                        ">
                            @foreach($sliders as $i => $s)
                            <button
                                class="transition w-[44px] h-[6px] max-md:w-[28px] max-md:h-[5px] cursor-pointer"
                                x-on:click="currentSlideIndex = {{ $i + 1 }}"
                                :class="currentSlideIndex === {{ $i + 1 }} ? 'bg-white drop-shadow-lg' : 'bg-white/50'"
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
                                class="absolute w-full h-full inset-0 object-cover"
                                src="{{ Storage::url($slider->image) }}"
                                alt="{{ $slider->title }}"
                            />
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/40 to-black/40"></div>

                    </div>
                </div>
            </template>
            @endforeach

            </div>
            </div>
            @endif
            <livewire:vistas.nosotros.nosotrosHome />
            <livewire:vistas.novedades.destacadas/>
</div>
