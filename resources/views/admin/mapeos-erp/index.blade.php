@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Mapeos ERP → Categorización B2B</h1>
            <p class="text-gray-500 text-sm mt-1">
                Definí a qué <strong>familia y/o categoría</strong> del B2B corresponde cada
                <strong>familia y subfamilia</strong> del ERP. Cuando llegan productos nuevos por el FTP,
                el sistema los categoriza automáticamente según estas reglas.
            </p>
        </div>
        <form action="{{ route('admin.mapeos-erp.aplicar') }}" method="POST"
              onsubmit="return confirm('Aplicar los mapeos actuales a los productos sin categoría. ¿Continuar?')">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                Aplicar mapeos a productos sin categoría
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-1 gap-4 mb-6">
        {{-- <div class="bg-white rounded-lg shadow p-5">
            <p class="text-xs uppercase text-gray-500">Familias del ERP</p>
            <p class="text-2xl font-bold text-slate-900">
                {{ $statsFam['mapeadas'] }} / {{ $statsFam['total'] }}
                <span class="text-sm text-gray-500 font-normal">mapeadas</span>
            </p>
        </div> --}}
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-xs uppercase text-gray-500">Subfamilias del ERP</p>
            <p class="text-2xl font-bold text-slate-900">
                {{ $statsSub['mapeadas'] }} / {{ $statsSub['total'] }}
                <span class="text-sm text-gray-500 font-normal">mapeadas</span>
            </p>
        </div>
    </div>

    <form action="{{ route('admin.mapeos-erp.save') }}" method="POST">
        @csrf

        {{-- Tabs --}}
        <div class="border-b border-gray-200 mb-4">
            <nav class="flex gap-4">
                <button type="button" data-tab="subfamilias"
                        class="tab-btn px-3 py-2 border-b-2 border-blue-600 text-blue-600 font-medium text-sm">
                    Categorias ({{ $statsSub['total'] }})
                </button>
                {{-- <button type="button" data-tab="familias"
                        class="tab-btn px-3 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                    Familias ({{ $statsFam['total'] }})
                </button> --}}
            </nav>
        </div>

        {{-- Buscador --}}
        <div class="mb-4">
            <input type="text" id="filtroBusqueda" placeholder="Buscar por nombre del ERP..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Tabla Subfamilias --}}
        <div id="tab-subfamilias" class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-2 text-left w-12">PK</th>
                        <th class="px-4 py-2 text-left">Subfamilia del ERP</th>
                        {{-- <th class="px-4 py-2 text-left">→ Familia B2B</th> --}}
                        <th class="px-4 py-2 text-left">→ Categoría B2B</th>
                        <th class="px-4 py-2 text-center w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($subfamiliasErp as $i => $sf)
                        @php
                            $m = $mapeos['subfamilia'][$sf->pk_externa] ?? null;
                            $total  = $countsSub[$sf->pk_externa] ?? 0;
                            $sinCat = $sinCatSub[$sf->pk_externa] ?? 0;
                        @endphp
                        <tr class="row-mapeo" data-nombre="{{ strtolower($sf->nombre) }}">
                            <td class="px-4 py-2 text-gray-500">{{ $sf->pk_externa }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800">
                                {{ $sf->nombre }}
                                @if($total > 0)
                                    <span class="ml-1 text-xs text-gray-500">({{ $total }} producto{{ $total == 1 ? '' : 's' }})</span>
                                    @if($sinCat > 0)
                                        <span class="ml-1 text-xs px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 font-semibold" title="Sin categoría B2B asignada">
                                            {{ $sinCat }} sin cat.
                                        </span>
                                    @endif
                                @else
                                    <span class="ml-1 text-xs text-gray-400">(sin productos)</span>
                                @endif
                            </td>
                            {{-- <td class="px-4 py-2">
                                <input type="hidden" name="mapeos[sub-{{ $sf->pk_externa }}][entidad_tipo]" value="subfamilia">
                                <input type="hidden" name="mapeos[sub-{{ $sf->pk_externa }}][entidad_pk_externa]" value="{{ $sf->pk_externa }}">
                                <div class="flex gap-1">
                                    <select name="mapeos[sub-{{ $sf->pk_externa }}][familia_id]"
                                            class="select-familia flex-1 border border-gray-300 rounded px-2 py-1 text-sm">
                                        <option value="">— sin asignar —</option>
                                        @foreach($familiasB2B as $f)
                                            <option value="{{ $f->id }}" {{ ($m?->familia_id ?? null) == $f->id ? 'selected' : '' }}>
                                                {{ $f->titulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn-nueva-familia text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-blue-600" title="Crear familia nueva" data-erp-nombre="{{ $sf->nombre }}">+</button>
                                </div>
                            </td> --}}
                            <td class="px-4 py-2">
                                <div class="flex gap-1">
                                    <select name="mapeos[sub-{{ $sf->pk_externa }}][categoria_id]"
                                            class="select-categoria flex-1 border border-gray-300 rounded px-2 py-1 text-sm">
                                        <option value="">— sin asignar —</option>
                                        @foreach($categoriasB2B as $c)
                                            <option value="{{ $c->id }}" data-familia-id="{{ $c->familia_id }}" {{ ($m?->categoria_id ?? null) == $c->id ? 'selected' : '' }}>
                                                {{ $c->titulo }} {{ $c->familia ? '('.$c->familia->titulo.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn-nueva-categoria text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-blue-600" title="Crear categoría nueva" data-erp-nombre="{{ $sf->nombre }}">+</button>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if($total > 0)
                                    <button type="button"
                                            class="ver-productos text-xs text-blue-600 hover:underline"
                                            data-tipo="subfamilia"
                                            data-pk="{{ $sf->pk_externa }}"
                                            data-nombre="{{ $sf->nombre }}">
                                        Ver
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tabla Familias --}}
        <div id="tab-familias" class="bg-white rounded-lg shadow overflow-x-auto hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-2 text-left w-12">PK</th>
                        <th class="px-4 py-2 text-left">Categoria del ERP</th>
                        {{-- <th class="px-4 py-2 text-left">→ Familia B2B</th> --}}
                        <th class="px-4 py-2 text-left">→ Categoría B2B</th>
                        <th class="px-4 py-2 text-center w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($familiasErp as $i => $fer)
                        @php
                            $m = $mapeos['familia'][$fer->pk_externa] ?? null;
                            $total  = $countsFam[$fer->pk_externa] ?? 0;
                            $sinCat = $sinCatFam[$fer->pk_externa] ?? 0;
                        @endphp
                        <tr class="row-mapeo" data-nombre="{{ strtolower($fer->nombre) }}">
                            <td class="px-4 py-2 text-gray-500">{{ $fer->pk_externa }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800">
                                {{ $fer->nombre }}
                                @if($total > 0)
                                    <span class="ml-1 text-xs text-gray-500">({{ $total }} producto{{ $total == 1 ? '' : 's' }})</span>
                                    @if($sinCat > 0)
                                        <span class="ml-1 text-xs px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 font-semibold" title="Sin categoría B2B asignada">
                                            {{ $sinCat }} sin cat.
                                        </span>
                                    @endif
                                @else
                                    <span class="ml-1 text-xs text-gray-400">(sin productos)</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <input type="hidden" name="mapeos[fam-{{ $fer->pk_externa }}][entidad_tipo]" value="familia">
                                <input type="hidden" name="mapeos[fam-{{ $fer->pk_externa }}][entidad_pk_externa]" value="{{ $fer->pk_externa }}">
                                <div class="flex gap-1">
                                    <select name="mapeos[fam-{{ $fer->pk_externa }}][familia_id]"
                                            class="select-familia flex-1 border border-gray-300 rounded px-2 py-1 text-sm">
                                        <option value="">— sin asignar —</option>
                                        @foreach($familiasB2B as $f)
                                            <option value="{{ $f->id }}" {{ ($m?->familia_id ?? null) == $f->id ? 'selected' : '' }}>
                                                {{ $f->titulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn-nueva-familia text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-blue-600" title="Crear familia nueva" data-erp-nombre="{{ $fer->nombre }}">+</button>
                                </div>
                            </td>
                            {{-- <td class="px-4 py-2">
                                <div class="flex gap-1">
                                    <select name="mapeos[fam-{{ $fer->pk_externa }}][categoria_id]"
                                            class="select-categoria flex-1 border border-gray-300 rounded px-2 py-1 text-sm">
                                        <option value="">— sin asignar —</option>
                                        @foreach($categoriasB2B as $c)
                                            <option value="{{ $c->id }}" data-familia-id="{{ $c->familia_id }}" {{ ($m?->categoria_id ?? null) == $c->id ? 'selected' : '' }}>
                                                {{ $c->titulo }} {{ $c->familia ? '('.$c->familia->titulo.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn-nueva-categoria text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-blue-600" title="Crear categoría nueva" data-erp-nombre="{{ $fer->nombre }}">+</button>
                                </div>
                            </td> --}}
                            <td class="px-4 py-2 text-center">
                                @if($total > 0)
                                    <button type="button"
                                            class="ver-productos text-xs text-blue-600 hover:underline"
                                            data-tipo="familia"
                                            data-pk="{{ $fer->pk_externa }}"
                                            data-nombre="{{ $fer->nombre }}">
                                        Ver
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                Guardar mapeos
            </button>
        </div>
    </form>

    <p class="mt-6 text-xs text-gray-500">
        💡 <strong>Tip:</strong> "Subfamilia" tiene precedencia sobre "Familia" — si un producto tiene un mapeo de subfamilia, ese gana.
        El sistema solo asigna categorías a productos que NO tienen ninguna (no se pisan las asignaciones manuales).
        Las etiquetas <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 text-[10px] font-semibold">X sin cat.</span>
        muestran cuántos productos de esa familia/subfamilia siguen sin categoría B2B.
    </p>
</div>

{{-- Modal: crear familia B2B --}}
<div id="modalFamilia" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between border-b px-5 py-3">
            <h3 class="text-lg font-semibold text-slate-900">Crear familia B2B</h3>
            <button type="button" class="modal-close text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-5 space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                <input type="text" id="familia-titulo" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Ej: Hilos premium" autofocus>
                <p id="familia-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>
        </div>
        <div class="border-t px-5 py-3 flex justify-end gap-2">
            <button type="button" class="modal-close px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm">Cancelar</button>
            <button type="button" id="btn-guardar-familia" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium">Crear</button>
        </div>
    </div>
</div>

{{-- Modal: crear categoría B2B --}}
<div id="modalCategoria" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between border-b px-5 py-3">
            <h3 class="text-lg font-semibold text-slate-900">Crear categoría B2B</h3>
            <button type="button" class="modal-close text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-5 space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                <input type="text" id="categoria-titulo" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Ej: Soga Sisal de 16mm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Familia padre <span class="text-red-500">*</span></label>
                <select id="categoria-familia" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">— elegir familia —</option>
                    @foreach($familiasB2B as $f)
                        <option value="{{ $f->id }}">{{ $f->titulo }}</option>
                    @endforeach
                </select>
            </div>
            <p id="categoria-error" class="text-xs text-red-600 mt-1 hidden"></p>
        </div>
        <div class="border-t px-5 py-3 flex justify-end gap-2">
            <button type="button" class="modal-close px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm">Cancelar</button>
            <button type="button" id="btn-guardar-categoria" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium">Crear</button>
        </div>
    </div>
</div>

{{-- Modal de productos --}}
<div id="modalProductos" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between border-b px-5 py-3">
            <h3 class="text-lg font-semibold text-slate-900">
                Productos de <span id="modalTitulo" class="text-blue-600"></span>
                <span id="modalTotal" class="text-sm text-gray-500 font-normal ml-2"></span>
            </h3>
            <button type="button" id="modalClose" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
        <div class="flex-1 overflow-y-auto">
            <div id="modalContent" class="p-4">
                <p class="text-center text-gray-500 py-8">Cargando…</p>
            </div>
        </div>
        <div class="border-t px-5 py-3 text-right">
            <button type="button" id="modalCloseFooter" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm">Cerrar</button>
        </div>
    </div>
</div>

<script>
(function () {
    // Tabs
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(btn => btn.addEventListener('click', () => {
        tabs.forEach(b => {
            b.classList.remove('border-blue-600', 'text-blue-600');
            b.classList.add('border-transparent', 'text-gray-500');
        });
        btn.classList.add('border-blue-600', 'text-blue-600');
        btn.classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('tab-subfamilias').classList.add('hidden');
        document.getElementById('tab-familias').classList.add('hidden');
        document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
    }));

    // Filtro
    document.getElementById('filtroBusqueda').addEventListener('input', function (e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.row-mapeo').forEach(row => {
            row.style.display = row.dataset.nombre.includes(q) ? '' : 'none';
        });
    });

    // ========== Modales: crear familia / crear categoría ==========
    const csrfToken = '{{ csrf_token() }}';
    let selectActivo = null;  // el <select> que disparó el modal de crear

    // Cerrar cualquier modal con .modal-close
    document.querySelectorAll('.modal-close').forEach(el => {
        el.addEventListener('click', () => {
            document.getElementById('modalFamilia').classList.add('hidden');
            document.getElementById('modalCategoria').classList.add('hidden');
        });
    });

    // Botón "+ familia"
    document.querySelectorAll('.btn-nueva-familia').forEach(btn => {
        btn.addEventListener('click', () => {
            selectActivo = btn.previousElementSibling;
            const nombreErp = btn.dataset.erpNombre || '';
            const input = document.getElementById('familia-titulo');
            input.value = nombreErp;
            document.getElementById('familia-error').classList.add('hidden');
            document.getElementById('modalFamilia').classList.remove('hidden');
            // Auto-focus + selecciona todo el texto, así el admin puede editar al toque
            setTimeout(() => { input.focus(); input.select(); }, 50);
        });
    });

    // Guardar familia
    document.getElementById('btn-guardar-familia').addEventListener('click', async () => {
        const titulo = document.getElementById('familia-titulo').value.trim();
        const errorEl = document.getElementById('familia-error');
        errorEl.classList.add('hidden');
        if (!titulo) {
            errorEl.textContent = 'El título es obligatorio';
            errorEl.classList.remove('hidden');
            return;
        }
        try {
            const r = await fetch(`{{ route('admin.mapeos-erp.crear-familia') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ titulo })
            });
            const data = await r.json();
            if (!r.ok) {
                errorEl.textContent = data.message || (data.errors?.titulo?.[0]) || 'Error';
                errorEl.classList.remove('hidden');
                return;
            }
            // Agregar option a TODOS los selects de familia
            document.querySelectorAll('.select-familia').forEach(sel => {
                const opt = new Option(data.titulo, data.id);
                sel.appendChild(opt);
            });
            // Y también al dropdown del modal de categoría
            const catFam = document.getElementById('categoria-familia');
            catFam.appendChild(new Option(data.titulo, data.id));
            // Seleccionar la nueva en el select que la disparó
            if (selectActivo) selectActivo.value = data.id;
            document.getElementById('modalFamilia').classList.add('hidden');
        } catch (err) {
            errorEl.textContent = 'Error de red';
            errorEl.classList.remove('hidden');
        }
    });

    // Botón "+ categoría"
    document.querySelectorAll('.btn-nueva-categoria').forEach(btn => {
        btn.addEventListener('click', () => {
            selectActivo = btn.previousElementSibling;
            const nombreErp = btn.dataset.erpNombre || '';
            const input = document.getElementById('categoria-titulo');
            input.value = nombreErp;

            // Si la fila ya tiene una Familia B2B elegida, la pre-seleccionamos como padre
            const familiaSelect = btn.closest('tr')?.querySelector('.select-familia');
            document.getElementById('categoria-familia').value = familiaSelect?.value || '';

            document.getElementById('categoria-error').classList.add('hidden');
            document.getElementById('modalCategoria').classList.remove('hidden');
            setTimeout(() => { input.focus(); input.select(); }, 50);
        });
    });

    // Guardar categoría
    document.getElementById('btn-guardar-categoria').addEventListener('click', async () => {
        const titulo = document.getElementById('categoria-titulo').value.trim();
        const familiaId = document.getElementById('categoria-familia').value;
        const errorEl = document.getElementById('categoria-error');
        errorEl.classList.add('hidden');
        if (!titulo || !familiaId) {
            errorEl.textContent = 'Título y familia padre son obligatorios';
            errorEl.classList.remove('hidden');
            return;
        }
        try {
            const r = await fetch(`{{ route('admin.mapeos-erp.crear-categoria') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ titulo, familia_id: familiaId })
            });
            const data = await r.json();
            if (!r.ok) {
                errorEl.textContent = data.message || 'Error';
                errorEl.classList.remove('hidden');
                return;
            }
            // Agregar option a TODOS los selects de categoría
            const label = `${data.titulo}${data.familia ? ' (' + data.familia + ')' : ''}`;
            document.querySelectorAll('.select-categoria').forEach(sel => {
                const opt = new Option(label, data.id);
                opt.dataset.familiaId = data.familia_id;
                sel.appendChild(opt);
            });
            if (selectActivo) selectActivo.value = data.id;
            document.getElementById('modalCategoria').classList.add('hidden');
        } catch (err) {
            errorEl.textContent = 'Error de red';
            errorEl.classList.remove('hidden');
        }
    });

    // Modal "Ver productos"
    const modal       = document.getElementById('modalProductos');
    const modalTitulo = document.getElementById('modalTitulo');
    const modalTotal  = document.getElementById('modalTotal');
    const modalContent = document.getElementById('modalContent');
    const closeModal  = () => modal.classList.add('hidden');

    document.getElementById('modalClose').addEventListener('click', closeModal);
    document.getElementById('modalCloseFooter').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    document.querySelectorAll('.ver-productos').forEach(btn => {
        btn.addEventListener('click', async () => {
            const tipo = btn.dataset.tipo;
            const pk   = btn.dataset.pk;
            const nombre = btn.dataset.nombre;
            modalTitulo.textContent = `${tipo === 'familia' ? 'Familia' : 'Subfamilia'} ERP "${nombre}"`;
            modalTotal.textContent = '';
            modalContent.innerHTML = '<p class="text-center text-gray-500 py-8">Cargando…</p>';
            modal.classList.remove('hidden');

            try {
                const r = await fetch(`{{ route('admin.mapeos-erp.productos') }}?tipo=${tipo}&pk=${pk}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();
                modalTotal.textContent = `(${data.total} producto${data.total === 1 ? '' : 's'})`;

                if (!data.productos.length) {
                    modalContent.innerHTML = '<p class="text-center text-gray-500 py-8">No hay productos.</p>';
                    return;
                }

                let html = '<table class="w-full text-sm"><thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr>';
                html += '<th class="px-3 py-2 text-left">Código</th>';
                html += '<th class="px-3 py-2 text-left">Título</th>';
                html += '<th class="px-3 py-2 text-left">Categorías B2B asignadas</th>';
                html += '<th class="px-3 py-2 text-right">Precio</th>';
                html += '</tr></thead><tbody class="divide-y divide-gray-100">';
                for (const p of data.productos) {
                    const cats = p.categorias
                        ? `<span class="text-green-700">${p.categorias}</span>`
                        : '<span class="text-amber-600 italic">— sin categoría —</span>';
                    const precio = parseFloat(p.precio_unitario).toLocaleString('es-AR', { minimumFractionDigits: 2 });
                    html += `<tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-mono text-xs text-gray-700">${p.codigo ?? '-'}</td>
                        <td class="px-3 py-2 text-gray-800">${p.titulo ?? ''}</td>
                        <td class="px-3 py-2">${cats}</td>
                        <td class="px-3 py-2 text-right">$ ${precio}</td>
                    </tr>`;
                }
                html += '</tbody></table>';
                modalContent.innerHTML = html;
            } catch (err) {
                modalContent.innerHTML = '<p class="text-center text-red-600 py-8">Error al cargar los productos.</p>';
            }
        });
    });
})();
</script>
@endsection
