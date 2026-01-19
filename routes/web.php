<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sliders\SlidersController;
use App\Http\Controllers\Sliders\SlidersCreate;
use App\Http\Controllers\Sliders\SlidersEdit;
use App\Http\Controllers\Contact\ContactManager;
use App\Livewire\Vistas\Home\Inicio;
use App\Http\Controllers\Nosotros\NosotrosAdmin;
use App\Http\Controllers\Nosotros\NosotrosHomeAdmin;
use App\Livewire\Vistas\Nosotros\NosotrosPage;
use App\Http\Controllers\Novedades\NovCategoriesIndex;
use App\Http\Controllers\Novedades\NovedadesIndex;
use App\Http\Controllers\Novedades\NovedadesCreate;
use App\Http\Controllers\Novedades\NovedadesEdit;
use App\Http\Controllers\Novedades\NovCategoriesCreate;
use App\Http\Controllers\Novedades\NovCategoriesEdit;
use App\Livewire\Vistas\Novedades\NovedadesPublic;
use App\Livewire\Vistas\Novedades\NovedadDetalle;
use App\Livewire\Vistas\Contact\ContactPage;
use App\Http\Controllers\Categorias\CategoriaController;
use App\Http\Controllers\Marcas\MarcaController;
use App\Http\Controllers\Productos\ProductoController;
use App\Http\Controllers\Precio\PrecioController;
use App\Http\Controllers\Equivalencias\EquivalenciaController;
use App\Livewire\Vistas\Productos\ProductosPage;
use App\Livewire\Zona\ProductosZona;
use App\Livewire\Zona\CarritoZona;
use App\Livewire\Vistas\Productos\ProductoDetalle;
use App\Http\Controllers\Usuarios\UsuariosController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Clientes\ClienteController;
use App\Http\Controllers\Auth\ClienteAuthController;
use App\Http\Controllers\Carrito\CarritoConfigController;
use App\Http\Controllers\Cliente\PagoController;
use App\Livewire\Zona\FormularioPago;
use App\Livewire\Zona\Precios;
use App\Http\Controllers\Cliente\CarritoController;
use App\Http\Controllers\Admin\Newsletter\NewsletterCrud;
use App\Http\Controllers\Metadata\MetadataCrud;
use App\Http\Controllers\PedidoController;



Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/', Inicio::class)->name('home');
Route::get('/nosotros', NosotrosPage::class)->name('nosotros');
Route::get('/novedades', NovedadesPublic::class)->name('novedades.public');
Route::get('/novedades/{id}', NovedadDetalle::class)->name('novedad.detalle');
Route::get('/contacto', ContactPage::class)->name('contacto');
Route::get('/productos', ProductosPage::class)->name('productos');
Route::get('/productos/{id}', ProductoDetalle::class)->name('productos.detalle');

