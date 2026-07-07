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
use App\Livewire\Vistas\Productos\ProductosTodotexPage;
use App\Livewire\Zona\ProductosZona;
use App\Livewire\Zona\CarritoZona;
use App\Livewire\Vistas\Productos\ProductoDetalle;
use App\Http\Controllers\Usuarios\UsuariosController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Clientes\ClienteController;
use App\Http\Controllers\Auth\ClienteAuthController;
use App\Http\Controllers\Auth\VendedorAuthController;
use App\Http\Controllers\Auth\B2BPasswordController;
use App\Http\Controllers\Vendedores\VendedorController;
use App\Http\Controllers\Carrito\CarritoConfigController;
use App\Http\Controllers\Cliente\PagoController;
use App\Livewire\Zona\FormularioPago;
use App\Livewire\Zona\Precios;
use App\Http\Controllers\Cliente\CarritoController;
use App\Http\Controllers\Admin\Newsletter\NewsletterCrud;
use App\Http\Controllers\Metadata\MetadataCrud;
use App\Http\Controllers\Categorias\CategoriaPadreController;
use App\Http\Controllers\Familias\FamiliaController;
use App\Http\Controllers\Rubros\RubroController;
use App\Http\Controllers\Colores\ColorTodotexController;
use App\Http\Controllers\Categorias\CategoriaTodotexController;
use App\Http\Controllers\Productos\ProductoTodotexController;
use App\Http\Controllers\Admin\ParametroController;
use App\Http\Controllers\Admin\BulkImportController;
use App\Http\Controllers\Admin\MapeoErpController;



Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/', Inicio::class)->name('home');
Route::get('/nosotros', NosotrosPage::class)->name('nosotros');
Route::get('/novedades', NovedadesPublic::class)->name('novedades.public');
Route::get('/novedades/{id}', NovedadDetalle::class)->name('novedad.detalle');
Route::get('/contacto', ContactPage::class)->name('contacto');
Route::get('/productos', ProductosTodotexPage::class)->name('productos');
Route::get('/productos/detalle/{id}', ProductoDetalle::class)->name('productos.detalle');
Route::get('/productos/{categoriaPadre}', ProductosPage::class)->name('productos.categoria');
Route::get('/catalogo/{id}', ProductosTodotexPage::class)->name('productos-todotex.detalle');

