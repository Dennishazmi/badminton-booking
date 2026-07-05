@extends('layouts.app')

@section('title', 'Forgot Password — Daiman Sports Complex')

@section('content')
<div class="form-container">
    <div class="form-card" style="max-width:460px;">

        <!-- Icon -->
        <div style="text-align:center; margin-bottom:28px;">
            <div style="width:72px; height:72px; background:rgba(0,230,118,0.1); border:2px solid rgba(0,230,118,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:28px; color:var(--primary);">
                <i class="fas fa-lock"></i>
            </div>
            <h1 class="form-title" style="font-size:32px; margin-bottom:8px;">FORGOT PASSWORD</h1>
            <p style="color:var(--text-muted); font-size:14px; line-height:1.6;">
                Enter your email address and we'll send you a link to reset your password.
            </p>
        </div>

        @if(session('success'))
        <div style="background:rgba(0,230,118,0.1); border:1px solid rgba(0,230,118,0.2); border-radius:var(--radius-sm); padding:14px; margin-bottom:20px; font-size:14px; color:var(--primary); text-align:center;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="your@email.com"
                    value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane me-2"></i> Send Reset Link
            </button>
        </form>

        <div class="form-footer-link" style="margin-top:20px;">
            <a href="{{ route('login') }}" style="color:var(--primary); text-decoration:none;">
                <i class="fas fa-arrow-left me-1"></i> Back to Login
            </a>
        </div>
    </div>
</div>
@endsection
