<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RecipeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group([], function() {
    Route::group(['prefix' => '/category'], function() {
        Route::get('/data', [CategoryController::class, 'GetData']);
        Route::post('/search-data', [CategoryController::class, 'SearchData']);
        Route::post('/create-data', [CategoryController::class, 'CreateData']);
        Route::put('/update-data', [CategoryController::class, 'UpdateData']);
        Route::post('/delete-data', [CategoryController::class, 'DeleteData']);
    });

    Route::group(['prefix' => '/recipe'], function() {
        Route::get('/data', [RecipeController::class, 'GetData']);
        Route::post('/search-data', [RecipeController::class, 'SearchData']);
        Route::post('/create-data', [RecipeController::class, 'CreateData']);
        Route::put('/update-data', [RecipeController::class, 'UpdateData']);
        Route::post('/delete-data', [RecipeController::class, 'DeleteData']);
    });
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
