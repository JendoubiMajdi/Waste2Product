<?php

use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CollectionPointController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LivraisonController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WasteController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    // If authenticated, redirect to home
    if (Auth::check()) {
        return redirect()->route('home');
    }
    // If you migrated a custom index view, use that; fall back to welcome
    if (view()->exists('index')) {
        return view('index');
    }

    return view('welcome');
});

// Auth pages (simple view routes for the static template)
// Note: Fortify handles the POST routes for login/register/password-reset
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Admin-only routes (must be BEFORE auth routes to match specific paths first)
Route::middleware(['auth', 'admin'])->group(function () {
    // Challenge management
    Route::get('/challenges/create', [ChallengeController::class, 'create'])->name('challenges.create');
    Route::post('/challenges', [ChallengeController::class, 'store'])->name('challenges.store');
    Route::post('/challenge-submissions/{id}/approve', [ChallengeController::class, 'approveSubmission'])->name('challenges.approve');

    // Event management (admin only) - MUST be before wildcard /events/{event}
    Route::get('/events/create', [App\Http\Controllers\EventController::class, 'create'])->name('events.create');
    Route::post('/events', [App\Http\Controllers\EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [App\Http\Controllers\EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [App\Http\Controllers\EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [App\Http\Controllers\EventController::class, 'destroy'])->name('events.destroy');

    // Admin Donation Management
    Route::prefix('admin/donations')->name('admin.donations.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminDonationController::class, 'index'])->name('index');
        Route::post('/{donation}/approve', [App\Http\Controllers\Admin\AdminDonationController::class, 'approve'])->name('approve');
        Route::post('/{donation}/reject', [App\Http\Controllers\Admin\AdminDonationController::class, 'reject'])->name('reject');
        Route::delete('/{donation}', [App\Http\Controllers\Admin\AdminDonationController::class, 'destroy'])->name('destroy');
    });

    // Admin Forum Management
    Route::prefix('admin/forum')->name('admin.forum.')->group(function () {
        Route::get('/activity', [App\Http\Controllers\Admin\AdminForumController::class, 'activity'])->name('activity');
        Route::get('/reports', [App\Http\Controllers\Admin\AdminForumController::class, 'reports'])->name('reports');
        Route::post('/reports/{report}/resolve', [App\Http\Controllers\Admin\AdminForumController::class, 'resolveReport'])->name('reports.resolve');
        Route::post('/users/{user}/ban', [App\Http\Controllers\Admin\AdminForumController::class, 'banUser'])->name('users.ban');
        Route::post('/users/{user}/unban', [App\Http\Controllers\Admin\AdminForumController::class, 'unbanUser'])->name('users.unban');
    });
});

// Chatbot Routes (available to all users - authenticated and guest)
Route::get('/chatbot', [App\Http\Controllers\ChatbotController::class, 'index'])->name('chatbot');
Route::post('/chatbot/send', [App\Http\Controllers\ChatbotController::class, 'sendMessage'])->name('chatbot.send');
Route::post('/chatbot/clear', [App\Http\Controllers\ChatbotController::class, 'clearHistory'])->name('chatbot.clear');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Home/Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

    // Progress/Leaderboard
    Route::get('/progress', function () {
        $user = Auth::user();

        // Get user stats
        $totalSubmissions = App\Models\ChallengeSubmission::where('user_id', $user->id)->count();
        $approvedSubmissions = App\Models\ChallengeSubmission::where('user_id', $user->id)->where('status', 'approved')->count();
        $approvalRate = $totalSubmissions > 0 ? round(($approvedSubmissions / $totalSubmissions) * 100) : 0;

        // Get top 10 users
        $topUsers = App\Models\User::orderBy('points', 'desc')->take(10)->get();

        // Get current user rank
        $userRank = App\Models\User::where('points', '>', $user->points)->count() + 1;

        return view('progress.index', compact('user', 'totalSubmissions', 'approvedSubmissions', 'approvalRate', 'topUsers', 'userRank'));
    })->middleware('auth')->name('progress');

    // Challenges
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::post('/challenges/{challengeId}/submit', [ChallengeController::class, 'submitProof'])->name('challenges.submit');

    // Products
    Route::resource('products', ProductController::class);

    // Wastes
    Route::post('/wastes/classify-image', [WasteController::class, 'classifyImage'])->name('wastes.classify');
    Route::resource('wastes', WasteController::class);

    // Orders
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my-orders');
    Route::resource('orders', OrderController::class);

    // Collection Points
    Route::get('/collection_points/dashboard', [CollectionPointController::class, 'dashboard'])->name('collection_points.dashboard');
    Route::get('/collection_points/map', [CollectionPointController::class, 'showMap'])->name('collection_points.map');
    Route::post('/collection_points/find-nearest', [CollectionPointController::class, 'findNearest'])->name('collection_points.findNearest');
    Route::resource('collection_points', CollectionPointController::class);

    // Livraisons
    Route::resource('livraisons', LivraisonController::class);

    // Donations
    Route::get('/donations', [App\Http\Controllers\DonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/create', [App\Http\Controllers\DonationController::class, 'create'])->name('donations.create');
    Route::post('/donations', [App\Http\Controllers\DonationController::class, 'store'])->name('donations.store');
    Route::get('/donations/{donation}', [App\Http\Controllers\DonationController::class, 'show'])->name('donations.show');
    Route::get('/my-donations', [App\Http\Controllers\DonationController::class, 'myDonations'])->name('donations.my-donations');

    // Forum
    Route::get('/forum', [App\Http\Controllers\ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/create', [App\Http\Controllers\ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [App\Http\Controllers\ForumController::class, 'storePost'])->name('forum.store');
    Route::post('/forum/posts', [App\Http\Controllers\ForumController::class, 'storePost'])->name('forum.posts.store');
    Route::get('/forum/posts/{post}', [App\Http\Controllers\ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum/posts/{post}/comments', [App\Http\Controllers\ForumController::class, 'storeComment'])->name('forum.comments.store');
    Route::delete('/forum/comments/{comment}', [App\Http\Controllers\ForumController::class, 'destroyComment'])->name('forum.comments.destroy');
    Route::post('/forum/posts/{post}/like', [App\Http\Controllers\ForumController::class, 'toggleLike'])->name('forum.posts.like');
    Route::post('/forum/posts/{post}/report', [App\Http\Controllers\ForumController::class, 'report'])->name('forum.posts.report');
    Route::delete('/forum/posts/{post}', [App\Http\Controllers\ForumController::class, 'destroy'])->name('forum.posts.destroy');

    // Events
    Route::get('/events', [App\Http\Controllers\EventController::class, 'index'])->name('events.index');
    Route::get('/my-events', [App\Http\Controllers\EventController::class, 'myEvents'])->name('events.my-events');
    // Note: /events/create is in admin section and should be loaded before /events/{event}
    Route::get('/events/{event}', [App\Http\Controllers\EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/register', [App\Http\Controllers\EventController::class, 'register'])->name('events.register');
    Route::post('/events/{event}/unregister', [App\Http\Controllers\EventController::class, 'unregister'])->name('events.unregister');

    // Event Feedback
    Route::post('/events/{event}/feedback', [App\Http\Controllers\EventFeedbackController::class, 'store'])->name('events.feedback.store');
    Route::put('/feedback/{feedback}', [App\Http\Controllers\EventFeedbackController::class, 'update'])->name('events.feedback.update');
    Route::delete('/feedback/{feedback}', [App\Http\Controllers\EventFeedbackController::class, 'destroy'])->name('events.feedback.destroy');

    // Event AI Analytics
    Route::get('/events/{event}/analytics', [App\Http\Controllers\EventAnalyticsController::class, 'show'])->name('events.analytics');
    Route::post('/events/{event}/analytics/generate', [App\Http\Controllers\EventAnalyticsController::class, 'generate'])->name('events.analytics.generate');
});

// Admin Backoffice Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

    // Post Reports Management
    Route::get('/post-reports', [App\Http\Controllers\Admin\AdminDashboardController::class, 'reports'])->name('post-reports');
    Route::get('/post-reports/{id}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'showReport'])->name('post-reports.show');
    Route::post('/post-reports/{id}/ban', [App\Http\Controllers\Admin\AdminDashboardController::class, 'banUser'])->name('post-reports.ban');
    Route::post('/post-reports/{id}/resolve', [App\Http\Controllers\Admin\AdminDashboardController::class, 'resolveReport'])->name('post-reports.resolve');
    Route::delete('/post-reports/{id}/delete-post', [App\Http\Controllers\Admin\AdminDashboardController::class, 'deletePost'])->name('post-reports.delete-post');

    // Events Management
    Route::get('/events-management', [App\Http\Controllers\Admin\AdminDashboardController::class, 'events'])->name('events-management');

    // Users Management
    Route::get('/users', function () {
        $users = App\Models\User::paginate(20);
        $stats = [
            'total' => App\Models\User::count(),
            'admins' => App\Models\User::where('role', 'admin')->count(),
            'collectors' => App\Models\User::where('role', 'collector')->count(),
            'customers' => App\Models\User::where('role', 'customer')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    })->name('users');

    Route::patch('/users/{user}', function (App\Models\User $user) {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,collector,transporter,customer',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    })->name('users.update');

    Route::delete('/users/{user}', function (App\Models\User $user) {
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete yourself',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    })->name('users.destroy');

    // Orders Management
    Route::get('/orders', function () {
        $orders = App\Models\Order::with(['client', 'products'])
            ->latest()
            ->paginate(20)
            ->through(function ($order) {
                $order->total = $order->products->sum(function ($product) {
                    return $product->pivot->quantite * $product->prix;
                });

                return $order;
            });

        return view('admin.orders.index', compact('orders'));
    })->name('orders');

    Route::patch('/orders/{order}/update-status', function (App\Models\Order $order) {
        $validated = request()->validate([
            'statut' => 'required|in:en cours,livré,annulé',
        ]);

        $order->update(['statut' => $validated['statut']]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'order' => $order,
        ]);
    })->name('orders.update-status');

    // Products Management
    Route::get('/products', function () {
        $products = App\Models\Product::paginate(20);

        return view('admin.products.index', compact('products'));
    })->name('products');

    Route::patch('/products/{product}', function (App\Models\Product $product) {
        $validated = request()->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:0',
            'etat' => 'required|in:disponible,rupture',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    })->name('products.update');

    Route::delete('/products/{product}', function (App\Models\Product $product) {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    })->name('products.destroy');

    // Collection Points Management
    Route::get('/collection-points', function () {
        $collection_points = App\Models\CollectionPoint::paginate(20);

        return view('admin.collection-points.index', compact('collection_points'));
    })->name('collection-points');

    Route::patch('/collection-points/{collectionPoint}', function (App\Models\CollectionPoint $collectionPoint) {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'working_hours' => 'required|string|regex:/^\d{2}:\d{2}-\d{2}:\d{2}$/',
            'status' => 'required|in:active,inactive',
        ]);

        $collectionPoint->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Collection point updated successfully',
            'collection_point' => $collectionPoint,
        ]);
    })->name('collection-points.update');

    Route::patch('/collection-points/{collectionPoint}/toggle-status', function (App\Models\CollectionPoint $collectionPoint) {
        $validated = request()->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $collectionPoint->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Collection point status updated successfully',
            'collection_point' => $collectionPoint,
        ]);
    })->name('collection-points.toggle-status');

    Route::delete('/collection-points/{collectionPoint}', function (App\Models\CollectionPoint $collectionPoint) {
        $collectionPoint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collection point deleted successfully',
        ]);
    })->name('collection-points.destroy');

    // Wastes Management
    Route::get('/wastes', function () {
        $wastes = App\Models\Waste::with(['user', 'collectionPoint'])
            ->latest()
            ->paginate(20);

        return view('admin.wastes.index', compact('wastes'));
    })->name('wastes');

    Route::patch('/wastes/{waste}', function (App\Models\Waste $waste) {
        $validated = request()->validate([
            'type' => 'required|string|max:255',
            'quantite' => 'required|numeric|min:0',
            'status' => 'required|in:pending,collected,processed',
        ]);

        $waste->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Waste updated successfully',
            'waste' => $waste,
        ]);
    })->name('wastes.update');

    Route::delete('/wastes/{waste}', function (App\Models\Waste $waste) {
        $waste->delete();

        return response()->json([
            'success' => true,
            'message' => 'Waste deleted successfully',
        ]);
    })->name('wastes.destroy');

    // Challenges Management
    Route::get('/challenges', function () {
        $challenges = App\Models\Challenge::withCount('submissions')
            ->latest()
            ->paginate(20);

        return view('admin.challenges.index', compact('challenges'));
    })->name('challenges');

    Route::post('/challenges', function () {
        $data = request()->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle image upload
        if (request()->hasFile('image')) {
            $data['image'] = base64_encode(file_get_contents(request()->file('image')->getRealPath()));
        }

        App\Models\Challenge::create($data);

        return redirect()->route('admin.challenges')->with('success', 'Challenge created successfully!');
    })->name('challenges.store');

    Route::patch('/challenges/{challenge}', function (App\Models\Challenge $challenge) {
        $validated = request()->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $challenge->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Challenge updated successfully',
            'challenge' => $challenge,
        ]);
    })->name('challenges.update');

    Route::delete('/challenges/{challenge}', function (App\Models\Challenge $challenge) {
        $challenge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Challenge deleted successfully',
        ]);
    })->name('challenges.destroy');

    // Challenge Submissions Management
    Route::get('/submissions', function () {
        $submissions = App\Models\ChallengeSubmission::with(['user', 'challenge'])
            ->latest()
            ->paginate(20);

        return view('admin.submissions.index', compact('submissions'));
    })->name('submissions');

    Route::post('/submissions/{submission}/approve', function (App\Models\ChallengeSubmission $submission) {
        $submission->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Award points to user
        $user = $submission->user;
        $challenge = $submission->challenge;
        $user->increment('points', $challenge->points);

        // Update user badge based on new points
        if ($user->points >= 1000) {
            $user->badge = 'diamond';
        } elseif ($user->points >= 500) {
            $user->badge = 'platinum';
        } elseif ($user->points >= 250) {
            $user->badge = 'gold';
        } elseif ($user->points >= 100) {
            $user->badge = 'silver';
        } elseif ($user->points >= 50) {
            $user->badge = 'bronze';
        } else {
            $user->badge = 'beginner';
        }
        $user->save();

        return back()->with('success', 'Submission approved successfully!');
    })->name('submissions.approve');

    Route::post('/submissions/{submission}/reject', function (App\Models\ChallengeSubmission $submission) {
        $submission->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Submission rejected successfully!');
    })->name('submissions.reject');

    // Reports - Old analytics page (disabled, using post-reports instead)
    // Route::get('/reports', function () {
    //     return view('admin.reports.index');
    // })->name('reports');

    // Settings
    Route::get('/settings', function () {
        return view('admin.settings.index');
    })->name('settings');

    // Profile
    Route::get('/profile', function () {
        return view('admin.profile.index');
    })->name('profile');

    // Forum Management
    Route::get('/forum', [App\Http\Controllers\Admin\AdminForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/reports', [App\Http\Controllers\Admin\AdminForumController::class, 'reports'])->name('forum.reports');
    Route::post('/forum/reports/{report}/resolve', [App\Http\Controllers\Admin\AdminForumController::class, 'resolveReport'])->name('forum.reports.resolve');
    Route::post('/forum/users/{user}/ban', [App\Http\Controllers\Admin\AdminForumController::class, 'banUser'])->name('forum.users.ban');
    Route::post('/forum/users/{user}/unban', [App\Http\Controllers\Admin\AdminForumController::class, 'unbanUser'])->name('forum.users.unban');
    Route::get('/forum/banned-users', [App\Http\Controllers\Admin\AdminForumController::class, 'bannedUsers'])->name('forum.banned');
});

