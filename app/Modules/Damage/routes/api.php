<?php

use App\Modules\Damage\Http\Controllers\DamageController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api/damages'

], function ($router) {

    Route::get('/', [DamageController::class, 'index']);
    Route::get('/getDamagesByProfileGroup/{id}', [DamageController::class, 'getDamagesByProfileGroup']);
    Route::get('/getDamagesByEquipments/{id}', [DamageController::class, 'getDamagesByEquipments']);
    Route::get('/getDamagesByDeclareds/{id}', [DamageController::class, 'getDamagesByDeclareds']);
    Route::get('/getDamagesByConfirmeds/{id}', [DamageController::class, 'getDamagesByConfirmeds']);
    Route::get('/getDamagesByCloseds/{id}', [DamageController::class, 'getDamagesByCloseds']);
    Route::get('/getDamagesByReverteds/{id}', [DamageController::class, 'getDamagesByReverteds']);
    Route::get('/getEquipmentDamagesMergedWithDamageTypes/{id}', [DamageController::class, 'getEquipmentDamagesMergedWithDamageTypes']);
    Route::post('/declareDamage', [DamageController::class, 'declareDamage']);
    Route::post('/confirmDamage', [DamageController::class, 'confirmDamage']);
    Route::post('/closeDamage', [DamageController::class, 'closeDamage']);
    Route::post('/revertDamage', [DamageController::class, 'revertDamage']);
    Route::post('/foremanIntervention', [DamageController::class, 'foremanIntervention']);
    Route::post('/sendDamagePhotosStoragePath', [DamageController::class, 'sendDamagePhotosStoragePath']);
    Route::post('/delete', [DamageController::class, 'delete']);


});
