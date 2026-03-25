<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CommentController;
use App\Mail\Timetable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
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

Route::get('/mailable', function () {
    $startDate = now()->addWeek(1)->startOfWeek();
    $endDate = now()->addWeek(1)->endOfWeek();

    $response = Http::get('https://tahveltp.edu.ee/hois_back/timetableevents/timetableSearch', [
        'from' => $startDate->toIsoString(),
        'lang' => 'ET',
        'page' => 0,
        'schoolId' => 38,
        'size' => 50,
        'studentGroups' => '4b26d1e5-11ac-4c63-840e-46c450c529ee',
        'thru' => $endDate->toIsoString(),
    ]);

    $timetableEvents = collect($response->json()['content'])
        ->sortBy(['date', 'timeStart'])
        ->groupBy(fn ($event) => Carbon::parse($event['date'])->locale('et')->dayName);

    return new Timetable($timetableEvents, $startDate, $endDate);
});