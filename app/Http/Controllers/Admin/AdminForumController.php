<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Don;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Report;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminForumController extends Controller
{
    /**
     * Display forum activity dashboard
     */
    public function activity()
    {
        // Overall statistics
        $postCount = Post::count();
        $commentCount = Comment::count();
        $likeCount = Like::count();
        $reportCount = Report::where('status', 'pending')->count();
        $userCount = User::count();
        
        // Activity by day (last 7 days)
        $postsByDay = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $commentsByDay = Comment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $likesByDay = Like::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Activity by type (pie chart data)
        $activityTypes = [
            'Posts' => $postCount,
            'Comments' => $commentCount,
            'Likes' => $likeCount,
            'Reports' => $reportCount
        ];
        
        // Most active users
        $topUsers = User::withCount(['posts', 'comments', 'likes'])
            ->orderByRaw('posts_count + comments_count + likes_count DESC')
            ->take(5)
            ->get();
        
        return view('admin.forum.activity', compact(
            'postCount', 'commentCount', 'likeCount', 'reportCount', 'userCount',
            'postsByDay', 'commentsByDay', 'likesByDay', 'activityTypes', 'topUsers'
        ));
    }

    /**
     * Display reports list
     */
    public function reports()
    {
        $reports = Report::with(['user', 'post.user'])
            ->orderBy('status')
            ->latest()
            ->paginate(20);
        
        return view('admin.forum.reports', compact('reports'));
    }

    /**
     * Resolve a report
     */
    public function resolveReport(Report $report, Request $request)
    {
        $action = $request->input('action'); // 'dismiss' or 'delete_post'

        if ($action === 'delete_post') {
            $report->post->delete();
            $report->update(['status' => 'resolved', 'admin_action' => 'Post deleted']);
        } else {
            $report->update(['status' => 'dismissed', 'admin_action' => 'Report dismissed']);
        }

        return redirect()->route('admin.forum.reports')
            ->with('success', 'Report handled successfully.');
    }

    /**
     * Ban a user
     */
    public function banUser(User $user, Request $request)
    {
        $validated = $request->validate([
            'duration' => 'required|integer|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $duration = (int) $validated['duration'];
        $reason = $validated['reason'];
        
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
        Notification::create([
            'user_id' => $user->id,
            'type' => 'ban',
            'message' => $banMessage,
            'related_id' => null,
            'is_read' => false
        ]);
        
        return redirect()->back()->with('success', 'User banned successfully.');
    }

    /**
     * Unban a user
     */
    public function unbanUser(User $user)
    {
        $user->banned_until = null;
        $user->ban_reason = null;
        $user->save();
        
        // Create unban notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'unban',
            'message' => 'Your account has been unbanned. You can now participate in the forum again.',
            'related_id' => null,
            'is_read' => false
        ]);
        
        return redirect()->back()->with('success', 'User unbanned successfully.');
    }
}
