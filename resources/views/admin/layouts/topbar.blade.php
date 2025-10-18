<header class="admin-topbar" id="adminTopbar">
    <button class="topbar-toggle" id="sidebarToggle">
        <span class="iconify" data-icon="mingcute:menu-fill"></span>
    </button>

    <div class="topbar-search">
        <div style="position: relative;">
            <span class="iconify" data-icon="mingcute:search-line" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 20px; color: var(--text-secondary);"></span>
            <input type="text" placeholder="Search...">
        </div>
    </div>

    <div class="topbar-actions">
        <button class="topbar-icon-btn" data-bs-toggle="dropdown">
            <span class="iconify" data-icon="mingcute:notification-fill"></span>
            <span class="badge"></span>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0" style="width: 320px; border-radius: 12px;">
            <div class="p-3 border-bottom">
                <h6 class="mb-0">Notifications</h6>
            </div>
            <div style="max-height: 300px; overflow-y: auto;">
                <div class="p-3 border-bottom" style="cursor: pointer;">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <span class="iconify" data-icon="mingcute:check-circle-fill" style="font-size: 24px; color: var(--success-color);"></span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1" style="font-size: 13px;">New order received</p>
                            <small class="text-muted">2 minutes ago</small>
                        </div>
                    </div>
                </div>
                <div class="p-3 border-bottom" style="cursor: pointer;">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <span class="iconify" data-icon="mingcute:user-add-fill" style="font-size: 24px; color: var(--info-color);"></span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1" style="font-size: 13px;">New user registration</p>
                            <small class="text-muted">1 hour ago</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-2 text-center border-top">
                <a href="#" class="text-decoration-none" style="font-size: 13px;">View all notifications</a>
            </div>
        </div>

        <button class="topbar-icon-btn">
            <span class="iconify" data-icon="mingcute:settings-3-line"></span>
        </button>

        <div class="dropdown">
            <button class="topbar-icon-btn" data-bs-toggle="dropdown" style="width: auto; padding: 0 12px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; color: white; font-weight: 600; font-size: 14px;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span style="font-size: 14px; font-weight: 500;">{{ Auth::user()->name }}</span>
                    <span class="iconify" data-icon="mingcute:down-line"></span>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px; border-radius: 12px; padding: 8px;">
                <li>
                    <a class="dropdown-item rounded" href="{{ route('home') }}" style="padding: 10px 16px;">
                        <span class="iconify me-2" data-icon="mingcute:home-1-fill"></span>
                        Switch to Frontoffice
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item rounded" href="{{ route('admin.profile') }}" style="padding: 10px 16px;">
                        <span class="iconify me-2" data-icon="clarity:user-line"></span>
                        Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item rounded" href="{{ route('admin.settings') }}" style="padding: 10px 16px;">
                        <span class="iconify me-2" data-icon="mingcute:settings-3-line"></span>
                        Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item rounded text-danger" style="padding: 10px 16px;">
                            <span class="iconify me-2" data-icon="tabler:logout"></span>
                            Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
