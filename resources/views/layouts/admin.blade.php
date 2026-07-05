<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Daiman Sports Complex')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,700;1,300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ time() }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    @stack('styles')
</head>
<body>

<!-- Top Bar -->
<nav class="main-navbar" style="position:fixed; top:0; width:100%; z-index:200;">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center gap-3" style="width:100%;">
            <a href="{{ route('admin.dashboard') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                <div class="form-logo-icon" style="width:36px; height:36px; font-size:20px; background:#ffffff;">🏸</div>
                <div>
                    <div style="font-family:var(--font-display); font-size:18px; color:var(--primary); letter-spacing:2px; line-height:1;">DAIMAN</div>
                    <div style="font-size:9px; color:var(--text-muted); letter-spacing:1.5px; text-transform:uppercase;">Admin Panel</div>
                </div>
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="{{ route('home') }}" class="btn-outline-sm" style="font-size:12px; padding:6px 14px;">
                    <i class="fas fa-external-link-alt"></i> View Site
                </a>
                <div class="dropdown">
                    <button class="btn-outline-sm dropdown-toggle" data-bs-toggle="dropdown" style="font-size:12px; padding:6px 14px; background:none; border:1px solid var(--border); display:flex; align-items:center; gap:8px;">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=ffffff&color=000000&size=24" style="width:24px; height:24px; border-radius:50%;" alt="">
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
@if(session('success'))
<div class="alert-banner alert-banner-success" style="position:fixed; top:72px; left:0; right:0; z-index:150;">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close-banner" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
@endif
@if(session('error'))
<div class="alert-banner alert-banner-error" style="position:fixed; top:72px; left:0; right:0; z-index:150;">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close-banner" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
@endif

<!-- Sidebar -->
<nav class="admin-sidebar" style="top:72px;">
    <div class="sidebar-label">Main</div>
    <div class="sidebar-section">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.bookings') }}" class="sidebar-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> Bookings
            @if($pendingCount = \App\Models\Booking::where('status','pending')->count())
            <span class="badge-count">{{ $pendingCount }}</span>
            @endif
        </a>
    </div>
    <div class="sidebar-label">Management</div>
    <div class="sidebar-section">
        <a href="{{ route('admin.courts') }}" class="sidebar-link {{ request()->routeIs('admin.courts*') ? 'active' : '' }}">
            🏸 Courts
        </a>
        <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="{{ route('admin.payments') }}" class="sidebar-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
            <i class="fas fa-credit-card"></i> Payments
        </a>
    </div>
    <div class="sidebar-label">Reports</div>
    <div class="sidebar-section">
        <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        <a href="{{ route('admin.qr-scanner') }}" class="sidebar-link {{ request()->routeIs('admin.qr-scanner') ? 'active' : '' }}">
            <i class="fas fa-qrcode"></i> QR Scanner
        </a>
    </div>
</nav>

<!-- Main Content -->
<div class="admin-content" style="margin-top:72px; padding-top:40px;">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>