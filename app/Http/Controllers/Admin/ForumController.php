<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ForumController extends Controller
{
    public function activity()
    {
        $postCount = Post::count();
        $reportCount = Report::where('status', 'pending')->count();
        $postsByDay = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get();

        return view('back.forum.activity', compact('postCount', 'reportCount', 'postsByDay'));
    }

    public function reports()
    {
        $reports = Report::with(['user', 'post'])->paginate(10);
        return view('back.forum.reports', compact('reports'));
    }

    public function banUser(Request $request, User $user)
    {
        $request->validate([
            'duration' => 'required|integer|min:0',
            'reason' => 'required|string',
        ]);

        $user->ban_reason = $request->reason;
        if ($request->duration > 0) {
            $user->banned_until = Carbon::now()->addDays($request->duration);
        } else {
            $user->banned_until = Carbon::now()->addYears(100);
        }
        $user->save();

        $user->notify(new \App\Notifications\BanNotification($request->reason, $user->banned_until));

        return redirect()->back()->with('success', 'User banned.');
    }
}