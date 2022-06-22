<?php

use Illuminate\Support\Facades\Route;
use \App\Modules\Equipment\Http\Controllers\EquipmentController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api/equipments'

], function ($router) {
    Route::get('/', [EquipmentController::class, 'index']);
    Route::get('/getEquipmentsByCounter/{id}', [EquipmentController::class, 'getEquipmentsByCounter']);
    Route::get('/getEquipmentsByCounters/{id}', [EquipmentController::class, 'getEquipmentsByCounters']);
    Route::get('/{id}', [EquipmentController::class, 'get']);
    Route::post('/create', [EquipmentController::class, 'create']);
    Route::post('/update', [EquipmentController::class, 'update']);
    Route::post('/delete', [EquipmentController::class, 'delete']);
});
