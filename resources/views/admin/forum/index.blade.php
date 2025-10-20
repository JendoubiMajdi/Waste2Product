@extends('admin.layouts.app')

@section('title', 'Forum Analytics')

@push('styles')
<style>
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
  }

  .stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }

  .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 16px;
  }

  .stat-icon.primary {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
  }

  .stat-icon.info {
    background: rgba(14, 165, 233, 0.1);
    color: #0ea5e9;
  }

  .stat-icon.success {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
  }

  .stat-icon.warning {
    background: rgba(251, 146, 60, 0.1);
    color: #fb923c;
  }

  .stat-icon.danger {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
  }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 4px;
  }

  .stat-label {
    color: #6b7280;
    font-size: 14px;
  }

  .section-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
  }

  .section-title {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .table {
    margin-bottom: 0;
  }

  .table th {
    font-weight: 600;
    color: #6b7280;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
  }

  .table td {
    vertical-align: middle;
  }

  .user-cell {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .user-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
  }

  .badge-custom {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
  }

  .badge-primary-custom {
    background: rgba(0, 146, 126, 0.1);
    color: #00927E;
  }

  .badge-danger-custom {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
  }

  .badge-warning-custom {
    background: rgba(251, 146, 60, 0.1);
    color: #fb923c;
  }

  .btn-action-sm {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .quick-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-right: 12px;
    margin-bottom: 12px;
  }

  .quick-action-btn.primary {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
  }

  .quick-action-btn.danger {
    background: #ef4444;
    color: white;
  }

  .quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1"><i class="bi bi-graph-up"></i> Forum Analytics</h1>
      <p class="text-muted mb-0">Monitor community activity and engagement</p>
    </div>
    <div>
      <a href="{{ route('admin.post-reports') }}" class="quick-action-btn primary">
        <i class="bi bi-flag-fill"></i> Post Reports
        @if($stats['pending_reports'] > 0)
        <span class="badge bg-danger">{{ $stats['pending_reports'] }}</span>
        @endif
      </a>
      <a href="{{ route('admin.forum.banned') }}" class="quick-action-btn danger">
        <i class="bi bi-shield-x"></i> Banned Users
        @if($stats['active_bans'] > 0)
        <span class="badge bg-light text-dark">{{ $stats['active_bans'] }}</span>
        @endif
      </a>
    </div>
  </div>

  <!-- Statistics Grid -->
  <div class="stats-grid">
    <!-- Total Users -->
    <div class="stat-card">
      <div class="stat-icon primary">
        <i class="bi bi-people-fill"></i>
      </div>
      <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
      <div class="stat-label">Total Users</div>
      @if($stats['users_growth'] != 0)
      <div class="mt-2">
        <span class="badge-custom {{ $stats['users_growth'] > 0 ? 'badge-primary-custom' : 'badge-danger-custom' }}">
          <i class="bi bi-arrow-{{ $stats['users_growth'] > 0 ? 'up' : 'down' }}"></i>
          {{ abs($stats['users_growth']) }}% this month
        </span>
      </div>
      @endif
    </div>

    <!-- Total Posts -->
    <div class="stat-card">
      <div class="stat-icon info">
        <i class="bi bi-file-post-fill"></i>
      </div>
      <div class="stat-value">{{ number_format($stats['total_posts']) }}</div>
      <div class="stat-label">Total Posts</div>
      @if($stats['posts_growth'] != 0)
      <div class="mt-2">
        <span class="badge-custom {{ $stats['posts_growth'] > 0 ? 'badge-primary-custom' : 'badge-danger-custom' }}">
          <i class="bi bi-arrow-{{ $stats['posts_growth'] > 0 ? 'up' : 'down' }}"></i>
          {{ abs($stats['posts_growth']) }}% this month
        </span>
      </div>
      @endif
    </div>

    <!-- Total Comments -->
    <div class="stat-card">
      <div class="stat-icon info">
        <i class="bi bi-chat-dots-fill"></i>
      </div>
      <div class="stat-value">{{ number_format($stats['total_comments']) }}</div>
      <div class="stat-label">Total Comments</div>
      @if($stats['comments_growth'] != 0)
      <div class="mt-2">
        <span class="badge-custom {{ $stats['comments_growth'] > 0 ? 'badge-primary-custom' : 'badge-danger-custom' }}">
          <i class="bi bi-arrow-{{ $stats['comments_growth'] > 0 ? 'up' : 'down' }}"></i>
          {{ abs($stats['comments_growth']) }}% this month
        </span>
      </div>
      @endif
    </div>

    <!-- Total Likes -->
    <div class="stat-card">
      <div class="stat-icon danger">
        <i class="bi bi-heart-fill"></i>
      </div>
      <div class="stat-value">{{ number_format($stats['total_likes']) }}</div>
      <div class="stat-label">Total Likes</div>
    </div>

    <!-- Friendships -->
    <div class="stat-card">
      <div class="stat-icon success">
        <i class="bi bi-people"></i>
      </div>
      <div class="stat-value">{{ number_format($stats['total_friendships']) }}</div>
      <div class="stat-label">Friendships</div>
    </div>

    <!-- Messages -->
    <div class="stat-card">
      <div class="stat-icon info">
        <i class="bi bi-envelope-fill"></i>
      </div>
      <div class="stat-value">{{ number_format($stats['total_messages']) }}</div>
      <div class="stat-label">Messages Sent</div>
    </div>

    <!-- Pending Reports -->
    <div class="stat-card">
      <div class="stat-icon warning">
        <i class="bi bi-flag-fill"></i>
      </div>
      <div class="stat-value">{{ number_format($stats['pending_reports']) }}</div>
      <div class="stat-label">Pending Reports</div>
      @if($stats['pending_reports'] > 0)
      <div class="mt-2">
        <a href="{{ route('admin.post-reports') }}" class="btn btn-sm btn-warning btn-action-sm">
          Review Now
        </a>
      </div>
      @endif
    </div>

    <!-- Active Bans -->
    <div class="stat-card">
      <div class="stat-icon danger">
        <i class="bi bi-shield-x"></i>
      </div>
      <div class="stat-value">{{ number_format($stats['active_bans']) }}</div>
      <div class="stat-label">Active Bans</div>
    </div>
  </div>

  <!-- Activity Chart -->
  <div class="section-card">
    <div class="section-title">
      <i class="bi bi-graph-up"></i> Last 7 Days Activity Trends
    </div>
    <div style="height: 320px; position: relative;">
      <canvas id="activityChart"></canvas>
    </div>
  </div>

  <div class="row">
    <!-- Recent Activity -->
    <div class="col-md-4">
      <div class="section-card">
        <div class="section-title">
          <i class="bi bi-clock-history"></i> Last 7 Days
        </div>
        <div class="table-responsive">
          <table class="table">
            <tbody>
              <tr>
                <td><i class="bi bi-person-plus-fill text-primary me-2"></i> New Users</td>
                <td class="text-end">
                  <span class="badge-custom badge-primary-custom">+{{ $recentActivity['new_users'] }}</span>
                </td>
              </tr>
              <tr>
                <td><i class="bi bi-file-post-fill text-info me-2"></i> New Posts</td>
                <td class="text-end">
                  <span class="badge-custom badge-primary-custom">+{{ $recentActivity['new_posts'] }}</span>
                </td>
              </tr>
              <tr>
                <td><i class="bi bi-chat-fill text-info me-2"></i> New Comments</td>
                <td class="text-end">
                  <span class="badge-custom badge-primary-custom">+{{ $recentActivity['new_comments'] }}</span>
                </td>
              </tr>
              <tr>
                <td><i class="bi bi-people-fill text-success me-2"></i> New Friendships</td>
                <td class="text-end">
                  <span class="badge-custom badge-primary-custom">+{{ $recentActivity['new_friendships'] }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Posts by Visibility -->
      <div class="section-card">
        <div class="section-title">
          <i class="bi bi-eye-fill"></i> Posts by Visibility
        </div>
        @forelse($postsByVisibility as $visibility)
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-capitalize">{{ $visibility->visibility }}</span>
          <span class="badge-custom badge-primary-custom">{{ $visibility->count }}</span>
        </div>
        @empty
        <p class="text-muted text-center py-3">No posts yet</p>
        @endforelse
      </div>
    </div>

    <!-- Top Users -->
    <div class="col-md-4">
      <div class="section-card">
        <div class="section-title">
          <i class="bi bi-trophy-fill"></i> Most Active Users
        </div>
        <div class="table-responsive">
          <table class="table">
            <tbody>
              @forelse($topUsers as $index => $user)
              <tr>
                <td>
                  <div class="user-cell">
                    <div class="user-avatar-sm">
                      {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                      <div><strong>{{ $user->name }}</strong></div>
                      <small class="text-muted">{{ $user->posts_count }} posts · {{ $user->comments_count }} comments</small>
                    </div>
                  </div>
                </td>
                <td class="text-end">
                  <span class="badge-custom badge-warning-custom">#{{ $index + 1 }}</span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="2" class="text-center text-muted py-4">No active users yet</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Most Liked Posts -->
    <div class="col-md-4">
      <div class="section-card">
        <div class="section-title">
          <i class="bi bi-heart-fill"></i> Most Liked Posts
        </div>
        <div class="table-responsive">
          <table class="table">
            <tbody>
              @forelse($mostLikedPosts as $post)
              <tr>
                <td>
                  <div class="user-cell">
                    <div class="user-avatar-sm">
                      {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                    <div>
                      <div><small>{{ Str::limit($post->content, 45) }}</small></div>
                      <small class="text-muted">by {{ $post->user->name }}</small>
                    </div>
                  </div>
                </td>
                <td class="text-end">
                  <span class="badge-custom badge-danger-custom">
                    <i class="bi bi-heart-fill"></i> {{ $post->likes_count }}
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td class="text-center text-muted py-4">No liked posts yet</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Most Commented Posts -->
      <div class="section-card">
        <div class="section-title">
          <i class="bi bi-chat-dots-fill"></i> Most Commented
        </div>
        <div class="table-responsive">
          <table class="table">
            <tbody>
              @forelse($mostCommentedPosts as $post)
              <tr>
                <td>
                  <div class="user-cell">
                    <div class="user-avatar-sm">
                      {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                    <div>
                      <div><small>{{ Str::limit($post->content, 45) }}</small></div>
                      <small class="text-muted">by {{ $post->user->name }}</small>
                    </div>
                  </div>
                </td>
                <td class="text-end">
                  <span class="badge-custom badge-primary-custom">
                    <i class="bi bi-chat-fill"></i> {{ $post->comments_count }}
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td class="text-center text-muted py-4">No commented posts yet</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  // Activity Chart
  const ctx = document.getElementById('activityChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: @json($activityChartData['labels']),
      datasets: [
        {
          label: 'Posts',
          data: @json($activityChartData['posts']),
          borderColor: '#0ea5e9',
          backgroundColor: 'rgba(14, 165, 233, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#0ea5e9',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
        },
        {
          label: 'Comments',
          data: @json($activityChartData['comments']),
          borderColor: '#8b5cf6',
          backgroundColor: 'rgba(139, 92, 246, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#8b5cf6',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
        },
        {
          label: 'New Users',
          data: @json($activityChartData['users']),
          borderColor: '#00927E',
          backgroundColor: 'rgba(0, 146, 126, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#00927E',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            usePointStyle: true,
            padding: 12,
            font: {
              size: 12,
              weight: '500',
            },
          },
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          cornerRadius: 8,
          titleFont: {
            size: 14,
            weight: '600',
          },
          bodyFont: {
            size: 13,
          },
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': ' + context.parsed.y;
            }
          }
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.05)',
          },
          ticks: {
            font: {
              size: 12,
            },
            precision: 0,
          },
        },
        x: {
          grid: {
            display: false,
          },
          ticks: {
            font: {
              size: 12,
            },
          },
        },
      },
    },
  });
</script>
@endpush
