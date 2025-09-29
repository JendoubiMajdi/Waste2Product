<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FeedbackController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use App\Http\Controllers\EventController;

// --- Homepage and Static Pages ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/donation', [HomeController::class, 'donation'])->name('donation');
Route::get('/feature', [HomeController::class, 'feature'])->name('feature');
Route::get('/team', [HomeController::class, 'team'])->name('team');
Route::get('/testimonial', [HomeController::class, 'testimonial'])->name('testimonial');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Debug route
Route::get('/debug-events', function () {
    return [
        'controller_exists' => class_exists(App\Http\Controllers\EventController::class),
        'method_exists' => method_exists(App\Http\Controllers\EventController::class, 'index'),
        'view_exists' => view()->exists('events.index'),
        'events_count' => App\Models\Event::count(),
        'routes' => [
            'events.create' => route('events.create'),
        ]
    ];
});

// Alias for backward compatibility
Route::get('/event', [EventController::class, 'index'])->name('events');


// --- Auth and Guest Routes ---
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::get('/register', fn () => view('auth.register'))->name('register');

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
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['email' => 'The provided credentials are incorrect.']);
    });
});


// --- Authenticated and Protected Routes ---
Route::middleware('auth')->group(function () {

    // Core User Routes (Profile, Dashboard)
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit', [ProfileController::class, 'update']);

    // Resource Routes that REQUIRE Authentication
    Route::resource('wastes', WasteController::class)->middleware('verified');
    Route::resource('products', ProductController::class)->middleware('verified');
    Route::resource('feedbacks', FeedbackController::class)->middleware('verified');

    // FIX: Define ALL event routes here, but allow guests/unverified users for index/show.
    // Index, Show, Create, Store, Edit, Update, Delete
    Route::resource('events', EventController::class)
        // Allow public access to index and show methods
        ->withoutMiddleware(['auth', 'verified'])
        // Then apply 'verified' only to the methods that require it
        ->middleware([
            'create' => 'verified',
            'store' => 'verified',
            'edit' => 'verified',
            'update' => 'verified',
            'destroy' => 'verified',
        ]);

    // Custom Join Route
    Route::post('events/{event}/join', [EventController::class, 'join'])->name('events.join')->middleware('verified');


    // --- Email Verification Routes ---
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

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
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('resent', true);
    })->middleware('throttle:6,1')->name('verification.send');
});


// --- Fallback Routes ---
Route::get('/404', [HomeController::class, 'notFound'])->name('404');

Route::fallback(function () {
    return redirect()->route('404');
});

require __DIR__.'/custom_register.php';
