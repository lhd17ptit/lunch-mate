<nav class="sidebar-nav scroll-sidebar" data-simplebar="">
    <ul id="sidebarnav">
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">MENU</span>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('admin.dashboard')}}" aria-expanded="false">
            <span>
                <i class="ti ti-layout-dashboard"></i>
            </span>
            <span class="hide-menu">Dashboard</span>
            </a>
        </li>
        @php
            $admin = auth()->guard('admin')->user();
        @endphp
        @if ($admin->role == 1) {{-- Super Admin --}}
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.floors.index')}}" aria-expanded="false">
                <span>
                    <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">Quản lý tầng</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.shops.index')}}" aria-expanded="false">
                <span>
                    <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">DS cửa hàng</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.food-categories.index')}}" aria-expanded="false">
                <span>
                    <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">DS danh mục đồ ăn</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.food-items.index')}}" aria-expanded="false">
                <span>
                    <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">DS đồ ăn</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.user-admins.index')}}" aria-expanded="false">
                <span>
                    <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">DS người quản lý</span>
                </a>
            </li>
        @endif
        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('admin.users.index')}}" aria-expanded="false">
            <span>
                <i class="ti ti-layout-dashboard"></i>
            </span>
            <span class="hide-menu">DS người dùng</span>
            </a>
        </li>
    </ul>
</nav>