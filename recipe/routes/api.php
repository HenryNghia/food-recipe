<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LevelController;
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
Route::post('/login', [AccountController::class, 'login']);
Route::post('/login-admin', [AccountController::class, 'loginAdmin']);
Route::post('/register', [AccountController::class, 'register']);
Route::post('/check', [AccountController::class, 'checkToken']);
Route::get('/dang-xuat', [AccountController::class, 'logout']);
Route::post('/dang-xuat-tat-ca', [AccountController::class, 'logoutAll']);
Route::delete('/remove-token/{id}', [AccountController::class, 'removeToken']);

Route::group([], function() {
    Route::group(['prefix' => '/category'], function() {
        Route::get('/data', [CategoryController::class, 'GetData']);
        Route::get('/data/{id}', [CategoryController::class, 'GetDataById']);
        Route::post('/search-data', [CategoryController::class, 'SearchData']);
        Route::post('/create-data', [CategoryController::class, 'CreateData']);
        Route::put('/update-data', [CategoryController::class, 'UpdateData']);
        Route::post('/delete-data', [CategoryController::class, 'DeleteData']);
    });

    Route::group(['prefix' => '/recipe'], function() {
        Route::get('/data', [RecipeController::class, 'GetData']);
        Route::get('/data-by-rating', [RecipeController::class, 'GetDataByRating']);
        Route::get('/data-by-time', [RecipeController::class, 'GetDataByTime']);
        Route::get('/data/{id}', [RecipeController::class, 'GetDataById']);
        Route::get('/data-by-category/{categoryId}', [RecipeController::class, 'GetDataByCategory']);
        Route::get('/data-by-user', [RecipeController::class, 'GetDataByUser']);
        Route::post('/search-data', [RecipeController::class, 'SearchData']);
        Route::post('/search-data-all', [RecipeController::class, 'SearchDataAll']);
        Route::post('/create-data', [RecipeController::class, 'CreateData']);
        Route::put('/update-data', [RecipeController::class, 'UpdateData']);
        Route::post('/delete-data', [RecipeController::class, 'DeleteData']);
    });

    Route::group(['prefix' => '/favorite'], function() {
        Route::get('/data', [FavoriteController::class, 'GetDataByUser']);
        Route::get('/data/{id}', [FavoriteController::class, 'GetDataById']);
        Route::post('/search-data', [FavoriteController::class, 'SearchData']);
        Route::post('/create-data', [FavoriteController::class, 'CreateData']);
        Route::put('/update-data', [FavoriteController::class, 'UpdateData']);
        Route::post('/delete-data', [FavoriteController::class, 'DeleteData']);
         Route::post('/check-data', [FavoriteController::class, 'CheckData']);
    });

    Route::group(['prefix' => '/user'], function() {
        Route::get('/data', [AccountController::class, 'GetData']);
        Route::put('/update-data', [AccountController::class, 'UpdateData']);
    });

    Route::group(['prefix' => '/level'], function() {
        Route::get('/data', [LevelController::class, 'GetData']);
        Route::post('/search-data', [LevelController::class, 'SearchData']);
        Route::post('/create-data', [LevelController::class, 'CreateData']);
        Route::put('/update-data', [LevelController::class, 'UpdateData']);
        Route::post('/delete-data', [LevelController::class, 'DeleteData']);
    });
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
