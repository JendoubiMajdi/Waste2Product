@extends('layouts.app')

@section('title', 'Portfolio Details')
@section('bodyClass', 'portfolio-details-page')

@section('content')
<main>
  <section class="portfolio-details section">
    <div class="container">
      <h2>Portfolio Item</h2>
      <p>Details about the selected portfolio item.</p>
      <img src="/assets/img/portfolio/portfolio-portrait-1.webp" class="img-fluid" alt="">
    </div>
  </section>
</main>

@endsection
