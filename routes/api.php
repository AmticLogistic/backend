<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UtilsController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DocumentController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'auth'], function () {
    Route::post('signIn', [LoginController::class, 'signIn']);
    Route::post('changePassword', [LoginController::class, 'changePassword']);
});


Route::group(['middleware' => 'jwt.auth'], function () {
});

Route::group(['prefix' => 'almacen'], function () {

    Route::get('listCategorias', [UtilsController::class, 'listCategorias']);
    Route::post('saveCategorias', [UtilsController::class, 'saveCategorias']);
    Route::post('editCategorias', [UtilsController::class, 'editCategorias']);

    Route::get('listMarcas', [UtilsController::class, 'listMarcas']);
    Route::post('saveMarcas', [UtilsController::class, 'saveMarcas']);
    Route::post('editMarcas', [UtilsController::class, 'editMarcas']);

    Route::get('getPersons', [UtilsController::class, 'getPersons']);


    Route::get('listMateriales', [UtilsController::class, 'listMateriales']);
    Route::get('oneMaterial/{id}', [UtilsController::class, 'oneMaterial']);
    Route::get('deleteMaterial/{id}', [UtilsController::class, 'deleteMaterial']);
    Route::get('disponible/{id}', [UtilsController::class, 'getMaterialDisponible']);

    Route::get('utils', [UtilsController::class, 'utils']);
    Route::post('saveMaterial', [UtilsController::class, 'saveMaterial']);
    Route::get('listServicios', [UtilsController::class, 'listServicios']);
});

Route::group(['prefix' => 'utils'], function () {
    Route::get('getPersons', [UtilsController::class, 'getPersons']);
    Route::get('getProviders', [UtilsController::class, 'getProviders']);
    Route::get('oneProviders/{id}', [UtilsController::class, 'oneProviders']);
    Route::post('saveProviders', [UtilsController::class, 'saveProviders']);
    Route::get('deleteProvider/{id}', [UtilsController::class, 'deleteProvider']);
    Route::get('getTransport', [UtilsController::class, 'getTransport']);
    Route::post('saveTransports', [UtilsController::class, 'saveTransports']);

    Route::get('getTransports', [UtilsController::class, 'getTransports']);
    Route::get('getOrderOne/{id}', [UtilsController::class, 'getOrderOne']);
    Route::get('getPersonas', [UtilsController::class, 'getPersonas']);
    Route::get('getCCostos', [UtilsController::class, 'getCCostos']);
    Route::post('saveCCostos', [UtilsController::class, 'saveCCostos']);
    Route::post('deleteCCostos', [UtilsController::class, 'deleteCCostos']);

    Route::get('onePersonal/{id}', [UtilsController::class, 'onePersonal']);
    Route::post('savePersonal', [UtilsController::class, 'savePersonal']);
    Route::get('deletePersonal/{id}', [UtilsController::class, 'deletePersonal']);

    Route::get('dashboard', [UtilsController::class, 'dashboard']);
});

Route::group(['prefix' => 'process'], function () {

    Route::post('saveRequerimiento', [ProcessController::class, 'saveRequerimiento']);
    Route::get('listRequerimientos/{init}/{end}', [ProcessController::class, 'listRequerimientos']);
    Route::get('deleteRequerimiento/{id}', [ProcessController::class, 'deleteRequerimiento']);
    Route::post('saveSolicitud', [ProcessController::class, 'saveSolicitud']);

    Route::post('saveInventario', [ProcessController::class, 'saveInventory']);
    Route::get('listInventario/{init}/{end}', [ProcessController::class, 'listInventory']);
    Route::get('deleteInventory/{id}', [ProcessController::class, 'deleteInventory']);


    Route::post('saveOrden', [ProcessController::class, 'saveOrden']);
    Route::get('listOrden/{init}/{end}', [ProcessController::class, 'listOrden']);

    Route::post('saveInput', [ProcessController::class, 'saveInput']);
    Route::get('obtenerEntradas/{init}/{end}', [ProcessController::class, 'obtenerEntradas']);
    Route::get('deleteInput/{id}', [ProcessController::class, 'deleteInput']);

    Route::post('saveOutput', [ProcessController::class, 'saveOutput']);
    Route::get('listOutput/{init}/{end}', [ProcessController::class, 'listOutput']);
    Route::get('deleteOutput/{id}', [ProcessController::class, 'deleteOutput']);
});

Route::group(['prefix' => 'report'], function () {
    Route::get('inventory/{init}/{end}', [ReportsController::class, 'getReportInventory']);
    Route::get('kardex/{init}/{end}', [ReportsController::class, 'getReportKardex']);
    Route::get('ordenes/{id}/{init}/{end}', [ReportsController::class, 'getReportOrdenes']);


    Route::get('inventoryPdf/{init}/{end}', [ReportsController::class, 'getReportInventoryPdf']);
    Route::get('kardexPdf/{init}/{end}', [ReportsController::class, 'getReportKardexPdf']);
    Route::get('ordenesCompraPdf/{id}/{init}/{end}', [ReportsController::class, 'getReportOrdenesPDF']);
});

Route::group(['prefix' => 'document'], function () {
    Route::get('required/{id}', [DocumentController::class, 'getRequired']);
    Route::get('solicitude/{id}', [DocumentController::class, 'getSolicitude']);
    Route::get('orderbuy/{id}', [DocumentController::class, 'getOrderBuy']);
    Route::get('input/{id}', [DocumentController::class, 'getInput']);
    Route::get('output/{id}', [DocumentController::class, 'getOutput']);

    Route::get('getRequiredEdit/{id}', [DocumentController::class, 'getRequiredEdit']);
    Route::get('getOrderBuyEdit/{id}', [DocumentController::class, 'getOrderBuyEdit']);
});

Route::group(['prefix' => 'users'], function () {

    Route::get('listUsers', [LoginController::class, 'listUsers']);
    Route::post('createUser', [LoginController::class, 'createUser']);
    Route::post('editUser', [LoginController::class, 'editUser']);
    Route::get('listUtils', [LoginController::class, 'listUtils']);
    Route::get('listRoles', [LoginController::class, 'listRoles']);
    Route::post('saveRoles', [LoginController::class, 'saveRoles']);
});
