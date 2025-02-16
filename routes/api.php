<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\TopicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/comments-by-topic', [CommentController::class, 'getCommentsByTopic']);
Route::post('/comments', [CommentController::class, 'store']);

Route::get('topics',[TopicController::class,'index']);
