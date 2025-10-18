@extends('layouts.app')

@section('title', 'Home - Waste2Product')

@section('content')
<main class="main">
    <!-- Page Title -->
    <div class="page-title dark-background" style="background-image: url(/assets/img/page-title-bg.webp);">
        <div class="container position-relative">
            <h1>Welcome, {{ Auth::user()->name }}</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="/">Home</a></li>
                    <li class="current">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <!-- Main Content Section -->
    <section id="home-content" class="home-content section">
        <div class="container">
            <!-- Placeholder for future content -->
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>Your Dashboard</h2>
                    <p class="lead">Welcome to your personalized dashboard. More features coming soon!</p>
                </div>
            </div>
        </div>
    </section><!-- End Home Content Section -->
</main>
@endsection
