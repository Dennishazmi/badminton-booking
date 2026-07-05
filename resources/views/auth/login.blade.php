@extends('layouts.app')

@section('title', 'Login — Daiman Sports Complex')

@section('content')
<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-logo">
                <div class="form-logo-icon"><i class="fas fa-shuttlecock"></i></div>
                <span class="form-logo-text">DAIMAN</span>
            </div>
            <h1 class="form-title">SIGN IN</h1>
            <p class="form-subtitle">Welcome back — let's get you on the court.</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                    Password
                    <a href="{{ route('password.request') }}" style="font-size:12px; color:var(--primary); text-decoration:none; text-transform:none; letter-spacing:0;">Forgot 				password?</a>
                </label>
                <div style="position:relative;">
                    <input type="password" name="password" id="login-password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter your password" required
                        style="padding-right: 48px;">
                    <button type="button" onclick="togglePassword('login-password', 'login-eye')"
                        style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                               background:none; border:none; cursor:pointer; color:var(--text-muted);
                               padding:0; display:flex; align-items:center; justify-content:center;
                               width:20px; height:20px; transition:color 0.2s;">
                        <i class="fas fa-eye" id="login-eye" style="font-size:15px;"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:16px; height:16px; accent-color:var(--primary);">
                    <span style="font-size:14px; color:var(--text-muted);">Remember me for 30 days</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-sign-in-alt me-2"></i> Sign In
            </button>
        </form>

        <div class="form-footer-link">
            Don't have an account? <a href="{{ route('register') }}">Create one free</a>
        </div>
             </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        icon.parentElement.style.color = 'var(--primary)';
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        icon.parentElement.style.color = 'var(--text-muted)';
    }
}
</script>
@endpush

@endsection