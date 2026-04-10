<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\PostController;
use App\Mail\Timetable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{post}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('blog.comments.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('markers', [MarkerController::class, 'index'])->name('markers.index');
    Route::post('markers', [MarkerController::class, 'store'])->name('markers.store');
    Route::get('markers/{marker}', [MarkerController::class, 'show'])->name('markers.show');
    Route::put('markers/{marker}', [MarkerController::class, 'update'])->name('markers.update');
    Route::delete('markers/{marker}', [MarkerController::class, 'destroy'])->name('markers.destroy');
    Route::resource('posts', PostController::class);
    Route::resource('authors', AuthorController::class);
    // Nested resource for comments under posts
    Route::resource('posts.comments', CommentController::class)->only(['store', 'destroy']);
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/posts.php';
require __DIR__.'/authors.php';

Route::get('test', function () {
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
