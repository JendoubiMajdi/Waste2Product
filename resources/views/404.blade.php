@extends('layouts.app')

@section('title', '404 - Not Found')
@section('bodyClass', 'page-404')

@section('content')
<main>
  <section class="notfound section">
    <div class="container text-center py-5">
      <h1>404</h1>
      <p>Page not found.</p>
      <a href="/" class="btn-primary">Go Home</a>
    </div>
  </section>
</main>

@endsection
