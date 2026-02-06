<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\App\Http\Controllers\Admin\CategoryController;
use Modules\Category\App\Http\Controllers\Admin\SubCategoryController;

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

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth', 'role:admin']], function () {
    Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
    Route::resource('categories', CategoryController::class);
    Route::post('subcategories/bulk-delete', [SubCategoryController::class, 'bulkDelete'])->name('subcategories.bulk-delete');
    Route::resource('subcategories', SubCategoryController::class);
});
