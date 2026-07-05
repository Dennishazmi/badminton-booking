@extends('layouts.app')

@section('title', 'Daiman Sports Complex — Smart Badminton Booking')

@section('content')

<!-- Hero -->
<section class="hero-section">
    <div class="hero-grid-overlay"></div>
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 hero-content">
                <div class="hero-tag">
                    <i class="fas fa-circle" style="font-size:8px;"></i>
                    Now Live — Smart Booking System
                </div>
                <h1 class="hero-title">
                    BOOK YOUR<br>
                    <span class="accent">COURT</span><br>
                    <span class="outline">ONLINE</span>
                </h1>
                <p class="hero-desc">
                    Reserve badminton courts at Daiman Sports Complex instantly. Real-time availability, QR-based entry, and seamless payments — all in one platform.
                </p>
                <div class="hero-actions">
                    @auth
                        <a href="{{ route('booking.index') }}" class="btn-hero-primary">
                            <i class="fas fa-calendar-plus"></i> Book Now
                        </a>
                        <a href="{{ route('booking.history') }}" class="btn-hero-secondary">
                            <i class="fas fa-list"></i> My Bookings
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-hero-primary">
                            <i class="fas fa-user-plus"></i> Get Started
                        </a>
                        <a href="{{ route('login') }}" class="btn-hero-secondary">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-6 hero-visual">
                <div class="court-illustration">
                    <div class="court-lines">
                        <div class="court-center-circle"></div>
                    </div>
                    <!-- Decorative elements -->
                    <div style="position:absolute; top:16px; right:16px; opacity:0.4;">
                        <span style="font-size:40px;">🏸</span>
                    </div>
                </div>
                <div class="floating-badge badge-1">
                    <span class="badge-icon"><i class="fas fa-qrcode"></i></span>
                    <div>
                        <div style="font-size:14px; font-weight:700;">QR Entry</div>
                        <div class="badge-label">Scan & Play</div>
                    </div>
                </div>
                <div class="floating-badge badge-2">
                    <span class="badge-icon"><i class="fas fa-bolt"></i></span>
                    <div>
                        <div style="font-size:14px; font-weight:700;">Instant Booking</div>
                        <div class="badge-label">Under 60 seconds</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="stats-bar">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">8+</div>
                    <div class="stat-label">Badminton Courts</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Monthly Players</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Uptime</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Online Booking</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="section section-dark">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-6">
                <div class="section-tag">Why Choose Us</div>
                <h2 class="section-title">SMART FEATURES FOR <span class="accent">SMART PLAYERS</span></h2>
                <p class="section-desc">Our platform eliminates double bookings, manual errors, and scheduling confusion — giving you more time to focus on the game.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card-dark">
                    <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="card-title">Real-Time Availability</div>
                    <div class="card-text">See which courts are available right now. No more phone calls or guesswork — just pick your slot and book instantly.</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-dark">
                    <div class="card-icon"><i class="fas fa-qrcode"></i></div>
                    <div class="card-title">QR-Based Entry</div>
                    <div class="card-text">Receive a unique QR code for every confirmed booking. Scan at the court entrance for instant, secure access.</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-dark">
                    <div class="card-icon"><i class="fas fa-credit-card"></i></div>
                    <div class="card-title">Online Payment</div>
                    <div class="card-text">Pay securely with credit cards, e-wallets, or PayPal. Receive instant booking confirmation, digital receipts and a unique QR code all in one 		                seamless transaction.</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-dark">
                    <div class="card-icon"><i class="fas fa-history"></i></div>
                    <div class="card-title">Booking History</div>
                    <div class="card-text">Track all your past and upcoming bookings. Cancel or view details with a single tap.</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-dark">
                    <div class="card-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="card-title">Secure Access Control</div>
                    <div class="card-text">Only authorized users with valid QR codes can access courts — preventing unauthorized use completely.</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card-dark">
                    <div class="card-icon"><i class="fas fa-mobile-alt"></i></div>
                    <div class="card-title">Mobile Friendly</div>
                    <div class="card-text">Book from any device — phone, tablet, or desktop. Responsive design built for players on the go.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Courts -->
@if(isset($courts) && $courts->count())
<section class="section section-darker">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-6">
                <div class="section-tag">Our Facilities</div>
                <h2 class="section-title">AVAILABLE <span class="accent">COURTS</span></h2>
                <p class="section-desc">Choose from our range of indoor and outdoor badminton courts — all maintained to professional standards.</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach($courts->take(4) as $court)
            <div class="col-md-6 col-lg-3">
                <div class="court-card">
                    <div class="court-card-image">
                        <span class="court-type-badge">{{ $court->type }}</span>
                        <div class="court-availability {{ $court->is_available ? '' : 'unavailable' }}"></div>
                        <span style="font-size:60px; opacity:0.4;">🏸</span>
                    </div>
                    <div class="court-card-body">
                        <div class="court-name">{{ $court->name }}</div>
                        <div class="court-price">RM {{ number_format($court->price_per_hour, 2) }} <span>/ hour</span></div>
                        <div class="mt-3">
                            @auth
                            <a href="{{ route('booking.index') }}?court={{ $court->id }}" class="btn-primary-sm w-100 justify-content-center">
                                <i class="fas fa-calendar-plus"></i> Book This Court
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="btn-outline-sm w-100 justify-content-center">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('booking.index') }}" class="btn-hero-primary">
                <i class="fas fa-th-large"></i> View All Courts & Book
            </a>
        </div>
    </div>
</section>
@endif

<!-- How It Works -->
<section class="section section-dark">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <div class="section-tag" style="justify-content:center;">How It Works</div>
                <h2 class="section-title">BOOK IN <span class="accent">3 STEPS</span></h2>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center" style="padding: 32px;">
                    <div style="width:72px; height:72px; background:rgba(0,230,118,0.1); border:2px solid rgba(0,230,118,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-family:var(--font-display); font-size:32px; color:var(--primary);">1</div>
                    <h5 style="font-weight:700; margin-bottom:10px;">Register & Login</h5>
                    <p style="color:var(--text-muted); font-size:14px; line-height:1.7;">Create a free account and log in to access the full booking platform.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center" style="padding: 32px;">
                    <div style="width:72px; height:72px; background:rgba(0,230,118,0.1); border:2px solid rgba(0,230,118,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-family:var(--font-display); font-size:32px; color:var(--primary);">2</div>
                    <h5 style="font-weight:700; margin-bottom:10px;">Choose & Pay</h5>
                    <p style="color:var(--text-muted); font-size:14px; line-height:1.7;">Select your court, date, and time. Pay securely with your preferred payment method.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center" style="padding: 32px;">
                    <div style="width:72px; height:72px; background:rgba(0,230,118,0.1); border:2px solid rgba(0,230,118,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-family:var(--font-display); font-size:32px; color:var(--primary);">3</div>
                    <h5 style="font-weight:700; margin-bottom:10px;">Scan & Play</h5>
                    <p style="color:var(--text-muted); font-size:14px; line-height:1.7;">Get your QR code, scan it at the court entrance, and enjoy your session!</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection