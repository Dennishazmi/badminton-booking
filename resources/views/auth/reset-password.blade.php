@extends('layouts.app')

@section('title', 'Reset Password — Daiman Sports Complex')

@section('content')
<div class="form-container">
    <div class="form-card" style="max-width:460px;">

        <!-- Icon -->
        <div style="text-align:center; margin-bottom:28px;">
            <div style="width:72px; height:72px; background:rgba(0,230,118,0.1); border:2px solid rgba(0,230,118,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:28px; color:var(--primary);">
                <i class="fas fa-key"></i>
            </div>
            <h1 class="form-title" style="font-size:32px; margin-bottom:8px;">RESET PASSWORD</h1>
            <p style="color:var(--text-muted); font-size:14px;">Enter your new password below.</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="your@email.com"
                    value="{{ old('email', $email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <div style="position:relative;">
                    <input type="password" name="password" id="new-password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Min. 8 characters" required
                        style="padding-right:48px;">
                    <button type="button" onclick="togglePassword('new-password', 'eye-new')"
                        style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                               background:none; border:none; cursor:pointer; color:var(--text-muted);
                               padding:0; display:flex; align-items:center; justify-content:center;
                               width:20px; height:20px; transition:color 0.2s;">
                        <i class="fas fa-eye" id="eye-new" style="font-size:15px;"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" id="confirm-password"
                        class="form-control"
                        placeholder="Repeat new password" required
                        style="padding-right:48px;">
                    <button type="button" onclick="togglePassword('confirm-password', 'eye-confirm')"
                        style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                               background:none; border:none; cursor:pointer; color:var(--text-muted);
                               padding:0; display:flex; align-items:center; justify-content:center;
                               width:20px; height:20px; transition:color 0.2s;">
                        <i class="fas fa-eye" id="eye-confirm" style="font-size:15px;"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-check me-2"></i> Reset Password
            </button>
        </form>

        <div class="form-footer-link" style="margin-top:20px;">
            <a href="{{ route('login') }}" style="color:var(--primary); text-decoration:none;">
                <i class="fas fa-arrow-left me-1"></i> Back to Login
            </a>
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
