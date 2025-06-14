<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\Admin\FoodCategoryController;
use App\Http\Controllers\Admin\FoodItemController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\VnpSandboxController;
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


Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/login', [DashboardController::class, 'indexLogin'])->name('auth.login.index');
    Route::post('/login', [DashboardController::class, 'login'])->name('auth.login');
    Route::post('/logout', [DashboardController::class, 'logout'])->name('auth.logout');

    Route::group(['prefix' => 'shops', 'as' => 'shops.'], function () {
        Route::get('/', [ShopController::class, 'index'])->name('index');
        Route::get('/list', [ShopController::class, 'getList'])->name('list');
        Route::get('change-status', [ShopController::class, 'changeStatus'])->name('change-status');
        Route::delete('delete', [ShopController::class, 'delete'])->name('delete');
        Route::post('save-data', [ShopController::class, 'save'])->name('save');
        Route::get('edit', [ShopController::class, 'edit'])->name('edit');
        Route::get('detail', [ShopController::class, 'detail'])->name('detail');
    });

    Route::group(['prefix' => 'food-categories', 'as' => 'food-categories.'], function () {
        Route::get('/', [FoodCategoryController::class, 'index'])->name('index');
        Route::get('/list', [FoodCategoryController::class, 'getList'])->name('list');
        Route::get('change-status', [FoodCategoryController::class, 'changeStatus'])->name('change-status');
        Route::delete('delete', [FoodCategoryController::class, 'delete'])->name('delete');
        Route::post('save-data', [FoodCategoryController::class, 'save'])->name('save');
        Route::get('edit', [FoodCategoryController::class, 'edit'])->name('edit');
        Route::get('detail', [FoodCategoryController::class, 'detail'])->name('detail');
    });

    Route::group(['prefix' => 'food-items', 'as' => 'food-items.'], function () {
        Route::get('/', [FoodItemController::class, 'index'])->name('index');
        Route::get('/list', [FoodItemController::class, 'getList'])->name('list');
        Route::get('change-status', [FoodItemController::class, 'changeStatus'])->name('change-status');
        Route::delete('delete', [FoodItemController::class, 'delete'])->name('delete');
        Route::post('save-data', [FoodItemController::class, 'save'])->name('save');
        Route::get('edit', [FoodItemController::class, 'edit'])->name('edit');
        Route::get('detail', [FoodItemController::class, 'detail'])->name('detail');
    });

    Route::group(['prefix' => 'floors', 'as' => 'floors.', 'middleware' => 'role-admin'], function () {
        Route::get('/', [FloorController::class, 'index'])->name('index');
        Route::get('/list', [FloorController::class, 'getList'])->name('list');
        Route::get('change-status', [FloorController::class, 'changeStatus'])->name('change-status');
        Route::delete('delete', [FloorController::class, 'delete'])->name('delete');
        Route::post('save-data', [FloorController::class, 'save'])->name('save');
        Route::get('detail', [FloorController::class, 'detail'])->name('detail');
    });

    Route::group(['prefix' => 'user-admins', 'as' => 'user-admins.'], function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/list', [AdminController::class, 'getList'])->name('list');
        Route::get('change-status', [AdminController::class, 'changeStatus'])->name('change-status');
        Route::delete('delete', [AdminController::class, 'delete'])->name('delete');
        Route::post('save-data', [AdminController::class, 'save'])->name('save');
        Route::get('edit', [AdminController::class, 'edit'])->name('edit');
    });

    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/list', [UserController::class, 'getList'])->name('list');
        Route::get('change-status', [UserController::class, 'changeStatus'])->name('change-status');
        Route::delete('delete', [UserController::class, 'delete'])->name('delete');
        Route::post('save-data', [UserController::class, 'save'])->name('save');
        Route::get('edit', [UserController::class, 'edit'])->name('edit');
    });

});

Route::prefix('vnp-sandbox')->name('vnpay-sandbox.')->group(function () {
    Route::get('/pay', [VnpSandboxController::class, 'index'])->name('sandbox');
    Route::post('/process', [VnpSandboxController::class, 'process'])->name('process');
    Route::get('/return', [VnpSandboxController::class, 'return'])->name('return');
    Route::get('/ipn', [VnpSandboxController::class, 'ipn'])->name('ipn');
});
