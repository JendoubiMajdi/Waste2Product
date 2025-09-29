<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Back Office')</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('back/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('back/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('back/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('back/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('back/images/favicon.png') }}" />
</head>
<body>
    <div class="container-scroller">
        {{-- Navbar --}}
        @include('back.partials.navbar')

        <div class="container-fluid page-body-wrapper">
            {{-- Sidebar --}}
            @include('back.partials.sidebar')

            <div class="main-panel">
                <div class="content-wrapper">
                    {{-- Page Content --}}
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('back/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('back/js/off-canvas.js') }}"></script>
    <script src="{{ asset('back/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('back/js/template.js') }}"></script>
    <script src="{{ asset('back/js/settings.js') }}"></script>
    <script src="{{ asset('back/js/todolist.js') }}"></script>
</body>
</html>
