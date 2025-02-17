<?php

use App\Http\Controllers\Auth\JWTAuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TopicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login',  [JWTAuthController::class, 'login']);
    Route::post('register',  [JWTAuthController::class, 'register']);
});

Route::middleware('auth:api')->group( function ($router) {
    Route::post('logout',  [JWTAuthController::class, 'logout']);
    Route::post('refresh',  [JWTAuthController::class, 'refresh']);
    Route::post('me', [JWTAuthController::class, 'me']);
});

Route::middleware('auth:api')->group( function ($router) {
    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/comments-by-user', [CommentController::class, 'getCommentsByUser']);
    Route::get('/topics-with-user-comments', [CommentController::class, 'getTopicsWithUserComments']);

});

Route::get('/comments-by-topic', [CommentController::class, 'getCommentsByTopic']);

Route::get('topics',[TopicController::class,'index']);
