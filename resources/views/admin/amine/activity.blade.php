@extends('back.layout')

@section('title', 'Forum Activity Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Forum Activity Dashboard</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $postCount }}</h3>
                                    <p class="mb-0">Total Posts</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $commentCount }}</h3>
                                    <p class="mb-0">Comments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $likeCount }}</h3>
                                    <p class="mb-0">Likes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $reportCount }}</h3>
                                    <p class="mb-0">Reports</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $userCount }}</h3>
                                    <p class="mb-0">Total Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-dark text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $postCount + $commentCount + $likeCount }}</h3>
                                    <p class="mb-0">Total Activity</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <!-- Activity Timeline Chart -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Activity Timeline (Last 7 Days)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="activityChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Distribution Pie Chart -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Activity Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="pieChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Users Table -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Most Active Users</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Posts</th>
                                                    <th>Comments</th>
                                                    <th>Likes</th>
                                                    <th>Total Activity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topUsers as $user)
                                                <tr>
                                                    <td>{{ $user->name }}</td>
                                                    <td><span class="badge bg-primary">{{ $user->posts_count }}</span></td>
                                                    <td><span class="badge bg-success">{{ $user->comments_count }}</span></td>
                                                    <td><span class="badge bg-info">{{ $user->likes_count }}</span></td>
                                                    <td><span class="badge bg-dark">{{ $user->posts_count + $user->comments_count + $user->likes_count }}</span></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for charts
    const last7Days = [];
    const postsData = [];
    const commentsData = [];
    const likesData = [];
    
    // Fill last 7 days
    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        last7Days.push(date.toLocaleDateString());
        
        // Find data for this date
        const postCount = @json($postsByDay).find(d => d.date === dateStr)?.count || 0;
        const commentCount = @json($commentsByDay).find(d => d.date === dateStr)?.count || 0;
        const likeCount = @json($likesByDay).find(d => d.date === dateStr)?.count || 0;
        
        postsData.push(postCount);
        commentsData.push(commentCount);
        likesData.push(likeCount);
    }

    // Activity Timeline Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: last7Days,
            datasets: [
                {
                    label: 'Posts',
                    data: postsData,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Comments',
                    data: commentsData,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Likes',
                    data: likesData,
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Daily Forum Activity'
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Activity Distribution Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Posts', 'Comments', 'Likes', 'Reports'],
            datasets: [{
                data: [{{ $postCount }}, {{ $commentCount }}, {{ $likeCount }}, {{ $reportCount }}],
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#17a2b8',
                    '#ffc107'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Activity Types'
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection