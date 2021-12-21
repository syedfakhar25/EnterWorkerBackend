<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/users-list', [ChatController::class, 'usersList']);
Route::get('/chats', [ChatController::class, 'index']);
Route::post('/fetch-messages', [ChatController::class,'fetchAllMessages']);
Route::post('/messages', [ChatController::class,'sendMessage']);

