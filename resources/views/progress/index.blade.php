@extends('layouts.app')

@section('title', 'My Progress')

@section('content')
<section class="py-5" style="min-height: 100vh; background: linear-gradient(135deg, #0a3223 0%, #12a16b 100%);">
    <div class="container">
        <!-- Header -->
        <div class="text-center text-white mb-5">
            <h1 class="display-4 fw-bold mb-3">My Progress</h1>
            <p class="lead">Track your achievements and compete with others!</p>
        </div>

        <!-- User Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card border-0 shadow-lg h-100" style="border-radius: 15px;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3" style="font-size: 48px; color: #12a16b;">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <h3 class="fw-bold mb-2" style="color: #12a16b;">{{ $user->points }}</h3>
                        <p class="text-muted mb-0">Total Points</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-lg h-100" style="border-radius: 15px;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3" style="font-size: 48px; color: #f59e0b;">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <h3 class="fw-bold mb-2" style="color: #f59e0b;">{{ $approvedSubmissions }}</h3>
                        <p class="text-muted mb-0">Challenges Completed</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-lg h-100" style="border-radius: 15px;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3" style="font-size: 48px; color: #10b981;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h3 class="fw-bold mb-2" style="color: #10b981;">{{ $approvalRate }}%</h3>
                        <p class="text-muted mb-0">Approval Rate</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-lg h-100" style="border-radius: 15px;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3" style="font-size: 48px; color: #{{ $user->badge_color ?? '6b7280' }};">
                            <i class="bi bi-{{ $user->badge_icon ?? 'award' }}"></i>
                        </div>
                        <h3 class="fw-bold mb-2" style="color: #{{ $user->badge_color ?? '6b7280' }};">{{ ucfirst($user->badge ?? 'beginner') }}</h3>
                        <p class="text-muted mb-0">Current Badge</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Podium Section -->
        <div class="card border-0 shadow-lg mb-5" style="border-radius: 20px; background: white;">
            <div class="card-body p-5">
                <h2 class="text-center fw-bold mb-5" style="color: #1a1a1a;">🏆 Top Champions 🏆</h2>
                
                <div class="row align-items-end justify-content-center" style="min-height: 350px;">
                    <!-- 2nd Place -->
                    @if(isset($topUsers[1]))
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        <div class="podium-item" style="animation: slideInUp 0.6s ease-out 0.2s both;">
                            <div class="avatar mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #c0c0c0, #a8a8a8); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; color: white; font-weight: bold; box-shadow: 0 8px 20px rgba(192, 192, 192, 0.4);">
                                {{ substr($topUsers[1]->name, 0, 1) }}
                            </div>
                            <h5 class="fw-bold mb-1">{{ $topUsers[1]->name }}</h5>
                            <p class="text-muted mb-2">{{ $topUsers[1]->points }} points</p>
                            <div class="badge bg-secondary" style="font-size: 14px; padding: 8px 16px; border-radius: 20px;">
                                <i class="bi bi-award"></i> 2nd Place
                            </div>
                            <div class="podium-base mx-auto mt-3" style="width: 100%; height: 120px; background: linear-gradient(135deg, #c0c0c0, #a8a8a8); border-radius: 10px 10px 0 0; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold;">
                                2
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 1st Place -->
                    @if(isset($topUsers[0]))
                    <div class="col-md-4 text-center mb-4 mb-md-0" style="position: relative; z-index: 2;">
                        <div class="podium-item" style="animation: slideInUp 0.6s ease-out;">
                            <div class="crown mb-3" style="font-size: 40px;">👑</div>
                            <div class="avatar mx-auto mb-3" style="width: 100px; height: 100px; background: linear-gradient(135deg, #ffd700, #ffed4e); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; color: white; font-weight: bold; box-shadow: 0 10px 30px rgba(255, 215, 0, 0.5); border: 4px solid white;">
                                {{ substr($topUsers[0]->name, 0, 1) }}
                            </div>
                            <h4 class="fw-bold mb-1">{{ $topUsers[0]->name }}</h4>
                            <p class="text-muted mb-2">{{ $topUsers[0]->points }} points</p>
                            <div class="badge" style="background: linear-gradient(135deg, #ffd700, #ffed4e); color: #1a1a1a; font-size: 14px; padding: 8px 16px; border-radius: 20px;">
                                <i class="bi bi-trophy-fill"></i> Champion
                            </div>
                            <div class="podium-base mx-auto mt-3" style="width: 100%; height: 160px; background: linear-gradient(135deg, #ffd700, #ffed4e); border-radius: 10px 10px 0 0; display: flex; align-items: center; justify-content: center; color: white; font-size: 56px; font-weight: bold; box-shadow: 0 -5px 20px rgba(255, 215, 0, 0.3);">
                                1
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 3rd Place -->
                    @if(isset($topUsers[2]))
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        <div class="podium-item" style="animation: slideInUp 0.6s ease-out 0.4s both;">
                            <div class="avatar mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #cd7f32, #b87333); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; color: white; font-weight: bold; box-shadow: 0 8px 20px rgba(205, 127, 50, 0.4);">
                                {{ substr($topUsers[2]->name, 0, 1) }}
                            </div>
                            <h5 class="fw-bold mb-1">{{ $topUsers[2]->name }}</h5>
                            <p class="text-muted mb-2">{{ $topUsers[2]->points }} points</p>
                            <div class="badge" style="background: linear-gradient(135deg, #cd7f32, #b87333); color: white; font-size: 14px; padding: 8px 16px; border-radius: 20px;">
                                <i class="bi bi-award"></i> 3rd Place
                            </div>
                            <div class="podium-base mx-auto mt-3" style="width: 100%; height: 100px; background: linear-gradient(135deg, #cd7f32, #b87333); border-radius: 10px 10px 0 0; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold;">
                                3
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Leaderboard Table -->
        <div class="card border-0 shadow-lg" style="border-radius: 20px; background: white;">
            <div class="card-body p-4">
                <h3 class="fw-bold mb-4" style="color: #1a1a1a;">
                    <i class="bi bi-bar-chart-fill text-primary"></i> Full Leaderboard
                </h3>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: linear-gradient(135deg, #0a3223, #12a16b); color: white;">
                            <tr>
                                <th style="border: none; padding: 16px;">Rank</th>
                                <th style="border: none; padding: 16px;">User</th>
                                <th style="border: none; padding: 16px;">Badge</th>
                                <th style="border: none; padding: 16px;">Points</th>
                                <th style="border: none; padding: 16px;">Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topUsers as $index => $topUser)
                                <tr class="{{ $topUser->id === $user->id ? 'table-primary' : '' }}" style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 16px; font-weight: 600;">
                                        @if($index === 0)
                                            <span class="badge bg-warning text-dark">🥇 1</span>
                                        @elseif($index === 1)
                                            <span class="badge bg-secondary">🥈 2</span>
                                        @elseif($index === 2)
                                            <span class="badge" style="background: #cd7f32; color: white;">🥉 3</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px;">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #{{ $topUser->badge_color ?? '6b7280' }}, #12a16b); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                {{ substr($topUser->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $topUser->name }}</div>
                                                @if($topUser->id === $user->id)
                                                    <small class="text-primary">(You)</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 16px;">
                                        <span class="badge" style="background: #{{ $topUser->badge_color ?? 'e5e7eb' }}; color: {{ $topUser->badge ? 'white' : '#6b7280' }}; padding: 6px 12px; border-radius: 20px;">
                                            <i class="bi bi-{{ $topUser->badge_icon ?? 'award' }}"></i> {{ ucfirst($topUser->badge ?? 'beginner') }}
                                        </span>
                                    </td>
                                    <td style="padding: 16px; font-weight: 600; color: #667eea;">{{ $topUser->points }} pts</td>
                                    <td style="padding: 16px;">{{ App\Models\ChallengeSubmission::where('user_id', $topUser->id)->where('status', 'approved')->count() }} challenges</td>
                                </tr>
                            @endforeach

                            @if($userRank > 10)
                                <tr>
                                    <td colspan="5" style="padding: 8px; text-align: center;">
                                        <small class="text-muted">...</small>
                                    </td>
                                </tr>
                                <tr class="table-primary" style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 16px; font-weight: 600;">
                                        <span class="badge bg-light text-dark">{{ $userRank }}</span>
                                    </td>
                                    <td style="padding: 16px;">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #{{ $user->badge_color ?? '6b7280' }}, #12a16b); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                <small class="text-primary">(You)</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 16px;">
                                        <span class="badge" style="background: #{{ $user->badge_color ?? 'e5e7eb' }}; color: {{ $user->badge ? 'white' : '#6b7280' }}; padding: 6px 12px; border-radius: 20px;">
                                            <i class="bi bi-{{ $user->badge_icon ?? 'award' }}"></i> {{ ucfirst($user->badge ?? 'beginner') }}
                                        </span>
                                    </td>
                                    <td style="padding: 16px; font-weight: 600; color: #12a16b;">{{ $user->points }} pts</td>
                                    <td style="padding: 16px;">{{ $approvedSubmissions }} challenges</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-5">
            <a href="{{ route('home') }}" class="btn btn-light btn-lg" style="border-radius: 50px; padding: 12px 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</section>

<style>
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table-hover tbody tr:hover {
        background-color: rgba(18, 161, 107, 0.05);
        transform: scale(1.01);
        transition: all 0.2s ease;
    }

    .podium-item {
        transition: transform 0.3s ease;
    }

    .podium-item:hover {
        transform: translateY(-10px);
    }
</style>
@endsection
