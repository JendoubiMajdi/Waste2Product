@extends('layouts.app')

@section('title', 'AI Analytics - ' . $event->title)

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2><i class="bi bi-robot"></i> AI Event Analytics</h2>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.show', $event) }}">{{ Str::limit($event->title, 30) }}</a></li>
                <li class="breadcrumb-item active">AI Analytics</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Event Header -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-2">{{ $event->title }}</h3>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar3"></i> {{ $event->event_date->format('F j, Y') }} 
                            <span class="ms-3"><i class="bi bi-people"></i> {{ $event->participants()->count() }} Participants</span>
                            <span class="ms-3"><i class="bi bi-chat-dots"></i> {{ $event->feedback()->count() }} Feedback</span>
                        </p>
                    </div>
                    <div class="text-end">
                        <form action="{{ route('events.analytics.generate', $event) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise"></i> Regenerate Analysis
                            </button>
                        </form>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-left"></i> Back to Event
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($insights)
        <!-- Achievement Badge -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-center text-white py-5">
                        <div style="font-size: 5rem;">{{ $insights['achievement']['badge'] }}</div>
                        <h2 class="mt-3 mb-2">{{ $insights['achievement']['title'] }}</h2>
                        <p class="lead mb-3">{{ $insights['achievement']['description'] }}</p>
                        <div class="badge bg-white text-dark px-4 py-2 fs-5">
                            Success Score: {{ $insights['success_score'] }}/100
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sentiment Overview -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-warning mb-3" style="font-size: 3rem;">
                            {{ $event->ai_sentiment_score >= 0.8 ? '😍' : ($event->ai_sentiment_score >= 0.6 ? '😊' : ($event->ai_sentiment_score >= 0.4 ? '😐' : '😟')) }}
                        </div>
                        <h5 class="card-title">Sentiment Score</h5>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-{{ $event->ai_sentiment_score >= 0.7 ? 'success' : ($event->ai_sentiment_score >= 0.4 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $event->ai_sentiment_score * 100 }}%">
                                {{ round($event->ai_sentiment_score * 100) }}%
                            </div>
                        </div>
                        <p class="text-muted mb-0">{{ $event->ai_sentiment_summary }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3" style="font-size: 3rem;">⭐</div>
                        <h5 class="card-title">Average Rating</h5>
                        <h2 class="text-warning mb-2">{{ $event->averageRating() }}/5</h2>
                        <div class="text-warning mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($event->averageRating()))
                                    <i class="bi bi-star-fill"></i>
                                @elseif($i - 0.5 <= $event->averageRating())
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </div>
                        <small class="text-muted">From {{ $event->feedback()->count() }} reviews</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3" style="font-size: 3rem;">📊</div>
                        <h5 class="card-title">Engagement Level</h5>
                        <h2 class="mb-2 text-{{ $insights['engagement_metrics']['engagement_level'] === 'High' ? 'success' : ($insights['engagement_metrics']['engagement_level'] === 'Moderate' ? 'warning' : 'secondary') }}">
                            {{ $insights['engagement_metrics']['engagement_level'] }}
                        </h2>
                        <p class="mb-1"><strong>{{ $insights['engagement_metrics']['feedback_rate'] }}</strong> Feedback Rate</p>
                        <small class="text-muted">{{ $insights['engagement_metrics']['feedback_received'] }} of {{ $insights['engagement_metrics']['participants'] }} participants</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Rating Distribution</h5>
                    </div>
                    <div class="card-body">
                        @foreach([5, 4, 3, 2, 1] as $star)
                            @php
                                $count = $insights['rating_distribution'][$star] ?? 0;
                                $total = $event->feedback()->count();
                                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>
                                        @for($i = 0; $i < $star; $i++)
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @endfor
                                    </span>
                                    <span class="text-muted">{{ $count }} ({{ round($percentage) }}%)</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-emoji-smile"></i> Sentiment Indicators</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-3">
                                    <div class="text-success" style="font-size: 2.5rem;">😊</div>
                                    <h4 class="text-success mt-2">{{ json_decode($event->ai_insights, true)['sentiment_ratio']['positive'] ?? 0 }}</h4>
                                    <small class="text-muted">Positive</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3">
                                    <div class="text-secondary" style="font-size: 2.5rem;">😐</div>
                                    <h4 class="text-secondary mt-2">{{ json_decode($event->ai_insights, true)['sentiment_ratio']['neutral'] ?? 0 }}</h4>
                                    <small class="text-muted">Neutral</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3">
                                    <div class="text-danger" style="font-size: 2.5rem;">😞</div>
                                    <h4 class="text-danger mt-2">{{ json_decode($event->ai_insights, true)['sentiment_ratio']['negative'] ?? 0 }}</h4>
                                    <small class="text-muted">Negative</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-check-circle"></i> Key Strengths</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach($insights['strengths'] as $strength)
                                <li class="mb-2">
                                    <i class="bi bi-check-lg text-success"></i> {{ $strength }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Areas for Improvement</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach($insights['improvements'] as $improvement)
                                <li class="mb-2">
                                    <i class="bi bi-arrow-right text-warning"></i> {{ $improvement }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100 border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-lightbulb"></i> Recommendations</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach($insights['recommendations'] as $recommendation)
                                <li class="mb-2">
                                    <i class="bi bi-arrow-right-circle text-info"></i> {{ $recommendation }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Report Summary -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-file-text"></i> AI-Generated Report Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="bg-light p-4 rounded" style="white-space: pre-line; font-family: monospace;">{{ $reportSummary }}</div>
                        <div class="mt-3 text-end">
                            <button class="btn btn-outline-primary" onclick="copyReport()">
                                <i class="bi bi-clipboard"></i> Copy Report
                            </button>
                            <button class="btn btn-outline-success" onclick="window.print()">
                                <i class="bi bi-printer"></i> Print Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-muted text-center mt-4">
            <small>
                <i class="bi bi-clock-history"></i> Analysis generated: {{ $event->ai_analyzed_at->diffForHumans() }}
            </small>
        </div>

        @else
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-robot" style="font-size: 4rem;"></i>
            <h3 class="mt-3">No Analysis Available</h3>
            <p class="mb-3">This event needs feedback before AI analytics can be generated.</p>
            <form action="{{ route('events.analytics.generate', $event) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-stars"></i> Generate AI Analysis
                </button>
            </form>
        </div>
        @endif
    </div>
</section>

<script>
function copyReport() {
    const reportText = document.querySelector('.bg-light.p-4').textContent;
    navigator.clipboard.writeText(reportText).then(() => {
        alert('Report copied to clipboard!');
    });
}
</script>

<style>
@media print {
    .btn, nav, .breadcrumbs { display: none !important; }
}
</style>
@endsection
