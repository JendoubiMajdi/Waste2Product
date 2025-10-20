<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollectionPoint;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Waste;
use App\Models\PostReport;
use App\Models\Post;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Calculate total revenue from delivered orders
        $total_revenue = Order::where('statut', 'livré')
            ->with('products')
            ->get()
            ->sum(function ($order) {
                return $order->products->sum(function ($product) {
                    return $product->pivot->quantite * $product->prix;
                });
            });

        // Stats
        $stats = [
            'total_users' => User::count(),
            'users_growth' => $this->calculateGrowth(User::class),
            'total_orders' => Order::count(),
            'orders_growth' => $this->calculateGrowth(Order::class),
            'total_revenue' => $total_revenue,
            'revenue_growth' => 12.5, // TODO: Calculate actual growth
            'total_waste' => Waste::sum('quantite') ?? 0,
            'waste_growth' => $this->calculateGrowth(Waste::class, 'quantite'),
            'pending_reports' => PostReport::where('status', 'pending')->count(),
            'banned_users' => User::whereNotNull('banned_until')->where('banned_until', '>', now())->count(),
        ];

        // Recent Orders
        $recent_orders = Order::with(['client', 'products'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                // Calculate total from order items
                $order->total = $order->products->sum(function ($product) {
                    return $product->pivot->quantite * $product->prix;
                });

                return $order;
            });

        // Top Products
        $top_products = Product::withCount('orders as sales_count')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        // Recent Users
        $recent_users = User::latest()->take(5)->get();

        // Collection Points Stats
        $collection_points_stats = [
            'total' => CollectionPoint::count(),
            'active' => CollectionPoint::where('status', 'active')->count(),
            'inactive' => CollectionPoint::where('status', 'inactive')->count(),
        ];

        // Sales Chart Data (Last 7 days)
        $sales_chart = $this->getSalesChartData();

        return view('admin.dashboard', compact(
            'stats',
            'recent_orders',
            'top_products',
            'recent_users',
            'collection_points_stats',
            'sales_chart'
        ));
    }

    private function calculateGrowth($model, $column = null)
    {
        $currentMonth = $column
            ? $model::whereMonth('created_at', now()->month)->sum($column)
            : $model::whereMonth('created_at', now()->month)->count();

        $lastMonth = $column
            ? $model::whereMonth('created_at', now()->subMonth()->month)->sum($column)
            : $model::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($lastMonth == 0) {
            return 100;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    private function getSalesChartData()
    {
        $days = 7;
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $dailySales = Order::whereDate('created_at', $date->toDateString())
                ->with('products')
                ->get()
                ->sum(function ($order) {
                    return $order->products->sum(function ($product) {
                        return $product->pivot->quantite * $product->prix;
                    });
                });

            $data[] = $dailySales;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    // Post Reports Management
    public function reports()
    {
        $reports = PostReport::with(['post.user', 'reporter'])
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.post-reports.index', compact('reports'));
    }

    public function showReport($id)
    {
        $report = PostReport::with(['post.user', 'reporter'])->findOrFail($id);
        return view('admin.post-reports.show', compact('report'));
    }

    public function banUser(Request $request, $reportId)
    {
        $request->validate([
            'ban_duration' => 'required|integer|min:1|max:365',
            'ban_reason' => 'required|string|max:500',
        ]);

        $report = PostReport::with('post.user')->findOrFail($reportId);
        $user = $report->post->user;

        // Calculate ban expiration - cast to int explicitly
        $banDays = (int) $request->ban_duration;
        $bannedUntil = Carbon::now()->addDays($banDays);

        // Update user
        $user->update([
            'banned_until' => $bannedUntil,
            'ban_reason' => $request->ban_reason,
        ]);

        // Update report status
        $report->update([
            'status' => 'resolved',
            'admin_notes' => "User banned for {$request->ban_duration} days. Reason: {$request->ban_reason}",
        ]);

        // Create notification for banned user
        Notification::create([
            'user_id' => $user->id,
            'type' => 'ban',
            'data' => json_encode([
                'message' => "You have been banned until " . $bannedUntil->format('M d, Y') . ". Reason: {$request->ban_reason}",
                'banned_until' => $bannedUntil->toDateTimeString(),
                'reason' => $request->ban_reason,
            ]),
        ]);

        return back()->with('success', "User {$user->name} has been banned for {$request->ban_duration} days.");
    }

    public function resolveReport(Request $request, $reportId)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
            'action' => 'required|in:dismiss,warning',
        ]);

        $report = PostReport::findOrFail($reportId);

        $report->update([
            'status' => 'resolved',
            'admin_notes' => $request->admin_notes ?? 'Report reviewed and ' . $request->action . ' applied.',
        ]);

        if ($request->action === 'warning') {
            // Create warning notification for post author
            Notification::create([
                'user_id' => $report->post->user_id,
                'type' => 'warning',
                'data' => json_encode([
                    'message' => 'Your post has been reported and reviewed. Please follow community guidelines.',
                    'post_id' => $report->post_id,
                ]),
            ]);
        }

        return back()->with('success', 'Report has been resolved.');
    }

    public function deletePost($reportId)
    {
        $report = PostReport::with('post')->findOrFail($reportId);
        $post = $report->post;

        if ($post) {
            // Create notification for post author
            Notification::create([
                'user_id' => $post->user_id,
                'type' => 'warning',
                'data' => json_encode([
                    'message' => 'Your post has been removed by administrators for violating community guidelines.',
                ]),
            ]);

            $post->delete();
        }

        $report->update([
            'status' => 'resolved',
            'admin_notes' => 'Post deleted by administrator.',
        ]);

        return back()->with('success', 'Post has been deleted and user notified.');
    }

    // Event Management
    public function events()
    {
        $events = Event::orderBy('date', 'desc')->paginate(20);
        return view('admin.events.index', compact('events'));
    }
}
