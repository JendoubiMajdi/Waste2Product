<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('assets/img/white_bg.png') }}" alt="Logo">
    </div>

    <nav class="sidebar-nav">
        <ul class="list-unstyled">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mingcute:home-1-fill"></span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="clarity:user-line"></span>
                    <span>Users</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.products') }}" class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="lets-icons:bag-alt-light"></span>
                    <span>Products</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="ant-design:shopping-cart-outlined"></span>
                    <span>Product Orders</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.wastes') }}" class="nav-link {{ request()->routeIs('admin.wastes*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mdi:recycle"></span>
                    <span>Wastes</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.collection-points') }}" class="nav-link {{ request()->routeIs('admin.collection-points*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mdi:map-marker-multiple"></span>
                    <span>Collection Points</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.post-reports') }}" class="nav-link {{ request()->routeIs('admin.post-reports*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mdi:flag"></span>
                    <span>Post Reports</span>
                    @php
                        $pendingReports = \App\Models\PostReport::where('status', 'pending')->count();
                    @endphp
                    @if($pendingReports > 0)
                        <span class="badge bg-danger ms-2">{{ $pendingReports }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.forum.index') }}" class="nav-link {{ request()->routeIs('admin.forum*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mdi:chart-line"></span>
                    <span>Forum Analytics</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.challenges') }}" class="nav-link {{ request()->routeIs('admin.challenges*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mdi:trophy-outline"></span>
                    <span>Challenges</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.events-management') }}" class="nav-link {{ request()->routeIs('admin.events-management*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mdi:calendar-event"></span>
                    <span>Events</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.submissions') }}" class="nav-link {{ request()->routeIs('admin.submissions*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mdi:file-check-outline"></span>
                    <span>Submissions</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.donations.index') }}" class="nav-link {{ request()->routeIs('admin.donations*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mdi:hand-heart-outline"></span>
                    <span>Donation Approvals</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <span class="iconify" data-icon="mingcute:settings-3-line"></span>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
