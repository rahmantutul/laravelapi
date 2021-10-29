<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('users',[ApiController::class,'getUsers']);
Route::post('add-users',[ApiController::class,'addUser']);
Route::post('add-multiple-users',[ApiController::class,'addUsers']);
Route::put('update-users/{id}',[ApiController::class,'updateUsers']);
Route::patch('update-user-name/{id}',[ApiController::class,'updateUserName']);
Route::delete('delete-user/{id}',[ApiController::class,'deleteUser']);
Route::delete('delete-multiple-users/{ids}',[ApiController::class,'deleteMultipleUser']);
Route::get('get-users',[ApiController::class,'headerAuthorization']);
Route::post('register-user',[ApiController::class,'registerUser']);
Route::post('login-user',[ApiController::class,'loginUser']);

// With passport
Route::post('passport-register-user',[ApiController::class,'registerUserWithPassport']);





