<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogUpdateController;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
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



Route::group([
    'prefix' => 'auth'
], function () {
    
    Route::post('login', [ AuthController::class, 'login']);
    Route::post('registration', [ AuthController::class, 'registration']);
    Route::post('me', [ AuthController::class, 'me']);
    Route::post('logout', [ AuthController::class, 'logout']);
    Route::post('refresh', [AuthController:: class, 'refresh']);
});


Route::resources(['blog' => BlogController::class]);

Route::post('blog/{id}', [BlogUpdateController::class, 'updateBlog']);

Route::get('blogs', [BlogsController::class, 'show']);

Route::get('blogs/category/{category_id}', [BlogsController::class, 'showPublicCategory']);

Route::get('blogs/{id}', [BlogsController::class, 'showPublicBlog']);

Route::post('like/{idBlog}', [LikeController::class, 'clickLike']);

Route::resources(['comment' => CommentController::class]);