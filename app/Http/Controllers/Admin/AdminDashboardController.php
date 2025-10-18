<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollectionPoint;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Waste;

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
}
