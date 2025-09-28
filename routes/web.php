<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\ProductController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

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
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Password reset routes (Fortify)
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');

    Route::post('/login', function (Illuminate\Http\Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')->withErrors(['email' => 'You must verify your email before logging in.']);
            }
            Auth::login($user);
        }
        return back()->withErrors(['email' => 'The provided credentials are incorrect.']);
    })->name('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    // Add other protected routes here
    Route::resource('wastes', WasteController::class);
    Route::resource('products', ProductController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit', [ProfileController::class, 'update']);
});

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function ($id, $hash, Illuminate\Http\Request $request) {
    $user = User::findOrFail($id);
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403);
    }
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }
    Auth::login($user);
    return redirect('/dashboard');
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__ . '/custom_register.php';
