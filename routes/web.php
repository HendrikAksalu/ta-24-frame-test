<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::resource('posts', PostController::class);
    Route::resource('authors', AuthorController::class);
    // Nested resource for comments under posts
    Route::resource('posts.comments', CommentController::class)->only(['store', 'destroy']);
});
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/posts.php';
require __DIR__ . '/authors.php';

Route::get("test", function () {
    return 'tere';
});

// Removed duplicate add-comment route