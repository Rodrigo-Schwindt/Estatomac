@extends('layouts.admin')

@section('content')

{{-- @php
use Illuminate\Support\Str;

function metadataName($section, $products, $categories, $infoTecnicas, $catalogos, $novedades){
    if (str_starts_with($section,'producto-')) {
        $id = (int) str_replace('producto-','',$section);
        $p = $products->firstWhere('id',$id);
        return $p ? "Producto: {$p->title}" : "Producto #$id";
    }
    if (str_starts_with($section,'categoria-')) {
        $id = (int) str_replace('categoria-','',$section);
        $c = $categories->firstWhere('id',$id);
        return $c ? "Categoría: {$c->title}" : "Categoría #$id";
    }
    if (str_starts_with($section,'info-tecnica-')) {
        $id = (int) str_replace('info-tecnica-','',$section);
        $i = $infoTecnicas->firstWhere('id',$id);
        return $i ? "Info Técnica: {$i->title}" : "Info Técnica #$id";
    }
    if (str_starts_with($section,'catalogo-')) {
        $id = (int) str_replace('catalogo-','',$section);
        $c = $catalogos->firstWhere('id',$id);
        return $c ? "Catálogo: {$c->title}" : "Catálogo #$id";
    }
    if (str_starts_with($section,'novedad-')) {
        $id = (int) str_replace('novedad-','',$section);
        $n = $novedades->firstWhere('id',$id);
        return $n ? "Novedad: {$n->title}" : "Novedad #$id";
    }

    $map = [
        'home' => 'Inicio',
        'nosotros' => 'Nosotros',
        'categorias' => 'Categorías',
        'info-tecnica' => 'Información Técnica',
        'catalogos' => 'Catálogos',
        'novedades' => 'Novedades',
        'contacto' => 'Contacto',
    ];

    return $map[$section] ?? $section;
}

$currentFormMode = old('formMode','create');
$metadataId = old('metadataId');
$hasErrors = $errors->any();
$initialType = old('metadataType','section');
$descVal = old('description','');
@endphp --}}

