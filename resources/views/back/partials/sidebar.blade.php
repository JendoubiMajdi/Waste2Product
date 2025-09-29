<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <li class="nav-item nav-category">Management</li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-account"></i>
                <span class="menu-title">Users</span>
            </a>
        </li>
        <li><a href="{{ route('admin.dons.index') }}">Dons</a></li>
        <li><a href="{{ route('admin.forum.activity') }}">Forum Activity</a></li>
        <li><a href="{{ route('admin.forum.reports') }}">Reports</a></li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-recycle"></i>
                <span class="menu-title">Waste</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-truck"></i>
                <span class="menu-title">Livraisons</span>
            </a>
        </li>

        <li class="nav-item nav-category">Extra</li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-settings"></i>
                <span class="menu-title">Settings</span>
            </a>
        </li>
    </ul>
</nav>
