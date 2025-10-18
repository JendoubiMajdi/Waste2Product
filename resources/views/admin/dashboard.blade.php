@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
    <!-- Stats Cards -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(0, 146, 126, 0.1); color: var(--primary-color);">
                <span class="iconify" data-icon="mingcute:user-4-fill"></span>
            </div>
            <div class="stat-label">Total Users</div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-change positive">
                <span class="iconify" data-icon="mingcute:arrow-up-fill"></span>
                +{{ $stats['users_growth'] }}% from last month
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(208, 67, 196, 0.1); color: var(--secondary-color);">
                <span class="iconify" data-icon="ant-design:shopping-cart-outlined"></span>
            </div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-change positive">
                <span class="iconify" data-icon="mingcute:arrow-up-fill"></span>
                +{{ $stats['orders_growth'] }}% from last month
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(23, 174, 19, 0.1); color: var(--success-color);">
                <span class="iconify" data-icon="mingcute:currency-dollar-fill"></span>
            </div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">{{ number_format($stats['total_revenue']) }} TND</div>
            <div class="stat-change positive">
                <span class="iconify" data-icon="mingcute:arrow-up-fill"></span>
                +{{ $stats['revenue_growth'] }}% from last month
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(255, 159, 67, 0.1); color: var(--warning-color);">
                <span class="iconify" data-icon="mdi:recycle"></span>
            </div>
            <div class="stat-label">Total Waste (kg)</div>
            <div class="stat-value">{{ number_format($stats['total_waste']) }}</div>
            <div class="stat-change positive">
                <span class="iconify" data-icon="mingcute:arrow-up-fill"></span>
                +{{ $stats['waste_growth'] }}% from last month
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="col-12 col-xl-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="admin-card-title">Sales Overview</h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Week</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary active">Month</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Year</button>
                    </div>
                </div>
            </div>
            <div class="admin-card-body">
                <canvas id="salesChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-12 col-xl-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Quick Actions</h5>
            </div>
            <div class="admin-card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between">
                        <span><span class="iconify me-2" data-icon="clarity:user-line"></span>Manage Users</span>
                        <span class="iconify" data-icon="mingcute:right-line"></span>
                    </a>
                    <a href="{{ route('admin.products') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between">
                        <span><span class="iconify me-2" data-icon="lets-icons:bag-alt-light"></span>Manage Products</span>
                        <span class="iconify" data-icon="mingcute:right-line"></span>
                    </a>
                    <a href="{{ route('admin.orders') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between">
                        <span><span class="iconify me-2" data-icon="ant-design:shopping-cart-outlined"></span>View Orders</span>
                        <span class="iconify" data-icon="mingcute:right-line"></span>
                    </a>
                    <a href="{{ route('admin.collection-points') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between">
                        <span><span class="iconify me-2" data-icon="mdi:map-marker-multiple"></span>Collection Points</span>
                        <span class="iconify" data-icon="mingcute:right-line"></span>
                    </a>
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between">
                        <span><span class="iconify me-2" data-icon="lucide:line-chart"></span>View Reports</span>
                        <span class="iconify" data-icon="mingcute:right-line"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-12 col-xl-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="admin-card-title">Recent Orders</h5>
                    <a href="{{ route('admin.orders') }}" class="text-decoration-none" style="font-size: 14px;">View All</a>
                </div>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--bg-light);">
                            <tr>
                                <th style="padding: 12px 24px; font-weight: 600; font-size: 13px;">Order ID</th>
                                <th style="padding: 12px 24px; font-weight: 600; font-size: 13px;">Customer</th>
                                <th style="padding: 12px 24px; font-weight: 600; font-size: 13px;">Date</th>
                                <th style="padding: 12px 24px; font-weight: 600; font-size: 13px;">Amount</th>
                                <th style="padding: 12px 24px; font-weight: 600; font-size: 13px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_orders as $order)
                            <tr>
                                <td style="padding: 16px 24px; font-size: 14px;">#{{ $order->id }}</td>
                                <td style="padding: 16px 24px; font-size: 14px;">{{ $order->client->name ?? 'N/A' }}</td>
                                <td style="padding: 16px 24px; font-size: 14px;">{{ $order->created_at->format('M d, Y') }}</td>
                                <td style="padding: 16px 24px; font-size: 14px; font-weight: 600;">{{ number_format($order->total, 2) }} TND</td>
                                <td style="padding: 16px 24px;">
                                    @if($order->statut === 'livré')
                                        <span class="badge bg-success">Delivered</span>
                                    @elseif($order->statut === 'en cours')
                                        <span class="badge bg-warning">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($order->statut) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No orders found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-12 col-xl-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Top Products</h5>
            </div>
            <div class="admin-card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($top_products as $product)
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded" style="width: 48px; height: 48px; background-color: var(--bg-light); display: flex; align-items: center; justify-content: center;">
                            <span class="iconify" data-icon="lets-icons:bag-alt-light" style="font-size: 24px; color: var(--primary-color);"></span>
                        </div>
                        <div class="flex-grow-1">
                            <div style="font-size: 14px; font-weight: 600;">{{ $product->nom }}</div>
                            <div style="font-size: 13px; color: var(--text-secondary);">{{ $product->sales_count }} sales</div>
                        </div>
                        <div style="font-size: 14px; font-weight: 600; color: var(--primary-color);">
                            {{ number_format($product->prix) }} TND
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">No products found</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="col-12 col-xl-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="admin-card-title">Recent Users</h5>
                    <a href="{{ route('admin.users') }}" class="text-decoration-none" style="font-size: 14px;">View All</a>
                </div>
            </div>
            <div class="admin-card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($recent_users as $user)
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: white; font-weight: 600;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div style="font-size: 14px; font-weight: 600;">{{ $user->name }}</div>
                            <div style="font-size: 13px; color: var(--text-secondary);">{{ $user->email }}</div>
                        </div>
                        <div>
                            <span class="badge" style="background-color: rgba(0, 146, 126, 0.1); color: var(--primary-color);">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">No users found</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Points Overview -->
    <div class="col-12 col-xl-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="admin-card-title">Collection Points</h5>
                    <a href="{{ route('admin.collection-points') }}" class="text-decoration-none" style="font-size: 14px;">View All</a>
                </div>
            </div>
            <div class="admin-card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center p-3 rounded" style="background-color: var(--bg-light);">
                            <div style="font-size: 24px; font-weight: 700; color: var(--primary-color);">{{ $collection_points_stats['total'] }}</div>
                            <div style="font-size: 13px; color: var(--text-secondary);">Total Points</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 rounded" style="background-color: var(--bg-light);">
                            <div style="font-size: 24px; font-weight: 700; color: var(--success-color);">{{ $collection_points_stats['active'] }}</div>
                            <div style="font-size: 13px; color: var(--text-secondary);">Active</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <canvas id="collectionPointsChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($sales_chart['labels']) !!},
            datasets: [{
                label: 'Sales',
                data: {!! json_encode($sales_chart['data']) !!},
                borderColor: '#00927E',
                backgroundColor: 'rgba(0, 146, 126, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#E5E7EB'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Collection Points Chart
    const cpCtx = document.getElementById('collectionPointsChart').getContext('2d');
    new Chart(cpCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Inactive'],
            datasets: [{
                data: [{{ $collection_points_stats['active'] }}, {{ $collection_points_stats['inactive'] }}],
                backgroundColor: ['#17AE13', '#E5E7EB']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
