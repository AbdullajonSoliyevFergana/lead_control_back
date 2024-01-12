<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\LeadController;

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

Route::middleware('api_admin')->group(function () {
    Route::controller(AdminController::class)->prefix('admin')->group(function () {
        Route::post('registration', 'registerAdmin');
        Route::post('login', 'loginAdmin')->withoutMiddleware('api_admin')->middleware("api_login_admin");
        Route::get('get', 'getAdmin');
        Route::post('list', 'getAdmins');
        Route::get('detail/{admin_id}', 'detailAdmin');
        Route::post('update/{admin_id}', 'updateAdmin');
        Route::get('delete/{admin_id}', 'deleteAdmin');
        Route::get('socket_id/{token}/{socket_id}', 'setSocket')->withoutMiddleware('api_admin');
    });

});

Route::middleware('api_admin')->controller(LeadController::class)->prefix('lead')->group(function () {
    Route::post('add', 'addLead')->withoutMiddleware('api_admin');
    Route::post('list', 'getLeads');
    Route::get('detail/{id}', 'detailLead');
    Route::post('change', 'changeLead');
});
