@extends('layouts.app')

@section('title', 'Admin - Forum Activity')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Forum Activity Dashboard</h2>
        <p>Monitor community engagement and activity</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Overall Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $totalPosts }}</h3>
                        <p class="mb-0">Total Posts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $totalComments }}</h3>
                        <p class="mb-0">Total Comments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $totalLikes }}</h3>
                        <p class="mb-0">Total Likes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $activeUsers }}</h3>
                        <p class="mb-0">Active Users (7d)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Charts -->
        <div class="row mb-4">
            <!-- Posts Activity -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-file-post"></i> Posts Activity (Last 7 Days)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="postsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Comments Activity -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots"></i> Comments Activity (Last 7 Days)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="commentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Likes Activity -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-heart"></i> Likes Activity (Last 7 Days)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="likesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Contributors -->
        <div class="row">
            <!-- Top Posters -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-trophy"></i> Top Posters
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($topPosters as $index => $poster)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    <div>
                                        <strong>{{ $poster->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $poster->email }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $poster->posts_count }} posts</span>
                            </div>
                            @empty
                            <p class="text-muted text-center py-3">No data available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Commenters -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-left-text"></i> Top Commenters
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($topCommenters as $index => $commenter)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2">{{ $index + 1 }}</span>
                                    <div>
                                        <strong>{{ $commenter->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $commenter->email }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-success rounded-pill">{{ $commenter->comments_count }} comments</span>
                            </div>
                            @empty
                            <p class="text-muted text-center py-3">No data available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Liked Users -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-heart-fill"></i> Most Liked Users
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($mostLikedUsers as $index => $user)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-danger me-2">{{ $index + 1 }}</span>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-danger rounded-pill">{{ $user->total_likes }} likes</span>
                            </div>
                            @empty
                            <p class="text-muted text-center py-3">No data available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Posts Chart
const postsCtx = document.getElementById('postsChart').getContext('2d');
new Chart(postsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($postsByDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))) !!},
        datasets: [{
            label: 'Posts',
            data: {!! json_encode($postsByDay->pluck('count')) !!},
            borderColor: 'rgb(13, 110, 253)',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Comments Chart
const commentsCtx = document.getElementById('commentsChart').getContext('2d');
new Chart(commentsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($commentsByDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))) !!},
        datasets: [{
            label: 'Comments',
            data: {!! json_encode($commentsByDay->pluck('count')) !!},
            borderColor: 'rgb(25, 135, 84)',
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Likes Chart
const likesCtx = document.getElementById('likesChart').getContext('2d');
new Chart(likesCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($likesByDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))) !!},
        datasets: [{
            label: 'Likes',
            data: {!! json_encode($likesByDay->pluck('count')) !!},
            backgroundColor: 'rgba(220, 53, 69, 0.8)',
            borderColor: 'rgb(220, 53, 69)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>
@endsection
