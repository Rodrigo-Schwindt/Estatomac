<?php

use App\Http\Controllers\Api\ErpPedidoController;
use Illuminate\Support\Facades\Route;

Route::prefix('erp')->middleware('erp.api_key')->group(function () {
    Route::get('/health', [ErpPedidoController::class, 'health']);
    Route::get('/pedidos', [ErpPedidoController::class, 'index']);
    Route::get('/pedidos/pendientes', [ErpPedidoController::class, 'pendientes']);
    Route::get('/pedidos/{numero}', [ErpPedidoController::class, 'show'])->where('numero', '[0-9]+');
});
