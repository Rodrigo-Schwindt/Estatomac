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



Route::get('/', Inicio::class)->name('home');
Route::get('/nosotros', NosotrosPage::class)->name('nosotros');
Route::get('/novedades', NovedadesPublic::class)->name('novedades.public');
Route::get('/novedades/{id}', NovedadDetalle::class)->name('novedad.detalle');
Route::get('/contacto', ContactPage::class)->name('contacto');


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

Route::get('/admin/novcategorias', [NovCategoriesIndex::class, 'index'])
->name('novcategories.index');
Route::get('/admin/novcategorias/create', [NovCategoriesCreate::class, 'create'])
->name('novcategories.create');

Route::post('/admin/novcategorias/store', [NovCategoriesCreate::class, 'store'])
->name('novcategories.store');
Route::get('/admin/novcategorias/{id}/edit', [NovCategoriesEdit::class, 'edit'])
->name('novcategories.edit');

Route::post('/admin/novcategorias/{id}/update', [NovCategoriesEdit::class, 'update'])
->name('novcategories.update');

Route::delete('/admin/novcategorias/{id}', [NovCategoriesIndex::class, 'delete'])
->name('novcategories.delete');


Route::get('/admin/novedades', [NovedadesIndex::class, 'index'])->name('novedades.index');
Route::get('/admin/novedades/create', [NovedadesCreate::class, 'create'])->name('novedades.create');
Route::post('/admin/novedades', [NovedadesCreate::class, 'store'])->name('novedades.store');
Route::post('/admin/novedades/banner', [NovedadesIndex::class, 'saveBanner'])
    ->name('novedades.banner.save');

Route::delete('/admin/novedades/banner', [NovedadesIndex::class, 'removeBanner'])
    ->name('novedades.banner.remove');

Route::delete('/admin/novedades/{id}', [NovedadesIndex::class, 'delete'])
    ->name('novedades.delete');

Route::post('/admin/novedades/{id}/destacado', [NovedadesIndex::class, 'toggleDestacado'])
    ->name('novedades.toggle');

Route::get('/admin/novedades/{id}/edit', [NovedadesEdit::class, 'edit'])
    ->name('novedades.edit');

Route::put('/admin/novedades/{id}', [NovedadesEdit::class, 'update'])
    ->name('novedades.update');