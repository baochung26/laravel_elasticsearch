<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::get('/create-user-index', [UserController::class, 'indexUser']);
Route::get('/create-post-index', [UserController::class, 'indexPost']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/delete-user-index', [UserController::class, 'deleteUserIndex']);
Route::get('/search-multiple-index', [UserController::class, 'searchMultiIndex']);
