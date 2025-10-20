<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use App\Models\UserBan;
use App\Models\Friendship;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminForumController extends Controller
{
    public function index()
    {
        // Overall statistics
        $stats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_comments' => Comment::count(),
            'total_likes' => Like::count(),
            'pending_reports' => PostReport::where('status', 'pending')->count(),
            'active_bans' => User::whereNotNull('banned_until')->where('banned_until', '>', now())->count(),
            'total_friendships' => Friendship::where('status', 'accepted')->count(),
            'total_messages' => Message::count(),
        ];

        // Calculate growth rates
        $stats['users_growth'] = $this->calculateGrowth(User::class);
        $stats['posts_growth'] = $this->calculateGrowth(Post::class);
        $stats['comments_growth'] = $this->calculateGrowth(Comment::class);

        // Activity last 7 days
        $recentActivity = [
            'new_users' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'new_posts' => Post::where('created_at', '>=', now()->subDays(7))->count(),
            'new_comments' => Comment::where('created_at', '>=', now()->subDays(7))->count(),
            'new_friendships' => Friendship::where('status', 'accepted')->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        // Activity chart data (last 7 days)
        $activityChartData = $this->getActivityChartData();

        // Most active users
        $topUsers = User::withCount(['posts', 'comments'])
            ->orderByDesc('posts_count')
            ->take(10)
            ->get();

        // Recent reports
        $recentReports = PostReport::with(['post.user', 'reporter'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Posts by visibility
        $postsByVisibility = Post::select('visibility', DB::raw('count(*) as count'))
            ->groupBy('visibility')
            ->get();

        // Most liked posts
        $mostLikedPosts = Post::withCount('likes')
            ->with('user')
            ->orderByDesc('likes_count')
            ->take(5)
            ->get();

        // Most commented posts
        $mostCommentedPosts = Post::withCount('comments')
            ->with('user')
            ->orderByDesc('comments_count')
            ->take(5)
            ->get();

        return view('admin.forum.index', compact(
            'stats', 
            'recentActivity', 
            'activityChartData',
            'topUsers', 
            'recentReports', 
            'postsByVisibility',
            'mostLikedPosts',
            'mostCommentedPosts'
        ));
    }

    private function calculateGrowth($model)
    {
        $currentMonth = $model::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $lastMonth = $model::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    private function getActivityChartData()
    {
        $data = [
            'labels' => [],
            'posts' => [],
            'comments' => [],
            'users' => [],
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data['labels'][] = $date->format('M d');
            
            $data['posts'][] = Post::whereDate('created_at', $date->format('Y-m-d'))->count();
            $data['comments'][] = Comment::whereDate('created_at', $date->format('Y-m-d'))->count();
            $data['users'][] = User::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        return $data;
    }

    public function reports()
    {
        $reports = PostReport::with(['post.user', 'reporter'])
            ->latest()
            ->paginate(20);

        return view('admin.forum.reports', compact('reports'));
    }

    public function resolveReport(PostReport $report, Request $request)
    {
        $action = $request->input('action');

        if ($action === 'delete_post') {
            $report->post->delete();
            $report->update(['status' => 'reviewed']);
        } else {
            $report->update(['status' => 'dismissed']);
        }

        return back()->with('success', 'Report handled successfully.');
    }

    public function banUser(Request $request, $userId)
    {
        $request->validate([
            'duration' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($userId);

        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot ban admin users.');
        }

        $bannedUntil = now()->addDays($request->duration);

        UserBan::create([
            'user_id' => $user->id,
            'banned_by' => Auth::id(),
            'reason' => $request->reason,
            'banned_until' => $bannedUntil,
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'ban',
            'data' => json_encode([
                'reason' => $request->reason,
                'duration' => $request->duration,
                'banned_until' => $bannedUntil->format('Y-m-d H:i:s'),
                'message' => "You have been banned for {$request->duration} days. Reason: {$request->reason}",
            ]),
        ]);

        return back()->with('success', 'User banned successfully.');
    }

    public function unbanUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Remove all active bans (temporary and permanent)
        UserBan::where('user_id', $user->id)->delete();

        // Clear the banned_until field in users table
        $user->update([
            'banned_until' => null,
            'ban_reason' => null
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'unban',
            'data' => json_encode([
                'message' => 'Your ban has been lifted. You can now access the platform again.',
                'unbanned_at' => now()->toDateTimeString(),
            ]),
        ]);

        return back()->with('success', 'User unbanned successfully.');
    }

    public function bannedUsers()
    {
        $bannedUsers = UserBan::with(['user', 'bannedBy'])
            ->where(function($query) {
                $query->whereNull('banned_until') // Permanent bans
                      ->orWhere('banned_until', '>', now()); // Active temporary bans
            })
            ->latest()
            ->paginate(20);

        return view('admin.forum.banned', compact('bannedUsers'));
    }
}