<div class="min-h-screen py-6 px-4">

    <div class="space-y-10 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg px-4 py-3">
                Hubo errores al guardar.
            </div>
        @endif

        <div id="list">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Metadatos SEO</h1>
                    <p class="text-sm text-gray-500 mt-1">Gestioná las etiquetas meta.</p>
                </div>

                <div class="flex gap-3">
                    <form method="GET" action="{{ route('admin.metadata') }}" class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar..."
                               class="pl-3 pr-3 py-2 w-72 text-sm border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500">
                    </form>

                    <button onclick="openCreate()" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm cursor-pointer">
                        Nuevo metadato
                    </button>
                </div>
            </div>

            <div class="text-xs text-gray-500 px-1 mt-2">
                Mostrando {{ $items->firstItem() }}–{{ $items->lastItem() }} de {{ $items->total() }}
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-2 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-500">Sección</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-500">Keywords</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-500">Descripción</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-500">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900">
                                    {{ metadataName($item->section, $products, $categories, $infoTecnicas, $catalogos, $novedades) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ Str::limit($item->keywords, 50) }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ Str::limit($item->description, 60) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button onclick="editMeta(this)"
                                        data-id="{{ $item->id }}"
                                        data-section="{{ $item->section }}"
                                        data-keywords="{{ $item->keywords }}"
                                        data-description="{{ $item->description }}"
                                        class="text-blue-600 hover:text-blue-800 mr-3">
                                    Editar
                                </button>
                                <button onclick="deleteMeta({{ $item->id }})"
                                        class="text-red-600 hover:text-red-800">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $items->links() }}</div>
        </div>

        <div id="form" class="hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold mb-5" id="formTitle">
                    {{-- {{ $currentFormMode === 'edit' ? 'Editar' : 'Crear' }} metadato --}}
                </h2>

                <form method="POST" action="{{ route('admin.metadata.save') }}">
                    @csrf

                    {{-- <input type="hidden" name="metadataId" id="metadataId" value="{{ old('metadataId') }}">
                    <input type="hidden" name="formMode" id="formMode" value="{{ $currentFormMode }}">
                    <input type="hidden" name="itemId" id="itemId" value="{{ old('itemId') }}"> --}}

                    <div class="grid md:grid-cols-1 gap-6">

                        <div class="space-y-4">

                            <div>
                                <label class="font-semibold text-sm">Tipo *</label>
                                <select name="metadataType" id="metadataType"
                                        class="w-full text-sm border rounded-lg px-3 py-2">
                                    {{-- <option value="section" {{ $initialType==='section'?'selected':'' }}>Sección</option>
                                    <option value="producto" {{ $initialType==='producto'?'selected':'' }}>Producto</option>
                                    <option value="categoria" {{ $initialType==='categoria'?'selected':'' }}>Categoría</option>
                                    <option value="info-tecnica" {{ $initialType==='info-tecnica'?'selected':'' }}>Info Técnica</option>
                                    <option value="catalogo" {{ $initialType==='catalogo'?'selected':'' }}>Catálogo</option>
                                    <option value="novedad" {{ $initialType==='novedad'?'selected':'' }}>Novedad</option> --}}
                                </select>
                            </div>

                            <div id="sectionGroup">
                                <label class="font-semibold text-sm">Sección *</label>
                                <select name="section" id="section"
                                        class="w-full text-sm border rounded-lg px-3 py-2">
                                    <option value="">Seleccionar...</option>
                                    @foreach($sections as $sec)
                                        <option value="{{ $sec }}" {{ old('section') === $sec ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('-', ' ', $sec)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="itemGroup" class="hidden">
                                <label class="font-semibold text-sm" id="itemLabel"></label>

                                <select id="selProducto" class="w-full text-sm border rounded-lg px-3 py-2 hidden">
                                    <option value="">Seleccionar producto...</option>
                                    {{-- @foreach($products as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->title }}</option>
                                    @endforeach --}}
                                </select>

                                <select id="selCategoria" class="w-full text-sm border rounded-lg px-3 py-2 hidden">
                                    <option value="">Seleccionar categoría...</option>
                                    {{-- @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                    @endforeach --}}
                                </select>

                                <select id="selInfoTecnica" class="w-full text-sm border rounded-lg px-3 py-2 hidden">
                                    <option value="">Seleccionar info técnica...</option>
                                    {{-- @foreach($infoTecnicas as $info)
                                        <option value="{{ $info->id }}">{{ $info->title }}</option>
                                    @endforeach --}}
                                </select>

                                <select id="selCatalogo" class="w-full text-sm border rounded-lg px-3 py-2 hidden">
                                    <option value="">Seleccionar catálogo...</option>
                                    {{-- @foreach($catalogos as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                    @endforeach --}}
                                </select>

                                <select id="selNovedad" class="w-full text-sm border rounded-lg px-3 py-2 hidden">
                                    <option value="">Seleccionar novedad...</option>
                                    {{-- @foreach($novedades as $nov)
                                        <option value="{{ $nov->id }}">{{ $nov->title }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="font-semibold text-sm">Keywords *</label>
                                <textarea name="keywords" id="keywords"
                                    class="w-full border rounded-lg px-3 py-2 text-sm"
                                    rows="3">{{ old('keywords') }}</textarea>
                            </div>

                            <div>
                                <label class="font-semibold text-sm">Descripción *</label>
                                <textarea name="description" id="description"
                                    class="w-full border rounded-lg px-3 py-2 text-sm"
                                    maxlength="160"
                                    rows="4"></textarea>
                                <div class="text-[11px] text-gray-500">
                                    {{-- <span id="dCount">{{ Str::length($descVal) }}</span>/160 --}}
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button class="px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm cursor-pointer">
                            Guardar
                        </button>
                        <button type="button" onclick="closeForm()"
                                class="px-5 py-2.5 rounded-lg bg-gray-200 text-gray-700 text-sm cursor-pointer">
                            Cancelar
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

<script>
const list = document.getElementById('list');
const form = document.getElementById('form');
const tSel = document.getElementById('metadataType');
const sectionGroup = document.getElementById('sectionGroup');
const itemGroup = document.getElementById('itemGroup');
const itemId = document.getElementById('itemId');
const selProducto = document.getElementById('selProducto');
const selCategoria = document.getElementById('selCategoria');
const selInfoTecnica = document.getElementById('selInfoTecnica');
const selCatalogo = document.getElementById('selCatalogo');
const selNovedad = document.getElementById('selNovedad');
const section = document.getElementById('section');
const keywords = document.getElementById('keywords');
const description = document.getElementById('description');
const dCount = document.getElementById('dCount');
const formTitle = document.getElementById('formTitle');
const metadataId = document.getElementById('metadataId');
const formMode = document.getElementById('formMode');
const itemLabel = document.getElementById('itemLabel');

function openCreate(){
    resetForm();
    formTitle.innerText = "Crear metadato";
    list.classList.add('hidden');
    form.classList.remove('hidden');
}

function closeForm(){
    form.classList.add('hidden');
    list.classList.remove('hidden');
}

function resetForm(){
    metadataId.value = "";
    formMode.value = "create";
    section.value = "";
    itemId.value = "";
    keywords.value = "";
    description.value = "";
    dCount.textContent = "0";
    tSel.value = "section";
    updateUI();
}

function editMeta(btn){
    resetForm();
    formMode.value = "edit";
    formTitle.innerText = "Editar metadato";

    metadataId.value = btn.dataset.id;
    const sec = btn.dataset.section;
    const k = btn.dataset.keywords;
    const d = btn.dataset.description;

    if (sec.startsWith("producto-")){
        tSel.value = "producto";
        itemId.value = sec.replace("producto-","");
        selProducto.value = itemId.value;
    } else if (sec.startsWith("categoria-")){
        tSel.value = "categoria";
        itemId.value = sec.replace("categoria-","");
        selCategoria.value = itemId.value;
    } else if (sec.startsWith("info-tecnica-")){
        tSel.value = "info-tecnica";
        itemId.value = sec.replace("info-tecnica-","");
        selInfoTecnica.value = itemId.value;
    } else if (sec.startsWith("catalogo-")){
        tSel.value = "catalogo";
        itemId.value = sec.replace("catalogo-","");
        selCatalogo.value = itemId.value;
    } else if (sec.startsWith("novedad-")){
        tSel.value = "novedad";
        itemId.value = sec.replace("novedad-","");
        selNovedad.value = itemId.value;
    } else {
        tSel.value = "section";
        section.value = sec;
    }

    keywords.value = k;
    description.value = d;
    dCount.innerText = d.length;

    updateUI();
    list.classList.add('hidden');
    form.classList.remove('hidden');
}

function updateUI(){
    const type = tSel.value;

    if (type === "section"){
        sectionGroup.classList.remove("hidden");
        itemGroup.classList.add("hidden");
        itemId.value = "";
    } else {
        sectionGroup.classList.add("hidden");
        itemGroup.classList.remove("hidden");

        selProducto.classList.add("hidden");
        selCategoria.classList.add("hidden");
        selInfoTecnica.classList.add("hidden");
        selCatalogo.classList.add("hidden");
        selNovedad.classList.add("hidden");

        if (type === "producto"){
            itemLabel.textContent = "Producto *";
            selProducto.classList.remove("hidden");
            itemId.value = selProducto.value;
        }
        if (type === "categoria"){
            itemLabel.textContent = "Categoría *";
            selCategoria.classList.remove("hidden");
            itemId.value = selCategoria.value;
        }
        if (type === "info-tecnica"){
            itemLabel.textContent = "Info Técnica *";
            selInfoTecnica.classList.remove("hidden");
            itemId.value = selInfoTecnica.value;
        }
        if (type === "catalogo"){
            itemLabel.textContent = "Catálogo *";
            selCatalogo.classList.remove("hidden");
            itemId.value = selCatalogo.value;
        }
        if (type === "novedad"){
            itemLabel.textContent = "Novedad *";
            selNovedad.classList.remove("hidden");
            itemId.value = selNovedad.value;
        }
    }
}

tSel.addEventListener('change', updateUI);

selProducto.addEventListener('change', ()=> itemId.value = selProducto.value );
selCategoria.addEventListener('change', ()=> itemId.value = selCategoria.value );
selInfoTecnica.addEventListener('change', ()=> itemId.value = selInfoTecnica.value );
selCatalogo.addEventListener('change', ()=> itemId.value = selCatalogo.value );
selNovedad.addEventListener('change', ()=> itemId.value = selNovedad.value );

description.addEventListener('input', ()=> dCount.innerText = description.value.length );

function deleteMeta(id){
    if(!confirm("¿Eliminar metadato?")) return;
    fetch(`/admin/metadata/${id}`,{
        method:"DELETE",
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
    }).then(()=>location.reload());
}


</script>

@endsection