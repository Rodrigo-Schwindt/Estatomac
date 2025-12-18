@extends('layouts.admin')

@section('content')

<div class="space-y-10 pb-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">

    <div id="alert-success" class="hidden bg-green-100 border border-green-400 text-green-800 px-6 py-3 rounded-md mb-4"></div>

    <section class="space-y-4">
        <h3 class="text-xl font-semibold text-slate-900">Banner Principal</h3>
        <p class="text-sm text-slate-600 italic">Imagen superior usada como portada.</p>

        <div>
            @if($banner && $banner->image_banner)
                <img id="banner-preview"
                     src="{{ Storage::url($banner->image_banner) }}"
                     class="w-full max-h-64 object-cover rounded-md border border-slate-200">
                <div id="banner-placeholder"
                     class="hidden h-32 w-full max-w-xs bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm">
                    Sin imagen
                </div>
            @else
                <img id="banner-preview"
                     class="hidden w-full max-h-64 object-cover rounded-md border border-slate-200">
                <div id="banner-placeholder"
                     class="h-32 w-full max-w-xs bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm">
                    Sin imagen
                </div>
            @endif
        </div>

        <div class="flex flex-wrap gap-2">
            <input id="file-image_banner" type="file" class="hidden" accept="image/*">

            <button type="button"
                    onclick="document.getElementById('file-image_banner').click()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                {{ $banner && $banner->image_banner ? 'Cambiar imagen' : 'Subir imagen' }}
            </button>

        </div>

        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            Resolución recomendada: 1360×450px<br>
            Peso recomendado: hasta 3MB<br>
            Formatos permitidos: JPG, PNG, WEBP
        </p>

        <p id="banner-error" class="text-red-600 text-xs mt-1 hidden"></p>

        <button id="btn-save-banner"
                type="button"
                class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm cursor-pointer mt-3 hidden">
            Guardar Banner
        </button>
    </section>

    <div class="flex justify-between items-center pb-4">
        <h2 class="text-2xl font-bold text-slate-900">Novedades (<span id="total-count">{{ $novedades->total() }}</span>)</h2>

        <a href="{{ route('novedades.create') }}"
           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition cursor-pointer">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Crear Nueva Novedad
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-md p-4 shadow-sm">
        <div class="relative">
            <input type="text"
                   id="search-input"
                   value="{{ $search }}"
                   placeholder="Buscar por título o descripción..."
                   class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-md text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">

            <svg class="absolute top-2.5 left-3 w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <div class="text-center text-sm text-slate-600 pb-1 font-medium" id="pagination-info">
        Página {{ $novedades->currentPage() }} de {{ $novedades->lastPage() }} ·
        Mostrando {{ $novedades->firstItem() }}–{{ $novedades->lastItem() }} de {{ $novedades->total() }}
    </div>

    <div id="ajax-wrapper">

        <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 text-xs uppercase">
                    <tr>
                        <th class="p-4 text-center font-medium w-20">Orden</th>
                        <th class="p-4 text-start font-medium">Título</th>
                        <th class="p-4 text-center font-medium w-20">Imagen</th>
                        <th class="p-4 text-center font-medium w-24">Destacado</th>
                        <th class="p-4 text-center font-medium w-52">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach($novedades as $nov)
                        <tr class="hover:bg-slate-50 transition">

                            <td class="p-4 text-center font-mono uppercase">{{ $nov->orden }}</td>

                            <td class="p-4 font-semibold text-slate-900">{{ $nov->title }}</td>

                            <td class="p-4 text-center">
                                @if($nov->image)
                                    <img src="{{ Storage::url($nov->image) }}"
                                        class="h-10 w-10 object-cover rounded-md mx-auto border border-slate-200">
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <td class="p-4 text-center">
                                <button
                                    data-id="{{ $nov->id }}"
                                    class="btn-toggle-destacado text-xl hover:scale-110 transition cursor-pointer">
                                    {{ $nov->destacado ? '⭐' : '—' }}
                                </button>
                            </td>

                            <td class="p-4 flex items-center justify-center gap-4">
                                <a href="{{ route('novedades.edit', $nov->id) }}"
                                   class="text-slate-500 hover:text-blue-600 transition cursor-pointer"
                                   title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>

                                <button data-id="{{ $nov->id }}"
                                        class="btn-delete text-red-600 hover:text-red-700 transition cursor-pointer"
                                        title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                            m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4"/>
                                    </svg>
                                </button>
                            </td>

                        </tr>
                    @endforeach

                    @if($novedades->count() === 0)
                        <tr>
                            <td colspan="5" class="p-8 text-center italic text-slate-500">
                                No hay novedades disponibles.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="pt-4 flex justify-center">
            @if ($novedades->lastPage() > 1)
                <div class="flex gap-2">
                    @for ($i = 1; $i <= $novedades->lastPage(); $i++)
                        <a href="#"
                           data-page="{{ $i }}"
                           class="ajax-page px-3 py-1 border rounded-md text-sm
                                {{ $i == $novedades->currentPage()
                                    ? 'bg-gray-200 text-gray-700'
                                    : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
            @endif
        </div>

    </div>

</div>

<style>
@keyframes fadeIn { from {opacity:0; transform:translateY(6px)} to {opacity:1; transform:translateY(0)} }
.animate-fadeIn { animation: fadeIn .28s ease; }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const searchInput      = document.getElementById("search-input");
    const alertBox         = document.getElementById("alert-success");
    const bannerInput      = document.getElementById("file-image_banner");
    const bannerPreview    = document.getElementById("banner-preview");
    const bannerPlaceholder= document.getElementById("banner-placeholder");
    const btnSaveBanner    = document.getElementById("btn-save-banner");
    const btnRemoveBanner  = document.getElementById("btn-remove-banner");
    const bannerError      = document.getElementById("banner-error");

    let currentPage = {{ $novedades->currentPage() }};

    if (searchInput) {
        searchInput.addEventListener("input", () => {
            currentPage = 1;
            loadTable(currentPage);
        });
    }

    document.addEventListener("click", (e) => {
        const pageLink = e.target.closest(".ajax-page");
        if (pageLink) {
            e.preventDefault();
            const page = pageLink.dataset.page;
            currentPage = page;
            loadTable(page);
        }
    });

    document.addEventListener("click", (e) => {
        const del = e.target.closest(".btn-delete");
        if (del) {
            const id = del.dataset.id;
            if (confirm("¿Eliminar novedad?")) {
                deleteNovedad(id);
            }
        }
    });

    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-toggle-destacado");
        if (btn) {
            const id = btn.dataset.id;
            toggleDestacado(id);
        }
    });

    if (bannerInput) {
        bannerInput.addEventListener("change", () => {
            bannerError.classList.add("hidden");
            const file = bannerInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    bannerPreview.src = ev.target.result;
                    bannerPreview.classList.remove("hidden");
                    bannerPlaceholder.classList.add("hidden");
                };
                reader.readAsDataURL(file);
                btnSaveBanner.classList.remove("hidden");
            } else {
                btnSaveBanner.classList.add("hidden");
            }
        });
    }

    if (btnSaveBanner) {
        btnSaveBanner.addEventListener("click", () => {
            const file = bannerInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append("image_banner", file);

            fetch("{{ route('novedades.banner.save') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.errors || data.error) {
                    bannerError.textContent = data.errors?.image_banner?.[0] || data.error || "Error al guardar el banner";
                    bannerError.classList.remove("hidden");
                } else {
                    showSuccess(data.success || "Banner actualizado");
                    if (data.url) {
                        bannerPreview.src = data.url;
                        bannerPreview.classList.remove("hidden");
                        bannerPlaceholder.classList.add("hidden");
                        btnRemoveBanner.classList.remove("hidden");
                    }
                    bannerInput.value = "";
                    btnSaveBanner.classList.add("hidden");
                }
            });
        });
    }

    if (btnRemoveBanner) {
        btnRemoveBanner.addEventListener("click", () => {
            if (!confirm("¿Eliminar banner?")) return;

            fetch("{{ route('novedades.banner.remove') }}", {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.success);
                    bannerPreview.src = "";
                    bannerPreview.classList.add("hidden");
                    bannerPlaceholder.classList.remove("hidden");
                    btnRemoveBanner.classList.add("hidden");
                }
            });
        });
    }

    function loadTable(page = 1) {
        const search = searchInput ? searchInput.value : "";

        fetch(`{{ route('novedades.index') }}?page=${page}&search=${encodeURIComponent(search)}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById("ajax-wrapper").innerHTML = data.html;
            document.getElementById("total-count").innerText = data.total;

            const info = document.getElementById("pagination-info");
            if (data.pages > 0) {
                info.innerText = `Página ${page} de ${data.pages} · ${data.total} resultados`;
            } else {
                info.innerText = "No hay resultados disponibles";
            }
        });
    }

    function deleteNovedad(id) {
        fetch(`{{ url('/admin/novedades') }}/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.success);
                loadTable(currentPage);
            }
        });
    }

    function toggleDestacado(id) {
        fetch(`{{ url('/admin/novedades') }}/${id}/destacado`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.success);
                loadTable(currentPage);
            }
        });
    }

    function showSuccess(msg) {
        if (!alertBox) return;
        alertBox.innerText = msg;
        alertBox.classList.remove("hidden");
        setTimeout(() => alertBox.classList.add("hidden"), 2500);
    }
});
</script>

@endsection
