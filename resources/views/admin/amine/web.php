<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;

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
            if (! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')->withErrors(['email' => 'You must verify your email before logging in.']);
            }
            \Illuminate\Support\Facades\Auth::login($user);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'The provided credentials are incorrect.']);
    })->name('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    // Add other protected routes here
});

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit', [ProfileController::class, 'update']);
    Route::get('/don-form', function () {
        return view('donation-form');
    })->name('don.form');
    Route::get('/forum-feed', function () {
        return view('forum');
    })->name('forum.feed');
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
    \Illuminate\Support\Facades\Auth::login($user);

    return redirect('/dashboard');
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Admin middleware to restrict access to admin only

// Define admin middleware inline (for demonstration, should be in a separate file for production)
// Use is_admin middleware for admin routes
Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('back.dashboard');
    })->name('admin.dashboard');

    // Donations management
    Route::get('/dons', function () {
        $type = request('type');
        $dons = \App\Models\Don::with('user')
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->paginate(10);

        return view('back.dons.index', compact('dons', 'type'));
    })->name('admin.dons.index');

    Route::delete('/dons/{don}', function (\App\Models\Don $don) {
        $don->delete();

        return redirect()->route('admin.dons.index')->with('success', 'Donation deleted successfully.');
    })->name('admin.dons.destroy');

    // Forum management
    Route::get('/forum/activity', function () {
        // Overall statistics
        $postCount = \App\Models\Post::count();
        $commentCount = \App\Models\Comment::count();
        $likeCount = \App\Models\Like::count();
        $reportCount = \App\Models\Report::count();
        $userCount = \App\Models\User::count();

        // Activity by day (last 7 days)
        $postsByDay = \App\Models\Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $commentsByDay = \App\Models\Comment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $likesByDay = \App\Models\Like::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Activity by type (pie chart data)
        $activityTypes = [
            'Posts' => $postCount,
            'Comments' => $commentCount,
            'Likes' => $likeCount,
            'Reports' => $reportCount,
        ];

        // Most active users
        $topUsers = \App\Models\User::withCount(['posts', 'comments', 'likes'])
            ->orderByRaw('posts_count + comments_count + likes_count DESC')
            ->take(5)
            ->get();

        return view('back.forum.activity', compact(
            'postCount', 'commentCount', 'likeCount', 'reportCount', 'userCount',
            'postsByDay', 'commentsByDay', 'likesByDay', 'activityTypes', 'topUsers'
        ));
    })->name('admin.forum.activity');

    Route::get('/forum/reports', function () {
        $reports = \App\Models\Report::with(['user', 'post.user'])->paginate(10);

        return view('back.forum.reports', compact('reports'));
    })->name('admin.forum.reports');

    // User management
    Route::post('/users/{user}/ban', function (\App\Models\User $user) {
        $duration = (int) request('duration'); // Cast to integer
        $reason = request('reason');

        if ($duration > 0) {
            $user->banned_until = now()->addDays($duration);
            $banMessage = "You have been banned for {$duration} days. Reason: {$reason}";
        } else {
            $user->banned_until = now()->addYears(100); // Permanent ban
            $banMessage = "You have been permanently banned. Reason: {$reason}";
        }
        $user->ban_reason = $reason;
        $user->save();

        // Create ban notification
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'ban',
            'message' => $banMessage,
            'related_id' => null,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'User banned successfully.');
    })->name('admin.users.ban');

    Route::post('/users/{user}/unban', function (\App\Models\User $user) {
        $user->banned_until = null;
        $user->ban_reason = null;
        $user->save();

        // Create unban notification
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'unban',
            'message' => 'Your account has been unbanned. You can now participate in the forum again.',
            'related_id' => null,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'User unbanned successfully.');
    })->name('admin.users.unban');
});

require __DIR__.'/custom_register.php';
