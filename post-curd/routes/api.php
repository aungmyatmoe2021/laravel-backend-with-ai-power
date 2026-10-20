<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::resource('/post', PostController::class);
Route::get('/post',[PostController::class,'index']);
Route::post('/post',[PostController::class,'store']);
Route::get('/post/{id}',[PostController::class,'show']);
Route::post('/post/title',[PostController::class,'showByTitle']);
Route::put('/post/{id}',[PostController::class,'update']);
Route::delete('post/{id}',[PostController::class,'destroy']);
