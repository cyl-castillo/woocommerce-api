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

Route::get('/category/test',  [\App\Http\Controllers\WoocommerceController::class, 'test']);
Route::get('/products/load', [\App\Http\Controllers\WoocommerceController::class, 'loadProducts']);
Route::get('/products/update', [\App\Http\Controllers\WoocommerceController::class, 'updateProducts']);
Route::get('/products/delete', [\App\Http\Controllers\WoocommerceController::class, 'deleteProducts']);
Route::get('/category/create', [\App\Http\Controllers\WoocommerceController::class, 'updateCategory']);
Route::get('/category/subcategory', [\App\Http\Controllers\WoocommerceController::class, 'updateSubcategories']);
Route::get('/category/loadWoo', [\App\Http\Controllers\WoocommerceController::class, 'loadCategoryWoo']);
Route::get('/category/delete', [\App\Http\Controllers\WoocommerceController::class, 'deleteCategory']);
Route::get('/category/deleteCategoryWoo', [\App\Http\Controllers\WoocommerceController::class, 'deleteCategoryWoo']);



Route::get('/center/load/categories', [\App\Http\Controllers\StarCenterController::class, 'loadCategoriesCenter']);
Route::get('/center/sync/categories', [\App\Http\Controllers\StarCenterController::class, 'synCategoriesCenter']);


Route::post('/shopit/price', [\App\Http\Controllers\ShopitController::class, 'confgiPrice']);

