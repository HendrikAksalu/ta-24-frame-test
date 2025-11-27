<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class)->middleware(['auth', 'verified']);
Route::resource('posts.comments', CommentController::class)->only(['store', 'destroy'])->middleware(['auth', 'verified']);
