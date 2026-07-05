@extends('layouts.app')

@section('title', 'Verify Your Email — Daiman Sports Complex')

@section('content')
<div class="form-container">
    <div class="form-card" style="max-width:520px; text-align:center;">

        <!-- Icon -->
        <div style="width:80px; height:80px; background:rgba(0,230,118,0.1); border:2px solid rgba(0,230,118,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 28px; font-size:32px; color:var(--primary);">
            <i class="fas fa-envelope"></i>
        </div>

        <h1 class="form-title" style="font-size:32px; margin-bottom:12px;">CHECK YOUR EMAIL</h1>
        <p style="color:var(--text-muted); font-size:15px; line-height:1.7; margin-bottom:28px;">
            We sent a verification link to
            <strong style="color:var(--light);">{{ auth()->user()->email }}</strong>.
            Click the link in the email to activate your account.
        </p>

        @if(session('success'))
        <div style="background:rgba(0,230,118,0.1); border:1px solid rgba(0,230,118,0.2); border-radius:var(--radius-sm); padding:14px; margin-bottom:20px; font-size:14px; color:var(--primary);">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
        @endif

        @if(session('resent'))
        <div style="background:rgba(0,230,118,0.1); border:1px solid rgba(0,230,118,0.2); border-radius:var(--radius-sm); padding:14px; margin-bottom:20px; font-size:14px; color:var(--primary);">
            <i class="fas fa-check-circle me-2"></i>A new verification link has been sent to your email!
        </div>
        @endif

        <!-- Steps -->
        <div style="background:var(--dark-4); border:1px solid var(--border); border-radius:var(--radius); padding:20px; margin-bottom:28px; text-align:left;">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:14px;">What to do next</div>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    <div style="width:24px; height:24px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:var(--font-display); font-size:13px; color:#000; flex-shrink:0;">1</div>
                    <div style="font-size:13px; color:#9ab89e; line-height:1.5;">Open your email inbox for <strong style="color:var(--light);">{{ auth()->user()->email }}</strong></div>
                </div>
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    <div style="width:24px; height:24px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:var(--font-display); font-size:13px; color:#000; flex-shrink:0;">2</div>
                    <div style="font-size:13px; color:#9ab89e; line-height:1.5;">Find the email from <strong style="color:var(--light);">Daiman Sports Complex</strong></div>
                </div>
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    <div style="width:24px; height:24px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:var(--font-display); font-size:13px; color:#000; flex-shrink:0;">3</div>
                    <div style="font-size:13px; color:#9ab89e; line-height:1.5;">Click the <strong style="color:var(--light);">"Verify Email Address"</strong> button in the email</div>
                </div>
            </div>
        </div>

        <!-- Resend Button -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-submit" style="margin-bottom:16px;">
                <i class="fas fa-paper-plane me-2"></i> Resend Verification Email
            </button>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-outline-sm w-100 justify-content-center" style="padding:12px; font-size:14px;">
                <i class="fas fa-sign-out-alt me-2"></i> Back to Login
            </button>
        </form>

        <p style="margin-top:20px; font-size:12px; color:var(--text-muted);">
            Didn't receive the email? Check your spam/junk folder.
        </p>
    </div>
</div>
@endsection
