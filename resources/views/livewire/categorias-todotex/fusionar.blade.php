@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-6 animate-fadeIn">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Fusionar Categorías</h2>
            <p class="text-sm text-slate-500 mt-1">Combiná varias categorías de la misma familia en una nueva.</p>
        </div>
        <a href="{{ route('categorias-todotex.index') }}"
           class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition cursor-pointer">
            ← Volver a la lista
        </a>
    </div>

    <div id="alertContainer"></div>

    {{-- Info --}}
    <div class="bg-amber-50 border border-amber-200 rounded-md p-4 flex gap-3">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-amber-800 space-y-0.5">
            <p class="font-semibold">¿Qué hace esta operación?</p>
            <ul class="list-disc list-inside text-amber-700 space-y-0.5">
                <li>Crea una nueva categoría con el nombre que elijas.</li>
                <li>Reasigna todos los productos de las categorías seleccionadas a la nueva.</li>
                <li>Oculta las categorías originales (quedan como respaldo en la base de datos).</li>
            </ul>
        </div>
    </div>

    <div x-data="{
        familiaId: '',
        selectedIds: [],
        categoriasPorFamilia: @js($categoriasPorFamilia),
        get categoriasActuales() {
            return this.familiaId ? (this.categoriasPorFamilia[this.familiaId] ?? []) : [];
        },
        get seleccionadas() {
            return this.categoriasActuales.filter(c => this.selectedIds.includes(c.id));
        },
        get totalProductosAprox() {
            return this.seleccionadas.reduce((s, c) => s + (c.productos_count || 0), 0);
        },
        onFamiliaChange() {
            this.selectedIds = [];
        },
        selectAll() {
            this.selectedIds = this.categoriasActuales.map(c => c.id);
        },
        clearAll() {
            this.selectedIds = [];
        },
        modal: { open: false, loading: false, titulo: '', productos: [] },
        async verProductos(catId, catTitulo) {
            this.modal.open = true;
            this.modal.loading = true;
            this.modal.titulo = catTitulo;
            this.modal.productos = [];
            try {
                const r = await fetch(`/admin/categorias-todotex/${catId}/productos`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await r.json();
                this.modal.productos = data.productos || [];
            } catch (e) {}
            this.modal.loading = false;
        }
    }">
        <form id="fusionarForm" class="space-y-6">
            @csrf

            {{-- Paso 1: Familia --}}
            <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm space-y-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 bg-violet-600 text-white text-xs rounded-full flex items-center justify-center font-bold shrink-0">1</span>
                    Seleccioná la familia
                </h3>

                <div>
                    <label for="familia_id" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Familia <span class="text-red-500">*</span>
                    </label>
                    <select id="familia_id" name="familia_id"
                            x-model="familiaId"
                            @change="onFamiliaChange()"
                            class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-violet-600 focus:outline-none max-w-md">
                        <option value="">Seleccionar familia...</option>
                        @foreach($familias as $familia)
                            <option value="{{ $familia->id }}">{{ $familia->titulo }}</option>
                        @endforeach
                    </select>
                    <p id="familia_id-error" class="mt-1 text-red-600 text-sm hidden"></p>
                </div>
            </div>

            {{-- Paso 2: Categorías --}}
            <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm space-y-4"
                 x-show="familiaId" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0">

                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <span class="w-6 h-6 bg-violet-600 text-white text-xs rounded-full flex items-center justify-center font-bold shrink-0">2</span>
                        Elegí las categorías a fusionar
                        <span class="text-sm font-normal text-slate-400">(mínimo 2)</span>
                    </h3>
                    <div class="flex gap-2">
                        <button type="button" @click="selectAll()"
                                class="text-xs text-violet-600 hover:text-violet-800 px-2.5 py-1 border border-violet-200 rounded-md hover:bg-violet-50 transition">
                            Seleccionar todas
                        </button>
                        <button type="button" @click="clearAll()"
                                class="text-xs text-slate-500 hover:text-slate-700 px-2.5 py-1 border border-slate-200 rounded-md hover:bg-slate-50 transition">
                            Limpiar
                        </button>
                    </div>
                </div>

                {{-- Sin categorías --}}
                <div x-show="categoriasActuales.length === 0"
                     class="py-10 text-center text-slate-400 text-sm">
                    Esta familia no tiene categorías visibles.
                </div>

                {{-- Lista de categorías --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2"
                     x-show="categoriasActuales.length > 0">
                    <template x-for="cat in categoriasActuales" :key="cat.id">
                        <label class="flex items-start gap-3 p-3 rounded-md border cursor-pointer transition-all select-none"
                               :class="selectedIds.includes(cat.id)
                                   ? 'border-violet-400 bg-violet-50 shadow-sm'
                                   : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'">
                            <input type="checkbox"
                                   :value="cat.id"
                                   x-model="selectedIds"
                                   name="categoria_ids[]"
                                   class="mt-0.5 w-4 h-4 text-violet-600 border-slate-300 rounded focus:ring-2 focus:ring-violet-500">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 leading-snug" x-text="cat.titulo"></p>
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-xs text-slate-400" x-text="cat.productos_count + ' productos'"></span>
                                        <span x-show="cat.rubro_titulo"
                                              class="text-xs bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full leading-none"
                                              x-text="cat.rubro_titulo"></span>
                                    </div>
                                    <button type="button" @click.stop="verProductos(cat.id, cat.titulo)"
                                            class="text-xs text-violet-600 hover:text-violet-800 underline shrink-0">
                                        Ver
                                    </button>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>

                {{-- Resumen selección --}}
                <div x-show="selectedIds.length > 0"
                     class="bg-violet-50 border border-violet-200 rounded-md p-3 text-sm text-violet-800">
                    <span x-text="selectedIds.length + ' categorías seleccionadas — aprox. ' + totalProductosAprox + ' productos a reasignar.'"></span>
                </div>

                <p id="categoria_ids-error" class="text-red-600 text-sm hidden"></p>
            </div>

            {{-- Paso 3: Nueva categoría --}}
            <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm space-y-5"
                 x-show="selectedIds.length >= 2" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0">

                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 bg-violet-600 text-white text-xs rounded-full flex items-center justify-center font-bold shrink-0">3</span>
                    Configurá la nueva categoría resultante
                </h3>

                <div>
                    <label for="titulo" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nombre de la nueva categoría <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="titulo" name="titulo"
                           placeholder="Ej: hilos yutes, hilos de algodón, hilos de seda..."
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-violet-600 focus:outline-none">
                    <p id="titulo-error" class="mt-1 text-red-600 text-sm hidden"></p>
                </div>

                <div>
                    <label for="orden" class="block text-sm font-medium text-slate-700 mb-1.5">Orden</label>
                    <input type="text" id="orden" name="orden"
                           placeholder="Ej: AA, AB, AAA..."
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white font-mono tracking-wider uppercase focus:ring-2 focus:ring-violet-600 focus:outline-none max-w-xs">
                    <p class="text-xs text-slate-400 mt-1">Usa letras: AA, AB, AC...</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Rubros <span class="text-slate-400 font-normal">(puede seleccionar varios)</span></label>
                    @if($rubros->isEmpty())
                        <p class="text-sm text-slate-400">No hay rubros disponibles.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($rubros as $rubro)
                                <label class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-md cursor-pointer hover:border-violet-400 hover:bg-violet-50 transition-all select-none has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50">
                                    <input type="checkbox"
                                           name="rubro_ids[]"
                                           value="{{ $rubro->id }}"
                                           class="w-4 h-4 text-violet-600 border-slate-300 rounded focus:ring-2 focus:ring-violet-500">
                                    <span class="text-sm text-slate-700">{{ $rubro->titulo }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resumen final de lo que va a pasar --}}
            <div x-show="selectedIds.length >= 2" x-cloak
                 class="bg-slate-50 border border-slate-200 rounded-md p-4 space-y-2">
                <p class="text-sm font-semibold text-slate-700">Resumen de la operación:</p>
                <ul class="space-y-1">
                    <template x-for="cat in seleccionadas" :key="cat.id">
                        <li class="text-sm text-slate-600 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            <span>
                                <strong x-text="cat.titulo"></strong>
                                <span class="text-slate-400" x-text="' — ' + cat.productos_count + ' productos · se oculta'"></span>
                            </span>
                        </li>
                    </template>
                </ul>
            </div>

            {{-- Botones --}}
            <div x-show="selectedIds.length >= 2" x-cloak
                 class="flex justify-end gap-3 pt-2">
                <a href="{{ route('categorias-todotex.index') }}"
                   class="px-5 py-2 border border-slate-300 rounded-md text-sm text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-violet-600 text-white rounded-md text-sm font-medium hover:bg-violet-700 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span id="submitText">Fusionar <span x-text="selectedIds.length"></span> categorías</span>
                    <span id="submitLoading" class="hidden items-center gap-2">
                        <div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        Procesando...
                    </span>
                </button>
            </div>

        </form>

        {{-- Fusiones previas (revertir) --}}
        @if($categoriasOcultas->isNotEmpty())
        @php $porFusion = $categoriasOcultas->groupBy('fusionada_en_id'); @endphp
        <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm space-y-6 mt-6">
            <div>
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 bg-amber-500 text-white text-xs rounded-full flex items-center justify-center font-bold shrink-0">↩</span>
                    Revertir fusiones
                </h3>
                <p class="text-sm text-slate-500 mt-1">Al revertir una categoría vuelve a ser visible. Los productos no se mueven automáticamente — quedan en la categoría fusionada.</p>
            </div>

            @foreach($porFusion as $fusionadaEnId => $cats)
            @php $catFusionada = $cats->first()->fusionadaEn; @endphp
            <div class="space-y-2">
                <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                    <svg class="w-4 h-4 text-violet-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        Fusionadas en:
                    </span>
                    <span class="text-sm font-semibold text-violet-700">
                        {{ $catFusionada?->titulo ?? '(categoría eliminada)' }}
                    </span>
                </div>

                @foreach($cats as $cat)
                <div class="flex items-center justify-between gap-3 p-3 border border-slate-200 rounded-md bg-slate-50 ml-4" id="cat-oculta-{{ $cat->id }}">
                    <div class="min-w-0">
                        <span class="text-sm font-medium text-slate-800">{{ $cat->titulo }}</span>
                        @if($cat->familia)
                            <span class="text-xs text-slate-400 ml-2">· {{ $cat->familia->titulo }}</span>
                        @endif
                        <span class="text-xs text-slate-400 ml-2">· {{ $cat->productos_count }} productos asignados actualmente</span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button"
                                @click="verProductos({{ $cat->id }}, '{{ addslashes($cat->titulo) }}')"
                                class="text-xs text-slate-600 hover:text-violet-700 border border-slate-200 px-2.5 py-1 rounded-md hover:bg-violet-50 transition">
                            Ver productos
                        </button>
                        <button type="button"
                                onclick="revertirCategoria({{ $cat->id }}, this)"
                                class="text-xs text-white bg-amber-500 hover:bg-amber-600 px-2.5 py-1 rounded-md transition">
                            Revertir
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        @endif

        {{-- Modal productos --}}
        <div x-show="modal.open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             @keydown.escape.window="modal.open = false"
             @click.self="modal.open = false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                    <h4 class="font-semibold text-slate-800 text-sm" x-text="modal.titulo"></h4>
                    <button type="button" @click="modal.open = false"
                            class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
                </div>
                <div class="px-5 py-4 max-h-80 overflow-y-auto">
                    <div x-show="modal.loading" class="text-center py-8 text-slate-400 text-sm">Cargando productos...</div>
                    <div x-show="!modal.loading">
                        <p x-show="modal.productos.length === 0"
                           class="text-slate-400 text-sm text-center py-4">
                            Esta categoría no tiene productos asignados.
                        </p>
                        <ul x-show="modal.productos.length > 0" class="space-y-1.5">
                            <template x-for="p in modal.productos" :key="p.id">
                                <li class="flex gap-3 text-sm">
                                    <span class="font-mono text-slate-400 shrink-0" x-text="p.codigo"></span>
                                    <span class="text-slate-700" x-text="p.titulo"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400" x-text="modal.productos.length + ' productos'"></span>
                    <button type="button" @click="modal.open = false"
                            class="text-xs text-slate-500 hover:text-slate-700 border border-slate-200 px-3 py-1 rounded-md hover:bg-slate-50 transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
[x-cloak] { display: none !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form       = document.getElementById('fusionarForm');
    const submitBtn  = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoad = document.getElementById('submitLoading');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoad.classList.remove('hidden');
        submitLoad.classList.add('flex');

        fetch('{{ route("categorias-todotex.procesarFusion") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(form),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => { window.location.href = data.redirect; }, 1800);
            } else {
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, msg]) => {
                        const el = document.getElementById(`${field}-error`);
                        if (el) {
                            el.textContent = Array.isArray(msg) ? msg[0] : msg;
                            el.classList.remove('hidden');
                        }
                    });
                } else {
                    showAlert(data.message || 'Error al fusionar', 'error');
                }
            }
        })
        .catch(() => showAlert('Error de conexión al fusionar las categorías', 'error'))
        .finally(() => {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoad.classList.add('hidden');
            submitLoad.classList.remove('flex');
        });
    });

    function clearErrors() {
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }

    function showAlert(message, type) {
        const container = document.getElementById('alertContainer');
        const cls = type === 'success'
            ? 'bg-green-50 border-green-200 text-green-700'
            : 'bg-red-50 border-red-200 text-red-700';
        container.innerHTML = `<div class="px-4 py-3 rounded-md ${cls} border text-sm">${message}</div>`;
        if (type !== 'success') {
            setTimeout(() => { container.innerHTML = ''; }, 5000);
        }
    }
});

function revertirCategoria(id, btn) {
    if (!confirm('¿Revertir esta categoría? Volverá a ser visible, pero los productos no se moverán automáticamente.')) return;

    btn.disabled = true;
    btn.textContent = '...';

    fetch(`/admin/categorias-todotex/${id}/revertir`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById(`cat-oculta-${id}`);
            if (row) {
                row.style.transition = 'opacity .3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
            const container = document.getElementById('alertContainer');
            container.innerHTML = `<div class="px-4 py-3 rounded-md bg-green-50 border-green-200 text-green-700 border text-sm">${data.message}</div>`;
            setTimeout(() => { container.innerHTML = ''; }, 4000);
        } else {
            btn.disabled = false;
            btn.textContent = 'Revertir';
            alert('Error al revertir la categoría.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Revertir';
        alert('Error de conexión.');
    });
}
</script>
@endsection
