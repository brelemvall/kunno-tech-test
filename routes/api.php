<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Las 3 rutas están definidas. No cambies los métodos ni las URIs.
| Solo implementa los métodos del controlador.
*/

Route::get('/operations', [OperationController::class, 'index']);
Route::post('/operations', [OperationController::class, 'store']);
Route::post('/operations/{operation}/process', [OperationController::class, 'process']);
