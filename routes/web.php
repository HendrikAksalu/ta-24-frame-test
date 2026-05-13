<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WeatherController;
use App\Mail\Timetable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard', 302)->name('home');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

// Varasem Laravel starter /posts voog → avalik NFL blogi (eemaldab inglisekeelse loendi/loomise UI).
Route::redirect('/posts', '/blog', 302)->name('posts.index');
Route::post('/blog/{post}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('blog.comments.store');

// Kaart: kõik näevad; lisamine/muutmine ainult sisse loginud (POST/PUT/DELETE allpool).
Route::get('kaart', function () {
    return Inertia::render('Kaart');
})->name('kaart');
Route::get('markers', [MarkerController::class, 'index'])->name('markers.index');
Route::get('markers/{marker}', [MarkerController::class, 'show'])->name('markers.show');

// Blogisse postitamine: piisab auth-st (verified võib ülesande keskkonnas blokeerida).
Route::middleware(['auth'])->group(function () {
    Route::post('/blog', [BlogController::class, 'store'])->middleware('throttle:30,1')->name('blog.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('ilm', WeatherController::class)->name('ilm');
    Route::post('markers', [MarkerController::class, 'store'])->name('markers.store');
    Route::put('markers/{marker}', [MarkerController::class, 'update'])->name('markers.update');
    Route::delete('markers/{marker}', [MarkerController::class, 'destroy'])->name('markers.destroy');
});

// Shop / ostukorv / kassa (Stripe hosted checkout)
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/paypal', [CheckoutController::class, 'paypalCheckout'])->name('checkout.paypal');
Route::post('/checkout/stripe', [CheckoutController::class, 'stripeCheckout'])->name('checkout.stripe');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/pay', [CheckoutController::class, 'success'])->name('checkout.pay');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// NFL rookies UI (list via JSON API)
Route::get('/nfl-rookies', function () {
    return Inertia::render('nfl-rookies/Index');
})->name('nfl.rookies.index');

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
