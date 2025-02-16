<?php

use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/comments-by-topic', [CommentController::class, 'getCommentsByTopic']);
