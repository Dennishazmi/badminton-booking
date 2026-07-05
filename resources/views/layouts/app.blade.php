<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Daiman Sports Complex')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,700;1,300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ time() }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <span class="brand-icon" style="font-size:20px; background:#ffffff;">🏸</span>
            <span class="brand-text">DAIMAN</span>
            <span class="brand-sub">SPORTS COMPLEX</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('booking.index') }}">Book Court</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('booking.history') }}">My Bookings</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=ffffff&color=000000&size=32" class="avatar-sm" alt="Avatar">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                            @if(auth()->user()->role === 'admin')
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Admin Panel</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary-custom ms-2" href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
@if(session('success'))
<div class="alert-banner alert-banner-success">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close-banner" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
@endif
@if(session('error'))
<div class="alert-banner alert-banner-error">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close-banner" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
@endif

<!-- Main Content -->
<main class="main-content">
    @yield('content')
</main>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="footer-brand">DAIMAN</div>
                <p class="footer-tagline">Your premier sports facility in Johor Bahru. Book your court online, play with confidence.</p>
            </div>
            <div class="col-lg-2 col-6 mb-4">
                <h6 class="footer-heading">Quick Links</h6>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('booking.index') }}">Book Court</a></li>
                    @auth
                    <li><a href="{{ route('booking.history') }}">My Bookings</a></li>
                    @endauth
                </ul>
            </div>
            <div class="col-lg-3 col-6 mb-4">
                <h6 class="footer-heading">Operating Hours</h6>
                <ul class="footer-links">
                    <li>Mon–Fri: 8:00 AM – 1:00 AM</li>
                    <li>Sat–Sun: 8:00 AM – 2:00 AM</li>
                </ul>
            </div>
            <div class="col-lg-3 mb-4">
                <h6 class="footer-heading">Contact</h6>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt me-2"></i>No. 1, Jalan Emas Puteh 3, Taman Sri Skudai, 81300 Skudai, Johor, Malaysia.</li>
                    <li><i class="fab fa-whatsapp me-2"></i>+6011-33375144</li>
                    <li><i class="fas fa-envelope me-2"></i>dsssc@daimansports.com.my</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Daiman Sports Complex. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>