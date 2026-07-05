@extends('layouts.app')

@section('title', 'My Profile — Daiman Sports Complex')

@section('content')

<div class="profile-header">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-auto">
                <div class="profile-avatar-wrapper">
                    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=ffffff&color=000000&size=90&bold=true"
                        class="profile-avatar" alt="Profile">
                </div>
            </div>
            <div class="col">
                <div style="font-family:var(--font-display); font-size:32px; letter-spacing:1px; line-height:1;">{{ auth()->user()->name }}</div>
                <div style="color:var(--text-muted); font-size:14px; margin-top:6px;">
                    <i class="fas fa-envelope me-2" style="color:var(--primary);"></i>{{ auth()->user()->email }}
                </div>
                <div style="margin-top:10px;">
                    <span style="background:rgba(0,230,118,0.1); border:1px solid rgba(0,230,118,0.2); color:var(--primary); font-size:11px; font-weight:700; padding:4px 12px; border-radius:100px; text-transform:uppercase; letter-spacing:1px;">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                    <span style="background:rgba(255,255,255,0.06); color:var(--text-muted); font-size:11px; font-weight:600; padding:4px 12px; border-radius:100px; margin-left:8px;">
                        Member since {{ auth()->user()->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
            <div class="col-auto d-none d-md-block">
                <div class="d-flex gap-3">
                    <div style="text-align:center; padding:16px 24px; background:var(--dark-3); border:1px solid var(--border); border-radius:var(--radius);">
                        <div style="font-family:var(--font-display); font-size:28px; color:var(--primary);">{{ $totalBookings }}</div>
                        <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Total Bookings</div>
                    </div>
                    <div style="text-align:center; padding:16px 24px; background:var(--dark-3); border:1px solid var(--border); border-radius:var(--radius);">
                        <div style="font-family:var(--font-display); font-size:28px; color:var(--primary);">{{ $upcomingBookings }}</div>
                        <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Upcoming</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section section-dark">
    <div class="container">
        <div class="row g-4">

            <!-- Profile Form -->
            <div class="col-lg-7">
                <div class="booking-step">
                    <div class="step-header">
                        <div class="step-number"><i class="fas fa-user" style="font-size:14px;"></i></div>
                        <div class="step-title">Personal Information</div>
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST" id="profile-form">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', auth()->user()->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', auth()->user()->phone) }}" placeholder="+60 12-345 6789">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', auth()->user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn-primary-sm" style="padding:13px 28px; font-size:14px;">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="booking-step mt-4">
                    <div class="step-header">
                        <div class="step-number"><i class="fas fa-lock" style="font-size:14px;"></i></div>
                        <div class="step-title">Change Password</div>
                    </div>
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Enter current password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password"
                                        class="form-control @error('new_password') is-invalid @enderror"
                                        placeholder="Min. 8 characters">
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation"
                                        class="form-control" placeholder="Repeat new password">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn-outline-sm" style="padding:13px 28px; font-size:14px;">
                                <i class="fas fa-key"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar info -->
            <div class="col-lg-5">
                <!-- Recent Bookings -->
                <div class="booking-step">
                    <div class="step-header">
                        <div class="step-number"><i class="fas fa-history" style="font-size:14px;"></i></div>
                        <div class="step-title">Recent Bookings</div>
                    </div>
                    @if($recentBookings->isEmpty())
                        <div style="text-align:center; padding:32px 0; color:var(--text-muted); font-size:14px;">
                            <i class="fas fa-calendar-times" style="font-size:32px; margin-bottom:12px; display:block; opacity:0.4;"></i>
                            No bookings yet
                        </div>
                    @else
                        @foreach($recentBookings as $booking)
                        <div style="display:flex; align-items:center; gap:14px; padding:14px 0; border-bottom:1px solid var(--border);">
                            <div style="width:42px; height:42px; background:var(--dark-4); border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:18px; flex-shrink:0;">
                                🏸
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:600; font-size:14px;">{{ $booking->court->name }}</div>
                                <div style="font-size:12px; color:var(--text-muted);">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }} · {{ $booking->time_slot }}</div>
                            </div>
                            <span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                        </div>
                        @endforeach
                        <div class="mt-3">
                            <a href="{{ route('booking.history') }}" class="btn-outline-sm w-100 justify-content-center">
                                View All Bookings <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Danger Zone -->
                <div class="booking-step mt-4" style="border-color:rgba(255,61,87,0.2);">
                    <div style="font-size:13px; font-weight:700; color:var(--danger); text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">
                        <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                    </div>
                    <p style="color:var(--text-muted); font-size:13px; margin-bottom:16px;">Permanently delete your account and all associated booking data. This action cannot be undone.</p>
                    <button type="button" class="btn-danger-sm" onclick="confirm('Are you absolutely sure? This cannot be undone.') && document.getElementById('delete-form').submit()">
                        <i class="fas fa-trash"></i> Delete My Account
                    </button>
                    <form id="delete-form" action="{{ route('profile.destroy') }}" method="POST" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
