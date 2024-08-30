<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('get/users',[UserController::class,'getUsers']);
Route::get('get/users/{status}',[UserController::class,'getData_by_Status']);
Route::get('get/users-by-date/{date}',[UserController::class,'getData_by_Date']);
Route::post('update/user/{id}',[UserController::class,'updateUser']);
Route::post('/store/user',[UserController::class,'Store']);
Route::delete('delete/user/{id}',[UserController::class,'Destroy']);
