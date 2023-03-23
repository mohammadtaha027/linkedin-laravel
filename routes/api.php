<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendsController;
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

Route::get('/', function () {
    return response()->json('Welcome To Laravel API');
});

// Route::post('/register', [RegisterController::class, 'Register']);
// Route::post('/login', [LoginController::class, 'Login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UserController::class)->group(function () {
    Route::post('login', 'Login');
    Route::Post('register', 'Register');
});
Route::controller(UserController::class)->group(function () {
    Route::get('user', 'getUserDetails');
    Route::get('logout', 'userLogout');
})->middleware('auth:api');

Route::controller(PostController::class)->group(function () {
    Route::post('/posts/create', 'create');
    Route::get('/posts/{id}', 'show');
    Route::get('/posts', 'index');
    Route::post('/posts/update', 'update');
    Route::delete('/posts/delete/{id}', 'delete');
    Route::post('/uploads', 'upload');
    Route::post('/posts/like', 'like');
});
Route::controller(CommentController::class)->group(function () {
    Route::post('/comments/create', 'create');
    Route::get('/comments/all', 'getComments');
});
Route::controller(FriendsController::class)->group(function () {
    Route::post('/friend', 'Friend');
    Route::post('/unfriend', 'unFriend');
});
// Route::post('/register', [UserController::class, 'Register']);
// Route::post('/login', [UserController::class, 'Login']);
