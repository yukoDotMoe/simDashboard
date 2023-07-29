<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


require __DIR__.'/auth.php';
//Route::get('/register', function () {
//    return redirect('/');
//});

Route::group([
    'middleware' => ['auth', 'verified', 'user'],
], function () {
    Route::get('/dashboard', [\App\Http\Controllers\UsersController::class, 'dashboardView'])->name('dashboard');
    Route::post('/dashboard/filter', [\App\Http\Controllers\UsersController::class, 'dashboardFilter'])->name('dashboard.filter');

    Route::get('/balance', [\App\Http\Controllers\UsersController::class, 'balanceView'])->name('balance');
    Route::post('/balance/filter', [\App\Http\Controllers\UsersController::class, 'balanceFilter'])->name('balance.filter');

    Route::get('/basicRent', [\App\Http\Controllers\SimController::class, 'rentView'])->name('basicRent');
    Route::get('/customRent', [\App\Http\Controllers\SimController::class, 'customRentView'])->name('customRent');
    Route::post('/rent', [\App\Http\Controllers\SimController::class, 'userRent'])->name('rentFunc');

    Route::get('/rentHistory', [\App\Http\Controllers\SimController::class, 'rentHistoryView'])->name('rentHistory');
    Route::post('/rentHistory/filter', [\App\Http\Controllers\UsersController::class, 'requestsFilter'])->name('rentHistory.filter');

    Route::get('/apiDocs', [\App\Http\Controllers\UsersController::class, 'apiDoc'])->name('apiDocs');
    Route::post('/resetToken', [\App\Http\Controllers\UsersController::class, 'resetToken'])->name('resetToken');

    Route::get('/accounts', [\App\Http\Controllers\UsersController::class, 'accountView'])->name('accounts');
    Route::post('/changePassword', [\App\Http\Controllers\UsersController::class, 'changePassword'])->name('accounts.changePass');

    Route::group([
        'middleware' => ['admin']
    ], function (){
        Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'adminUsersView'])->name('admin.users');

        Route::get('/admin/vendors', [\App\Http\Controllers\AdminController::class, 'adminVendorsView'])->name('admin.vendors');
        Route::get('/admin/vendors/create', [\App\Http\Controllers\AdminController::class, 'vendorCreate'])->name('admin.vendors.create');
        Route::post('/admin/vendors/create', [\App\Http\Controllers\AdminController::class, 'vendorCreatePost'])->name('admin.vendors.create.post');


        Route::post('/admin/users/{id}', [\App\Http\Controllers\AdminController::class, 'getUser'])->name('admin.user');
        Route::post('/admin/balance/edit', [\App\Http\Controllers\AdminController::class, 'editBal'])->name('admin.userBalance');
        Route::post('/admin/user/edit', [\App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.userEdit');
        Route::post('/admin/user/veryBadUserUpdate', [\App\Http\Controllers\AdminController::class, 'veryBadUserUpdate'])->name('admin.veryBadUserUpdate');

        Route::get('/admin/users/history/{id}', [\App\Http\Controllers\AdminController::class, 'getHistory'])->name('admin.user.history');
        Route::post('/admin/users/post/history', [\App\Http\Controllers\AdminController::class, 'getHistoryPost'])->name('admin.user.historyPost');


        Route::get('/admin/sims', [\App\Http\Controllers\AdminController::class, 'adminSimsView'])->name('admin.sims');
        Route::post('/admin/sims/{id}', [\App\Http\Controllers\AdminController::class, 'getSim'])->name('admin.sim');
        Route::post('/admin/phone/{id}', [\App\Http\Controllers\AdminController::class, 'getSimByPhone'])->name('admin.simByPhone');
        Route::post('/admin/sim/edit', [\App\Http\Controllers\AdminController::class, 'updateSimInfo'])->name('admin.simEdit');
        Route::post('/admin/sim/removeLockedService', [\App\Http\Controllers\AdminController::class, 'removeLockedService'])->name('admin.removeLockedService');
        Route::post('/admin/sim/bulk', [\App\Http\Controllers\AdminController::class, 'bulkEdit'])->name('admin.bulkEdit');
        Route::post('/admin/object/ban', [\App\Http\Controllers\AdminController::class, 'ban'])->name('admin.ban');

        Route::get('/admin/services', [\App\Http\Controllers\AdminController::class, 'adminServicesView'])->name('admin.services');
        Route::get('/admin/create/service', [\App\Http\Controllers\AdminController::class, 'serviceCreate'])->name('admin.createService');
        Route::post('/admin/services/{id}', [\App\Http\Controllers\AdminController::class, 'getService'])->name('admin.service');
        Route::post('/admin/create/service', [\App\Http\Controllers\AdminController::class, 'createService'])->name('admin.createServicePost');
        Route::post('/admin/service/edit', [\App\Http\Controllers\AdminController::class, 'updateServicesInfo'])->name('admin.serviceEdit');

        Route::get('/admin/networks', [\App\Http\Controllers\AdminController::class, 'adminNetworksView'])->name('admin.networks');
        Route::get('/admin/create/network', [\App\Http\Controllers\AdminController::class, 'networkCreate'])->name('admin.createNetwork');
        Route::post('/admin/networks/{id}', [\App\Http\Controllers\AdminController::class, 'getNetwork'])->name('admin.service');
        Route::post('/admin/create/network', [\App\Http\Controllers\AdminController::class, 'createNetwork'])->name('admin.createNetworkPost');
        Route::post('/admin/network/edit', [\App\Http\Controllers\AdminController::class, 'updateNetworksInfo'])->name('admin.networkEdit');

        Route::get('/admin/dashboard', [\App\Http\Controllers\AdminController::class, 'adminDashboardView'])->name('admin.dashboard');
        Route::post('/admin/api/update', [\App\Http\Controllers\AdminController::class, 'handleApiChange'])->name('admin.apiUpdate');
        Route::post('/admin/filter', [\App\Http\Controllers\AdminController::class, 'dashboardFilter'])->name('admin.filter');
    });
});

Route::group([
    'middleware' => ['auth', 'verified', 'vendor'],
    'prefix' => 'vendor'
], function () {
    Route::get('/dashboard', [\App\Http\Controllers\VendorController::class, 'dashboard'])->name('vendor.dashboard');
    Route::get('/sims/{showOffline?}', [\App\Http\Controllers\VendorController::class, 'sims'])->name('vendor.sims');
    Route::post('/sims/{id}', [\App\Http\Controllers\VendorController::class, 'simsActivities'])->name('vendor.sims.activities');
    Route::post('/dashboard/filter', [\App\Http\Controllers\VendorController::class, 'dashboardFilter'])->name('vendor.dashboard.filter');
    Route::post('/sims/filter', [\App\Http\Controllers\VendorController::class, 'simsFilter'])->name('vendor.sims.filter');

    Route::post('/resetToken', [\App\Http\Controllers\UsersController::class, 'resetToken'])->name('vendor.resetToken');

    Route::get('/accounts', [\App\Http\Controllers\VendorController::class, 'accountView'])->name('vendor.accounts');
    Route::post('/changePassword', [\App\Http\Controllers\UsersController::class, 'changePassword'])->name('vendor.accounts.changePass');
    
    Route::get('/transactions', [\App\Http\Controllers\VendorController::class, 'transactionsView'])->name('vendor.transactions');
    Route::post('/transactions/filter', [\App\Http\Controllers\VendorController::class, 'transactionsFilter'])->name('vendor.transactions.filter');
});