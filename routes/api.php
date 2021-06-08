<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(["prefix" => "v1"], function () {
    Route::post('signup', "AuthController@register")->name("auth.register");
    Route::post('login', "AuthController@login")->name("auth.login");
    
    Route::get('/posts', [PostsController::class, 'index']);
    Route::post('/posts', [PostsController::class, 'store']);
    Route::get('/posts/create', [PostsController::class, 'create']);
    Route::get('/posts/{post}/edit', [PostsController::class, 'edit']);
    Route::put('/posts/{post}', [PostsController::class, 'update']);
    Route::delete('/posts/{post}', [PostsController::class, 'destroy']);
});