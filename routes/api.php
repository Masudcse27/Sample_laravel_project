<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/test', [TestController::class, 'index']);

Route::controller(AuthController::class)->group(function (){
    Route::post('login','login')->name('login');
    Route::post('register','register')->name('user.register');
});

Route::middleware('auth:api')->group(function(){
    Route::get('users',[AuthController::class, 'users']);
    Route::controller(PostController::class)->prefix('post')->group(function(){
        Route::get('list', 'index');
        Route::post('store','store')->middleware('role:user');
    });
    
});