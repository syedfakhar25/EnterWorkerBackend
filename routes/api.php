<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\API\AuthController; 
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;


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

// Route::group(['prefix'=>'v1/auth'],function(){
//     Route::post('/register', [AuthController::class, 'register']);
//     Route::post('/verify', [AuthController::class,'verify']);
//     Route::post('/resend-otp', [AuthController::class,'resendOtp']);
//     Route::post('/login', [AuthController::class,'login']);
//     Route::get('/logout', [AuthController::class,'logout'])->middleware('auth:api');
//     });

Route::group(['prefix'=>'admin'],function(){
    Route::resource('users', UsersController::class);
    });

Route::get('/all-employee', [UsersController::class,'getallemployee'])->name('allemployee');
Route::get('/all-customer', [UsersController::class,'getallcustomer'])->name('allcustomer');;
Route::get('/all-manager', [UsersController::class,'getallmanagers'])->name('allmanagers');

Route::group(['prefix'=>'manager'],function(){
    Route::resource('projets', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    });