// Friend System Routes (Protected by auth and ban check)
Route::middleware(['auth', 'check.banned'])->group(function () {
    // Friendships
    Route::post('/friends/request', [App\Http\Controllers\FriendshipController::class, 'sendRequest'])->name('friends.request');
    Route::post('/friends/{friendship}/accept', [App\Http\Controllers\FriendshipController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/friends/{friendship}/deny', [App\Http\Controllers\FriendshipController::class, 'denyRequest'])->name('friends.deny');
    Route::delete('/friends/{friend}/remove', [App\Http\Controllers\FriendshipController::class, 'removeFriend'])->name('friends.remove');
    Route::get('/friends', [App\Http\Controllers\FriendshipController::class, 'index'])->name('friends.index');
    
    // Blocking
    Route::post('/users/block', [App\Http\Controllers\FriendshipController::class, 'blockUser'])->name('users.block');
    Route::delete('/users/{user}/unblock', [App\Http\Controllers\FriendshipController::class, 'unblockUser'])->name('users.unblock');

    // Messaging
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/send', [App\Http\Controllers\MessageController::class, 'send'])->name('messages.send');
    Route::post('/messages/{conversation}/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.read');

    // User Profile
    Route::get('/profile/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');

    // Posts
    Route::get('/posts', [App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/share', [App\Http\Controllers\PostController::class, 'shareToFeed'])->name('posts.share');
    Route::delete('/posts/{post}', [App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy');

    // Post Reports
    Route::post('/posts/report', [App\Http\Controllers\PostReportController::class, 'store'])->name('posts.report');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.count');
});
