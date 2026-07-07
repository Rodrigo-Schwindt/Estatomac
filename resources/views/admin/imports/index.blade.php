@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-5xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Cargas masivas (ERP → B2B)</h1>
        <p class="text-gray-500 text-sm mt-1">
            Cada carga <strong>reemplaza</strong> la tabla destino. Si falla, se hace
            <strong>rollback automático</strong> y los datos originales quedan operativos.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ===== Clientes + Canales ===== --}}
    <section class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-800 mb-2">Actualización masiva de Clientes y Canales</h2>
        <p class="text-xs text-gray-500 mb-4">
            Archivo Excel/CSV con cabeceras en la primera fila. Columnas reconocidas:
            <code class="bg-gray-100 px-1 rounded">codigo</code>,
            <code class="bg-gray-100 px-1 rounded">cliente</code> (o nombre),
            <code class="bg-gray-100 px-1 rounded">nombre_fantasia</code>,
            <code class="bg-gray-100 px-1 rounded">direccion</code>,
            <code class="bg-gray-100 px-1 rounded">telefono</code>,
            <code class="bg-gray-100 px-1 rounded">celular</code>,
            <code class="bg-gray-100 px-1 rounded">codigo_postal</code>,
            <code class="bg-gray-100 px-1 rounded">ciudades</code>,
            <code class="bg-gray-100 px-1 rounded">provincia</code>,
            <code class="bg-gray-100 px-1 rounded">email</code>,
            <code class="bg-gray-100 px-1 rounded">whatsapp</code>,
            <code class="bg-gray-100 px-1 rounded">condicion_iva</code>,
            <code class="bg-gray-100 px-1 rounded">cuit</code>,
            <code class="bg-gray-100 px-1 rounded">activo</code>,
            <code class="bg-gray-100 px-1 rounded">condicion_venta</code>,
            <code class="bg-gray-100 px-1 rounded">tipo_operacion</code>,
            <code class="bg-gray-100 px-1 rounded">descuento</code>,
            <code class="bg-gray-100 px-1 rounded">transporte</code>,
            <code class="bg-gray-100 px-1 rounded">vendedor</code>,
            <code class="bg-gray-100 px-1 rounded">rubro_cliente</code>,
            <code class="bg-gray-100 px-1 rounded">tipo_lista</code>,
            <code class="bg-gray-100 px-1 rounded">canal</code>,
            <code class="bg-gray-100 px-1 rounded">descuento_canal</code>.
        </p>
        <form action="{{ route('admin.imports.clientes') }}" method="POST" enctype="multipart/form-data"
              onsubmit="return confirm('Esto va a reemplazar TODOS los clientes existentes. ¿Continuar?')">
            @csrf
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <input type="file" name="archivo" accept=".xls,.xlsx,.csv" required
                       class="flex-1 text-sm border border-gray-300 rounded-lg p-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Cargar Clientes
                </button>
            </div>
        </form>
    </section>

    {{-- ===== Productos + ListaPrecio cabecera ===== --}}
    <section class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-800 mb-2">Actualización masiva de Productos y Lista de Precios</h2>
        <p class="text-xs text-gray-500 mb-4">
            Sube el Excel/CSV de productos. Opcionalmente, definí la cabecera de la lista de precios
            (los importes detallados por producto se mantienen tal cual están hoy).
        </p>
        <form action="{{ route('admin.imports.productos') }}" method="POST" enctype="multipart/form-data"
              onsubmit="return confirm('Esto va a reemplazar TODOS los productos existentes. ¿Continuar?')">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Archivo de productos *</label>
                    <input type="file" name="archivo" accept=".xls,.xlsx,.csv" required
                           class="w-full text-sm border border-gray-300 rounded-lg p-2">
                    <p class="text-xs text-gray-500 mt-1">
                        Columnas: <code>codigo</code>, <code>titulo</code>, <code>descripcion</code>,
                        <code>presentacion</code>, <code>precio_paquete</code>, <code>precio_unitario</code>,
                        <code>precio_kilo</code>, <code>descuento</code>, <code>bulto</code>, <code>bulto_cantidad</code>,
                        <code>codigo_color</code>, <code>nombre_color</code>, <code>porcentaje_aumento</code>.
                    </p>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-3">Cabecera de Lista de Precios (opcional)</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Número</label>
                            <input type="number" name="lista_numero" class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-600 mb-1">Título</label>
                            <input type="text" name="lista_titulo" class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Vigente desde</label>
                            <input type="date" name="lista_fecha_desde" class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Archivo de la lista (PDF/Excel)</label>
                            <input type="file" name="lista_archivo" accept=".pdf,.xls,.xlsx" class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="hidden" name="lista_vigente" value="0">
                                <input type="checkbox" name="lista_vigente" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Activar como vigente
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Cargar Productos
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- ===== Baja rotación ===== --}}
    <section class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-800 mb-2">Actualización de tablas asociadas (baja rotación)</h2>
        <p class="text-xs text-gray-500 mb-4">
            Excel con <strong>4 hojas en este orden</strong>:
            <strong>1) Rubros</strong>,
            <strong>2) Familias</strong>,
            <strong>3) Categorías</strong>,
            <strong>4) Colores</strong>.
            La primera fila de cada hoja son las cabeceras (columnas reconocidas:
            <code>titulo</code>, <code>orden</code>, <code>destacado</code>, <code>visible</code>,
            para Categorías también <code>familia</code> y <code>rubro</code>; para Colores <code>codigo_color</code>).
        </p>
        <form action="{{ route('admin.imports.baja-rotacion') }}" method="POST" enctype="multipart/form-data"
              onsubmit="return confirm('Esto va a reemplazar Rubros, Familias, Categorías y Colores. ¿Continuar?')">
            @csrf
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <input type="file" name="archivo" accept=".xls,.xlsx" required
                       class="flex-1 text-sm border border-gray-300 rounded-lg p-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Cargar tablas asociadas
                </button>
            </div>
        </form>
    </section>

    {{-- ===== Historial ===== --}}
    <section class="bg-white rounded-lg shadow p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Últimas cargas</h2>
        @if($logs->isEmpty())
            <p class="text-sm text-gray-500">Todavía no se ejecutó ninguna carga.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-2">Fecha</th>
                            <th class="px-3 py-2">Proceso</th>
                            <th class="px-3 py-2">Archivo</th>
                            <th class="px-3 py-2">Filas</th>
                            <th class="px-3 py-2">Estado</th>
                            <th class="px-3 py-2">Mensaje</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($logs as $l)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-600">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $l->proceso }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $l->archivo ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $l->filas_procesadas }}</td>
                                <td class="px-3 py-2">
                                    @if($l->estado === 'ok')
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">OK</span>
                                    @elseif($l->estado === 'error')
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">ERROR</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ strtoupper($l->estado) }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-gray-600">{{ $l->mensaje }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

</div>
@endsection
