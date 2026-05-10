<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class)
    ->except(['index', 'create'])
    ->middleware(['auth', 'verified']);
Route::resource('posts.comments', CommentController::class)->only(['store', 'destroy'])->middleware(['auth', 'verified']);
