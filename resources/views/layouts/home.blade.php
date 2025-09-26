@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Carousel Start -->
    <div class="container-fluid p-0 wow fadeIn" data-wow-delay="0.1s">
        <div class="owl-carousel header-carousel py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="carousel-text">
                            <h1 class="display-1 text-uppercase mb-3">Together for a Better Tomorrow</h1>
                            <p class="fs-5 mb-5">We believe in creating opportunities and empowering communities through education, healthcare, and sustainable development.</p>
                            <div class="d-flex">
                                <a class="btn btn-primary py-3 px-4 me-3" href="{{ route('donation') }}">Donate Now</a>
                                <a class="btn btn-secondary py-3 px-4" href="#!">Join Us Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="carousel-img">
                            <img class="w-100" src="{{ asset('img/carousel-1.jpg') }}" alt="Image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="carousel-text">
                            <h1 class="display-1 text-uppercase mb-3">Together, We Can End Hunger</h1>
                            <p class="fs-5 mb-5">No one should go to bed hungry. Your support helps us bring smiles, hope, and a brighter future to those in need.</p>
                            <div class="d-flex mt-4">
                                <a class="btn btn-primary py-3 px-4 me-3" href="{{ route('donation') }}">Donate Now</a>
                                <a class="btn btn-secondary py-3 px-4" href="#!">Join Us Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="carousel-img">
                            <img class="w-100" src="{{ asset('img/carousel-2.jpg') }}" alt="Image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Video Start -->
    <div class="container-fluid bg-primary mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <div class="row g-0">
                <div class="col-lg-11">
                    <div class="h-100 py-5 d-flex align-items-center">
                        <button type="button" class="btn-play" data-bs-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-bs-target="#videoModal">
                            <span></span>
                        </button>
                        <h3 class="ms-5 mb-0">Together, we can build a world where everyone has the chance to thrive.</h3>
                    </div>
                </div>
                <div class="d-none d-lg-block col-lg-1">
                    <div class="h-100 w-100 bg-secondary d-flex align-items-center justify-content-center">
                        <span class="text-white" style="transform: rotate(-90deg);">Scroll Down</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Video End -->

    <!-- Video Modal Start -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Youtube Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ratio ratio-16x9">
                        <iframe class="embed-responsive-item" src="" id="video" allowfullscreen allowscriptaccess="always" allow="autoplay"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Video Modal End -->

    <!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.2s">
                    <div class="about-img">
                        <img class="img-fluid w-100" src="{{ asset('img/about.jpg') }}" alt="Image">
                    </div>
                </div>
                <div class="col-lg-6">
                    <p class="section-title bg-white text-start text-primary pe-3">About Us</p>
                    <h1 class="display-6 mb-4 wow fadeIn" data-wow-delay="0.2s">Join Hands, Change the World</h1>
                    <p class="mb-4 wow fadeIn" data-wow-delay="0.3s">Every hand extended in kindness brings us closer to a world free from suffering. Be part of a global movement dedicated to building a future where equality and compassion thrive.</p>
                    <div class="row g-4 pt-2">
                        <div class="col-sm-6 wow fadeIn" data-wow-delay="0.4s">
                            <div class="h-100">
                                <h3>Our Mission</h3>
                                <p>Our mission is to uplift underprivileged communities by providing resources, education, and tools for growth.</p>
                                <p class="text-dark"><i class="fa fa-check text-primary me-2"></i>No one should go to bed hungry.</p>
                                <p class="text-dark"><i class="fa fa-check text-primary me-2"></i>We spread kindness and support.</p>
                                <p class="text-dark mb-0"><i class="fa fa-check text-primary me-2"></i>We can change someone’s life.</p>
                            </div>
                        </div>
                        <div class="col-sm-6 wow fadeIn" data-wow-delay="0.5s">
                            <div class="h-100 bg-primary p-4 text-center">
                                <p class="fs-5 text-dark">Through your donations, we spread kindness and support to children and families.</p>
                                <a class="btn btn-secondary py-2 px-4" href="{{ route('donation') }}">Donate Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Service Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-12 col-lg-4 col-xl-3 wow fadeIn" data-wow-delay="0.1s">
                    <div class="service-title">
                        <h1 class="display-6 mb-4">What We Do for Those in Need.</h1>
                        <p class="fs-5 mb-0">We work to bring smiles, hope, and a brighter future to those in need.</p>
                    </div>
                </div>
                <div class="col-md-12 col-lg-8 col-xl-9">
                    <div class="row g-5">
                        <div class="col-sm-6 col-md-4 wow fadeIn" data-wow-delay="0.1s">
                            <div class="service-item h-100">
                                <div class="btn-square bg-light mb-4">
                                    <i class="fa fa-droplet fa-2x text-secondary"></i>
                                </div>
                                <h3>Pure Water</h3>
                                <p class="mb-2">We’re creating programs that address urgent needs while fostering long-term solutions for sustainable change.</p>
                                <a href="{{ route('services') }}">Read More</a>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 wow fadeIn" data-wow-delay="0.3s">
                            <div class="service-item h-100">
                                <div class="btn-square bg-light mb-4">
                                    <i class="fa fa-hospital fa-2x text-secondary"></i>
                                </div>
                                <h3>Health Care</h3>
                                <p class="mb-2">We’re creating programs that address urgent needs while fostering long-term solutions for sustainable change.</p>
                                <a href="{{ route('services') }}">Read More</a>
                            </div>
                        </div>
                        <!-- Add remaining service items similarly -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->

    <!-- Features, Donation, Banner, Event, Donate, Team, Testimonial, Newsletter sections -->
    <!-- Add these sections similarly, replacing static links with Laravel routes (e.g., {{ route('donation') }}) and asset paths with {{ asset('path/to/file') }} -->

    <!-- Newsletter Start -->
    <div class="container-fluid bg-primary py-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 text-center wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="display-6 mb-4">Subscribe the Newsletter</h1>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <div class="position-relative w-100 mb-2">
                            <input class="form-control border-0 w-100 ps-4 pe-5" type="email" name="email" placeholder="Enter Your Email" style="height: 60px;" required>
                            <button type="submit" class="btn btn-lg-square shadow-none position-absolute top-0 end-0 mt-2 me-2"><i class="fa fa-paper-plane text-primary fs-4"></i></button>
                        </div>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <p class="mb-0">Don't worry, we won't spam you with emails.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Newsletter End -->
@endsection