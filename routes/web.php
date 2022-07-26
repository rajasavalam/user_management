<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LoginController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/create/{id?}',function(){
//     //dd('aaaaaaaaaaaa');
// });
Route::get("/", [LoginController::class, "index"]);
Route::get("/login", [LoginController::class, "index"])->name('login');
Route::post("post_login", [LoginController::class, "post_login"]);
Route::get("create-user/{id?}", [UsersController::class, "createUser"])->middleware('auth');
Route::post("save_user", [UsersController::class, "saveUser"])->middleware('auth');
Route::post("delete_user", [UsersController::class, "deleteUser"])->middleware('auth');

Route::post("ajax_list_users", [UsersController::class, "ajaxListUsers"])->middleware('auth');
Route::get("list-users", [UsersController::class, "listUsers"])->middleware('auth');
//Route::get("edit-user", [UsersController::class, "editUser"])->middleware('auth');
Route::get("logout", [LoginController::class, "logout"]);
