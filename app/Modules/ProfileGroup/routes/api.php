<?php

use Illuminate\Support\Facades\Route;
use \App\Modules\ProfileGroup\Http\Controllers\ProfileGroupController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api/profilegroup'

], function ($router) {
    Route::get('/', [ProfileGroupController::class, 'index']);
    Route::get('/{id}', [ProfileGroupController::class, 'get']);
    Route::get('/getProfileGroupUsers/{id}', [ProfileGroupController::class, 'getProfileGroupUsers']);
    Route::get('/getProfileGroupEquipments/{id}', [ProfileGroupController::class, 'getProfileGroupEquipments']);
    Route::get('/getProfileGroupDamageTypes/{id}', [ProfileGroupController::class, 'getProfileGroupDamageTypes']);
    Route::post('/create', [ProfileGroupController::class, 'create']);
    Route::post('/update', [ProfileGroupController::class, 'update']);
    Route::post('/delete', [ProfileGroupController::class, 'delete']);
    Route::post('/addUserToProfileGroup', [ProfileGroupController::class, 'addUserToProfileGroup']);

});
