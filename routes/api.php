<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/getUsers', [\App\Http\Controllers\AppController::class, 'getUsers']);
Route::get('/getFiles/{id}', [\App\Http\Controllers\AppController::class, 'getFiles']);
Route::get('/deleteFiles/{id}', [\App\Http\Controllers\AppController::class, 'deleteFiles']);
Route::get('/getApproved/{id}/{page}', [\App\Http\Controllers\AppController::class, 'getApproved']);
Route::get('/getFailure/{id}/{page}', [\App\Http\Controllers\AppController::class, 'getFailure']);
Route::post('/upload', [\App\Http\Controllers\AppController::class, 'upload']);
