<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

//admin routes
Route::get('/admins-only',function(){
    return "only admins can see this pages";
})->middleware('can:visitAdminPages');

// user routes
Route::get('/',[UserController::class,"ShowCorrectHomePage"])->name('login');
Route::post('/register',[UserController::class,'register'])->name('reg')->middleware('guest');
Route::post('/login',[UserController::class,'login'])->middleware('guest');
Route::post('/logout',[UserController::class,'logout'])->middleware('MustBeLoggedIn');
Route::get('/manage-avatar',[UserController::class,'AvatarForm'])->middleware('MustBeLoggedIn');
Route::post('/manage-avatar',[UserController::class,'StoreAvatar'])->middleware('MustBeLoggedIn');

// blog post routes
Route::get('/Create-Post',[PostController::class,"ShowCreateForm"])->middleware('MustBeLoggedIn');
Route::post('/Create-Post',[PostController::class,"AddNewPost"])->middleware('MustBeLoggedIn');
Route::get('/post/{post}',[PostController::class,"ShowSinglePost"]);
Route::delete('/post/{post}',[PostController::class,"delete"])->middleware('can:delete,post');
Route::get('/post/{post}/edit',[PostController::class,"EditForm"])->middleware('can:update,post');
Route::put('/post/{post}',[PostController::class,"update"])->middleware('can:update,post');

// profile routes
Route::get('/profile/{user:username}',[UserController::class,'profile']);