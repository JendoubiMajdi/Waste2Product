<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/donation', [HomeController::class, 'donation'])->name('donation');
Route::get('/event', [HomeController::class, 'event'])->name('event');
Route::get('/feature', [HomeController::class, 'feature'])->name('feature');
Route::get('/team', [HomeController::class, 'team'])->name('team');
Route::get('/testimonial', [HomeController::class, 'testimonial'])->name('testimonial');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/404', [HomeController::class, 'notFound'])->name('404');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
// Authentication routes
Route::get('/login', function () {
	return view('auth.login');
})->name('login');

Route::get('/register', function () {
	return view('auth.register');
})->name('register');