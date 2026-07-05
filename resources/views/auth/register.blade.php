@extends('layouts.app')

@section('title', 'Register — Daiman Sports Complex')

@section('content')
<div class="form-container" style="padding: 60px 20px;">
    <div class="form-card" style="max-width:520px;">
        <div class="form-header">
            <div class="form-logo">
                <div class="form-logo-icon"><i class="fas fa-shuttlecock"></i></div>
                <span class="form-logo-text">DAIMAN</span>
            </div>
            <h1 class="form-title">CREATE ACCOUNT</h1>
            <p class="form-subtitle">Join Daiman Sports Complex and start booking courts instantly.</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="Muhammad Dennish" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="your@email.com" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            placeholder="+60 12-345 6789" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="reg-password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Min. 8 characters" required
                                style="padding-right:48px;">
                            <button type="button" onclick="togglePassword('reg-password', 'reg-eye')"
                                style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                                       background:none; border:none; cursor:pointer; color:var(--text-muted);
                                       padding:0; display:flex; align-items:center; justify-content:center;
                                       width:20px; height:20px; transition:color 0.2s;">
                                <i class="fas fa-eye" id="reg-eye" style="font-size:15px;"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <div style="position:relative;">
                            <input type="password" name="password_confirmation" id="reg-password-confirm"
                                class="form-control"
                                placeholder="Repeat password" required
                                style="padding-right:48px;">
                            <button type="button" onclick="togglePassword('reg-password-confirm', 'reg-eye-confirm')"
                                style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                                       background:none; border:none; cursor:pointer; color:var(--text-muted);
                                       padding:0; display:flex; align-items:center; justify-content:center;
                                       width:20px; height:20px; transition:color 0.2s;">
                                <i class="fas fa-eye" id="reg-eye-confirm" style="font-size:15px;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; margin-bottom:20px;">
                        <input type="checkbox" name="terms" required style="width:16px; height:16px; accent-color:var(--primary); margin-top:2px; flex-shrink:0;">
                        <span style="font-size:13px; color:var(--text-muted); line-height:1.5;">
                            I agree to the <a href="#" style="color:var(--primary); text-decoration:none;">Terms of Service</a> and <a href="#" style="color:var(--primary); text-decoration:none;">Privacy Policy</a>
                        </span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-user-plus me-2"></i> Create Free Account
            </button>
        </form>

        <div class="form-footer-link">
            Already have an account? <a href="{{ route('login') }}">Sign in here</a>
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