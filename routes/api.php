<?php

use App\Http\Controllers\AuthController;
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