Route::prefix('zona-privada')->name('cliente.')->group(function () {
    Route::get('/login', [ClienteAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClienteAuthController::class, 'login'])->name('login.post');
    
    Route::middleware('auth:cliente')->group(function () {
        Route::get('/productos', ProductosZona::class)->name('productos');
        Route::get('/carrito', CarritoZona::class)->name('carrito');
        Route::post('/carrito/realizar-pedido', [CarritoController::class, 'realizarPedido'])->name('carrito.realizar-pedido'); // 👈 NUEVA RUTA
        
        Route::get('/pedidos', \App\Livewire\Zona\MisPedidos::class)->name('pedidos');
        Route::get('/pedidos/{id}', \App\Livewire\Zona\DetallePedido::class)->name('pedidos.detalle');
        Route::get('/pedidos/{id}/descargar', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'descargar'])->name('pedidos.descargar');
        
        Route::get('/informar-pago', FormularioPago::class)->name('informar-pago');
        Route::post('/informar-pago/enviar', [PagoController::class, 'enviar'])->name('pago.enviar');
        
        Route::get('/precios', Precios::class)->name('precios');
        Route::get('/pedidos/{pedido}/descargar', [PedidoController::class, 'descargar'])->name('cliente.pedidos.descargar');
        
        Route::post('/logout', [ClienteAuthController::class, 'logout'])->name('logout');
    });
});
Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin', function () {
        return redirect()->route('productos.index');
    })->name('admin.dashboard');

    Route::prefix('admin/sliders')->group(function () {
        Route::get('/', [SlidersController::class, 'index'])->name('sliders.index');
        Route::get('/create', [SlidersCreate::class, 'index'])->name('sliders.create');
        Route::post('/', [SlidersCreate::class, 'store'])->name('sliders.store');
        Route::get('/{id}/edit', [SlidersEdit::class, 'index'])->name('sliders.edit');
        Route::post('/{id}', [SlidersEdit::class, 'update'])->name('sliders.update');
        Route::delete('/{id}', [SlidersController::class, 'destroy'])->name('sliders.destroy');
    });

    Route::get('/admin/contacto', [ContactManager::class, 'index'])->name('admin.contacto');
    Route::post('/admin/contacto', [ContactManager::class, 'save'])->name('admin.contacto.save');

    Route::get('admin/nosotros', [NosotrosAdmin::class, 'index'])->name('nosotros.index');
    Route::post('admin/nosotros', [NosotrosAdmin::class, 'save'])->name('nosotros.save');
    Route::delete('admin/nosotros/image/{field}', [NosotrosAdmin::class, 'deleteImage'])->name('nosotros.image.delete');

    Route::get('admin/nosotros/home', [NosotrosHomeAdmin::class, 'index'])->name('nosotros.home.index');
    Route::post('admin/nosotros/home', [NosotrosHomeAdmin::class, 'save'])->name('nosotros.home.save');
    Route::delete('admin/nosotros/home/image', [NosotrosHomeAdmin::class, 'deleteImage'])->name('nosotros.home.image.delete');

    Route::get('/admin/novcategorias', [NovCategoriesIndex::class, 'index'])->name('novcategories.index');
    Route::get('/admin/novcategorias/create', [NovCategoriesCreate::class, 'create'])->name('novcategories.create');
    Route::post('/admin/novcategorias/store', [NovCategoriesCreate::class, 'store'])->name('novcategories.store');
    Route::get('/admin/novcategorias/{id}/edit', [NovCategoriesEdit::class, 'edit'])->name('novcategories.edit');
    Route::post('/admin/novcategorias/{id}/update', [NovCategoriesEdit::class, 'update'])->name('novcategories.update');
    Route::delete('/admin/novcategorias/{id}', [NovCategoriesIndex::class, 'delete'])->name('novcategories.delete');

    Route::get('/admin/novedades', [NovedadesIndex::class, 'index'])->name('novedades.index');
    Route::get('/admin/novedades/create', [NovedadesCreate::class, 'create'])->name('novedades.create');
    Route::post('/admin/novedades', [NovedadesCreate::class, 'store'])->name('novedades.store');
    Route::post('/admin/novedades/banner', [NovedadesIndex::class, 'saveBanner'])->name('novedades.banner.save');
    Route::delete('/admin/novedades/banner', [NovedadesIndex::class, 'removeBanner'])->name('novedades.banner.remove');
    Route::delete('/admin/novedades/{id}', [NovedadesIndex::class, 'delete'])->name('novedades.delete');
    Route::post('/admin/novedades/{id}/destacado', [NovedadesIndex::class, 'toggleDestacado'])->name('novedades.toggle');
    Route::get('/admin/novedades/{id}/edit', [NovedadesEdit::class, 'edit'])->name('novedades.edit');
    Route::put('/admin/novedades/{id}', [NovedadesEdit::class, 'update'])->name('novedades.update');

    Route::prefix('admin/categorias')->group(function () {
        Route::get('/', [CategoriaController::class, 'index'])->name('categorias.index');
        Route::get('/create', [CategoriaController::class, 'create'])->name('categorias.create');
        Route::post('/', [CategoriaController::class, 'store'])->name('categorias.store');
        Route::get('/{categoria}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
        Route::put('/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
        Route::delete('/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
        Route::post('/banner', [CategoriaController::class, 'updateBanner'])->name('categorias.banner.update');
        Route::delete('/banner', [CategoriaController::class, 'deleteBannerImage'])->name('categorias.banner.delete');
    });

    Route::prefix('admin/marcas')->group(function () {
        Route::get('/', [MarcaController::class, 'index'])->name('marcas.index');
        Route::get('/create', [MarcaController::class, 'create'])->name('marcas.create');
        Route::post('/', [MarcaController::class, 'store'])->name('marcas.store');
        Route::get('/{marca}/edit', [MarcaController::class, 'edit'])->name('marcas.edit');
        Route::put('/{marca}', [MarcaController::class, 'update'])->name('marcas.update');
        Route::delete('/{marca}', [MarcaController::class, 'destroy'])->name('marcas.destroy');
    });

    Route::prefix('admin/precios')->group(function () {
        Route::get('/', [PrecioController::class, 'index'])->name('precios.index');
        Route::get('/create', [PrecioController::class, 'create'])->name('precios.create');
        Route::get('/edit', [PrecioController::class, 'edit'])->name('precios.edit');
        Route::post('/', [PrecioController::class, 'store'])->name('precios.store');
        Route::delete('/{id}', [PrecioController::class, 'destroy'])->name('precios.destroy');
    });

    Route::prefix('admin/productos')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        Route::get('/modelos/{marca}', [ProductoController::class, 'getModelos'])->name('productos.modelos');
        Route::get('/subcategorias/{categoria}', [ProductoController::class, 'getSubcategorias'])->name('productos.subcategorias');

        Route::get('/importar', [\App\Http\Controllers\Productos\ProductoImportController::class, 'showImportForm'])->name('productos.import.form');
    Route::post('/importar', [\App\Http\Controllers\Productos\ProductoImportController::class, 'import'])->name('productos.import');
    Route::get('/plantilla', [\App\Http\Controllers\Productos\ProductoImportController::class, 'downloadTemplate'])->name('productos.template');
    });

    Route::prefix('admin/equivalencias')->group(function () {
        Route::get('/', [EquivalenciaController::class, 'index'])->name('equivalencias.index');
        Route::get('/create', [EquivalenciaController::class, 'create'])->name('equivalencias.create');
        Route::post('/', [EquivalenciaController::class, 'store'])->name('equivalencias.store');
        Route::get('/{equivalencia}/edit', [EquivalenciaController::class, 'edit'])->name('equivalencias.edit');
        Route::put('/{equivalencia}', [EquivalenciaController::class, 'update'])->name('equivalencias.update');
        Route::delete('/{equivalencia}', [EquivalenciaController::class, 'destroy'])->name('equivalencias.destroy');
    });

    Route::prefix('admin/usuarios')->group(function () {
        Route::get('/', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/create', [UsuariosController::class, 'create'])->name('usuarios.create');
        Route::post('/', [UsuariosController::class, 'store'])->name('usuarios.store');
        Route::get('/{id}/edit', [UsuariosController::class, 'edit'])->name('usuarios.edit');
        Route::put('/{id}', [UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/{id}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
    });

    Route::prefix('admin/clientes')->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::post('/{cliente}/toggle-activo', [ClienteController::class, 'toggleActivo'])->name('clientes.toggle-activo');
    });

    Route::prefix('admin/carrito')->group(function () {
        Route::get('/config', [CarritoConfigController::class, 'index'])->name('carrito.config.index');
        Route::post('/config', [CarritoConfigController::class, 'save'])->name('carrito.config.save');
    });

    Route::prefix('admin/pedidos')->name('admin.pedidos.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PedidoController::class, 'index'])->name('index');
        Route::get('/{pedido}', [\App\Http\Controllers\Admin\PedidoController::class, 'show'])->name('show');
        Route::put('/{pedido}/fecha-entrega', [\App\Http\Controllers\Admin\PedidoController::class, 'updateFechaEntrega'])->name('updateFechaEntrega');
        Route::put('/{pedido}/toggle-entregado', [\App\Http\Controllers\Admin\PedidoController::class, 'toggleEntregado'])->name('toggleEntregado');
        Route::put('/{pedido}/toggle-descarga', [\App\Http\Controllers\Admin\PedidoController::class, 'toggleDescargaHabilitada'])->name('toggleDescarga');
        Route::get('/{pedido}/factura', [\App\Http\Controllers\Admin\FacturaController::class, 'generar'])->name('factura');
    });
    Route::controller(NewsletterCrud::class)->group(function () {
        Route::get('/admin/newsletter', 'index')->name('admin.newsletter');
        Route::post('/admin/newsletter/send', 'send')->name('admin.newsletter.send');
        Route::post('/admin/newsletter/{id}/toggle', 'toggleActive')->name('admin.newsletter.toggle');
        Route::delete('/admin/newsletter/{id}', 'deleteSubscriber')->name('admin.newsletter.delete');
    });

        Route::get('/admin/metadata', [MetadataCrud::class, 'index'])->name('admin.metadata');
    Route::post('/admin/metadata', [MetadataCrud::class, 'save'])->name('admin.metadata.save');
    Route::delete('/admin/metadata/{id}', [MetadataCrud::class, 'delete'])->name('admin.metadata.delete');
    
});