Route::prefix('zona-privada')->name('cliente.')->middleware('mantenimiento')->group(function () {
    Route::get('/login', [ClienteAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClienteAuthController::class, 'login'])->name('login.post');

    Route::get('/vendedor/login', [VendedorAuthController::class, 'showLoginForm'])->name('vendedor.login');
    Route::post('/vendedor/login', [VendedorAuthController::class, 'login'])->name('vendedor.login.post');
    Route::post('/vendedor/logout', [VendedorAuthController::class, 'logout'])->name('vendedor.logout');

    Route::get('/password/recuperar', [B2BPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/password/recuperar', [B2BPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password/restablecer', [B2BPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/password/restablecer', [B2BPasswordController::class, 'reset'])->name('password.update');
    Route::get('/password/inicial', [B2BPasswordController::class, 'showInicialForm'])->name('password.inicial');
    Route::post('/password/inicial', [B2BPasswordController::class, 'storeInicial'])->name('password.inicial.store');

    Route::middleware('auth.zona')->group(function () {
    Route::get('/productos', ProductosZona::class)->name('productos');
    Route::get('/carrito', CarritoZona::class)->name('carrito');
    Route::post('/carrito/realizar-pedido', [CarritoController::class, 'realizarPedido'])->name('carrito.realizar-pedido'); // 👈 NUEVA RUTA
    
    Route::get('/pedidos', \App\Livewire\Zona\MisPedidos::class)->name('pedidos');
    Route::get('/pedidos/{id}', \App\Livewire\Zona\DetallePedido::class)->name('pedidos.detalle');
    Route::get('/pedidos/{id}/descargar', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'descargar'])->name('pedidos.descargar');
    
    Route::get('/informar-pago', FormularioPago::class)->name('informar-pago');
    Route::post('/informar-pago/enviar', [PagoController::class, 'enviar'])->name('pago.enviar');
    
    Route::get('/precios', Precios::class)->name('precios');
    Route::get('/cambiar-password', \App\Livewire\Zona\CambiarPassword::class)->name('cambiar-password');

    Route::post('/logout', [ClienteAuthController::class, 'logout'])->name('logout');
    });
});
Route::middleware(['auth', 'is_admin'])->group(function () {

    Route::get('/admin', function () {
        return redirect()->route('sliders.index');
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
    Route::get('/{precio}/edit', [PrecioController::class, 'edit'])->name('precios.edit');
    Route::post('/', [PrecioController::class, 'store'])->name('precios.store');
    Route::put('/{precio}', [PrecioController::class, 'update'])->name('precios.update');
    Route::post('/{precio}/activar', [PrecioController::class, 'activar'])->name('precios.activar');
    Route::delete('/{id}', [PrecioController::class, 'destroy'])->name('precios.destroy');
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
        Route::post('/{cliente}/reset-password', [ClienteController::class, 'resetPassword'])->name('clientes.reset-password');
    });

    Route::prefix('admin/vendedores')->group(function () {
        Route::get('/', [VendedorController::class, 'index'])->name('vendedores.index');
        Route::get('/create', [VendedorController::class, 'create'])->name('vendedores.create');
        Route::post('/', [VendedorController::class, 'store'])->name('vendedores.store');
        Route::get('/{vendedor}/edit', [VendedorController::class, 'edit'])->name('vendedores.edit');
        Route::put('/{vendedor}', [VendedorController::class, 'update'])->name('vendedores.update');
        Route::delete('/{vendedor}', [VendedorController::class, 'destroy'])->name('vendedores.destroy');
        Route::post('/{vendedor}/toggle-activo', [VendedorController::class, 'toggleActivo'])->name('vendedores.toggle-activo');
        Route::post('/{vendedor}/reset-password', [VendedorController::class, 'resetPassword'])->name('vendedores.reset-password');
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

    Route::resource('/admin/categorias-padre', CategoriaPadreController::class);

    Route::prefix('admin/familias')->group(function () {
        Route::get('/', [FamiliaController::class, 'index'])->name('familias.index');
        Route::get('/create', [FamiliaController::class, 'create'])->name('familias.create');
        Route::post('/', [FamiliaController::class, 'store'])->name('familias.store');
        Route::post('/banner-todos', [FamiliaController::class, 'uploadBannerTodos'])->name('familias.bannerTodos.upload');
        Route::delete('/banner-todos', [FamiliaController::class, 'deleteBannerTodos'])->name('familias.bannerTodos.delete');
        Route::get('/{id}/edit', [FamiliaController::class, 'edit'])->name('familias.edit');
        Route::post('/{id}', [FamiliaController::class, 'update'])->name('familias.update');
        Route::delete('/{id}/imagen', [FamiliaController::class, 'deleteImagen'])->name('familias.deleteImagen');
        Route::delete('/{id}', [FamiliaController::class, 'destroy'])->name('familias.destroy');
    });

    Route::prefix('admin/rubros')->group(function () {
        Route::get('/', [RubroController::class, 'index'])->name('rubros.index');
        Route::get('/create', [RubroController::class, 'create'])->name('rubros.create');
        Route::post('/', [RubroController::class, 'store'])->name('rubros.store');
        Route::get('/{id}/edit', [RubroController::class, 'edit'])->name('rubros.edit');
        Route::get('/{id}/orden', [RubroController::class, 'ordenProductos'])->name('rubros.orden');
        Route::post('/{id}/orden', [RubroController::class, 'guardarOrdenProductos'])->name('rubros.guardarOrden');
        Route::post('/{id}', [RubroController::class, 'update'])->name('rubros.update');
        Route::delete('/{id}/imagen', [RubroController::class, 'deleteImagen'])->name('rubros.deleteImagen');
        Route::delete('/{id}', [RubroController::class, 'destroy'])->name('rubros.destroy');
    });

    Route::prefix('admin/categorias-todotex')->group(function () {
        Route::get('/', [CategoriaTodotexController::class, 'index'])->name('categorias-todotex.index');
        Route::get('/create', [CategoriaTodotexController::class, 'create'])->name('categorias-todotex.create');
        Route::post('/', [CategoriaTodotexController::class, 'store'])->name('categorias-todotex.store');
        Route::get('/fusionar', [CategoriaTodotexController::class, 'fusionar'])->name('categorias-todotex.fusionar');
        Route::post('/fusionar', [CategoriaTodotexController::class, 'procesarFusion'])->name('categorias-todotex.procesarFusion');
        Route::get('/{id}/productos', [CategoriaTodotexController::class, 'productosCategoria'])->name('categorias-todotex.productos');
        Route::post('/{id}/revertir', [CategoriaTodotexController::class, 'revertirCategoria'])->name('categorias-todotex.revertir');
        Route::get('/{id}/orden', [CategoriaTodotexController::class, 'ordenProductos'])->name('categorias-todotex.orden');
        Route::post('/{id}/orden', [CategoriaTodotexController::class, 'guardarOrdenProductos'])->name('categorias-todotex.guardarOrden');
        Route::get('/{id}/edit', [CategoriaTodotexController::class, 'edit'])->name('categorias-todotex.edit');
        Route::post('/{id}', [CategoriaTodotexController::class, 'update'])->name('categorias-todotex.update');
        Route::delete('/{id}/imagen', [CategoriaTodotexController::class, 'deleteImagen'])->name('categorias-todotex.deleteImagen');
        Route::delete('/{id}', [CategoriaTodotexController::class, 'destroy'])->name('categorias-todotex.destroy');
        Route::patch('/{id}/toggle-visible', [CategoriaTodotexController::class, 'toggleVisible'])->name('categorias-todotex.toggle-visible');
    });

    Route::prefix('admin/productos-todotex')->group(function () {
        Route::get('/', [ProductoTodotexController::class, 'index'])->name('productos-todotex.index');
        Route::get('/create', [ProductoTodotexController::class, 'create'])->name('productos-todotex.create');
        Route::post('/', [ProductoTodotexController::class, 'store'])->name('productos-todotex.store');
        Route::get('/{id}/edit', [ProductoTodotexController::class, 'edit'])->name('productos-todotex.edit');
        Route::post('/{id}', [ProductoTodotexController::class, 'update'])->name('productos-todotex.update');
        Route::delete('/{id}', [ProductoTodotexController::class, 'destroy'])->name('productos-todotex.destroy');
        Route::patch('/{id}/toggle-visible', [ProductoTodotexController::class, 'toggleVisible'])->name('productos-todotex.toggle-visible');
        Route::delete('/{id}/gallery/{imageId}', [ProductoTodotexController::class, 'destroyGalleryImage'])->name('productos-todotex.gallery.destroy');
    });

    Route::get('/admin/parametros', [ParametroController::class, 'index'])->name('admin.parametros');
    Route::post('/admin/parametros', [ParametroController::class, 'save'])->name('admin.parametros.save');

    Route::prefix('admin/imports')->name('admin.imports.')->group(function () {
        Route::get('/', [BulkImportController::class, 'index'])->name('index');
        Route::post('/clientes', [BulkImportController::class, 'importClientes'])->name('clientes');
        Route::post('/productos', [BulkImportController::class, 'importProductos'])->name('productos');
        Route::post('/baja-rotacion', [BulkImportController::class, 'importBajaRotacion'])->name('baja-rotacion');
        Route::post('/erp-zip', [BulkImportController::class, 'procesarErpZip'])->name('erp-zip');
    });

    Route::prefix('admin/mapeos-erp')->name('admin.mapeos-erp.')->group(function () {
        Route::get('/', [MapeoErpController::class, 'index'])->name('index');
        Route::post('/', [MapeoErpController::class, 'save'])->name('save');
        Route::post('/aplicar', [MapeoErpController::class, 'aplicar'])->name('aplicar');
        Route::get('/productos', [MapeoErpController::class, 'productos'])->name('productos');
        Route::post('/familia-b2b', [MapeoErpController::class, 'crearFamiliaB2B'])->name('crear-familia');
        Route::post('/categoria-b2b', [MapeoErpController::class, 'crearCategoriaB2B'])->name('crear-categoria');
    });

    Route::prefix('admin/colores-todotex')->group(function () {
        Route::get('/', [ColorTodotexController::class, 'index'])->name('colores-todotex.index');
        Route::get('/create', [ColorTodotexController::class, 'create'])->name('colores-todotex.create');
        Route::post('/', [ColorTodotexController::class, 'store'])->name('colores-todotex.store');
        Route::get('/{id}/edit', [ColorTodotexController::class, 'edit'])->name('colores-todotex.edit');
        Route::post('/{id}', [ColorTodotexController::class, 'update'])->name('colores-todotex.update');
        Route::delete('/{id}', [ColorTodotexController::class, 'destroy'])->name('colores-todotex.destroy');
    });

}); // end middleware auth + is_admin
