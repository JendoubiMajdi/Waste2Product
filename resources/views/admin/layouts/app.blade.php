<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Iconify -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <!-- Admin Custom Styles -->
    <style>
        :root {
            --sidebar-width: 240px;
            --sidebar-collapsed-width: 110px;
            --topbar-height: 70px;
            --primary-color: #00927E;
            --primary-dark: #007662;
            --primary-light: #00A391;
            --secondary-color: #D043C4;
            --success-color: #17AE13;
            --warning-color: #FF9F43;
            --danger-color: #F43F5E;
            --info-color: #0EA5E9;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --bg-light: #F9FAFB;
            --bg-white: #FFFFFF;
            --border-color: #E5E7EB;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
        }

        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .admin-content {
            flex: 1;
            margin-left: var(--sidebar-collapsed-width);
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .admin-content.sidebar-open {
            margin-left: var(--sidebar-width);
        }

        .admin-main {
            flex: 1;
            padding: 28px;
            margin-top: var(--topbar-height);
        }

        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-collapsed-width);
            height: 100vh;
            background: var(--bg-white);
            border-right: 1px solid var(--border-color);
            transition: width 0.3s ease;
            z-index: 1000;
            overflow: hidden;
        }

        .admin-sidebar.open {
            width: var(--sidebar-width);
        }

        .sidebar-logo {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-logo img {
            height: 40px;
            transition: all 0.3s ease;
        }

        .sidebar-nav {
            padding: 20px 0;
            overflow-y: auto;
            height: calc(100vh - var(--topbar-height));
        }

        .nav-item {
            margin: 4px 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            background-color: var(--bg-light);
            color: var(--primary-color);
        }

        .nav-link.active {
            background-color: rgba(0, 146, 126, 0.1);
            color: var(--primary-color);
            font-weight: 600;
        }

        .nav-link .iconify {
            font-size: 24px;
            min-width: 24px;
        }

        .nav-link span {
            margin-left: 16px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .admin-sidebar.open .nav-link span {
            opacity: 1;
        }

        /* Topbar */
        .admin-topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-collapsed-width);
            height: var(--topbar-height);
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 999;
            transition: left 0.3s ease;
        }

        .admin-topbar.sidebar-open {
            left: var(--sidebar-width);
        }

        .topbar-toggle {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .topbar-toggle:hover {
            background-color: var(--bg-light);
            color: var(--primary-color);
        }

        .topbar-search {
            flex: 1;
            max-width: 600px;
            margin: 0 24px;
        }

        .topbar-search input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-light);
            font-size: 14px;
            transition: all 0.2s;
        }

        .topbar-search input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: var(--bg-white);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            position: relative;
            transition: all 0.2s;
        }

        .topbar-icon-btn:hover {
            background-color: var(--bg-light);
            color: var(--primary-color);
        }

        .topbar-icon-btn .badge {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background-color: var(--danger-color);
            border-radius: 50%;
            border: 2px solid var(--bg-white);
        }

        /* Cards */
        .admin-card {
            background: var(--bg-white);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: all 0.2s;
        }

        .admin-card:hover {
            box-shadow: var(--shadow-md);
        }

        .admin-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-card-body {
            padding: 24px;
        }

        .admin-card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        /* Stats Cards */
        .stat-card {
            padding: 24px;
            border-radius: 12px;
            background: var(--bg-white);
            border: 1px solid var(--border-color);
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin: 8px 0;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .stat-card .stat-change {
            font-size: 13px;
            margin-top: 8px;
        }

        .stat-change.positive {
            color: var(--success-color);
        }

        .stat-change.negative {
            color: var(--danger-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                left: -240px;
            }

            .admin-sidebar.open {
                left: 0;
            }

            .admin-content {
                margin-left: 0;
            }

            .admin-topbar {
                left: 0;
            }

            .topbar-search {
                display: none;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        @include('admin.layouts.sidebar')

        <div class="admin-content" id="adminContent">
            @include('admin.layouts.topbar')

            <main class="admin-main">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            @include('admin.layouts.footer')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const content = document.getElementById('adminContent');
        const topbar = document.getElementById('adminTopbar');
        const toggleBtn = document.getElementById('sidebarToggle');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            content.classList.toggle('sidebar-open');
            topbar.classList.toggle('sidebar-open');
            
            // Save state to localStorage
            localStorage.setItem('sidebarOpen', sidebar.classList.contains('open'));
        });

        // Restore sidebar state
        if (localStorage.getItem('sidebarOpen') === 'true') {
            sidebar.classList.add('open');
            content.classList.add('sidebar-open');
            topbar.classList.add('sidebar-open');
        }
    </script>

    @stack('scripts')
</body>
</html>
